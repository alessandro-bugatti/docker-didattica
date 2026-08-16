<?php

namespace Model;

use PDO;

final class ReservationRepository
{
    public function __construct(private PDO $pdo) {}

    public function forCustomer(int $customerId): array
    {
        $statement = $this->pdo->prepare(
            "SELECT p.id, p.stato, p.created_at, p.annullata_at,
                    p.quantita, pr.nome AS prodotto_nome, pr.prezzo,
                    (p.quantita * pr.prezzo) AS totale_stimato
             FROM prenotazioni p
             JOIN prodotti pr ON pr.id = p.prodotto_id
             WHERE p.cliente_id = :cliente_id
             ORDER BY p.created_at DESC, p.id DESC"
        );
        $statement->execute(['cliente_id' => $customerId]);
        return $statement->fetchAll();
    }

    public function totalForCustomer(int $customerId): float
    {
        $statement = $this->pdo->prepare(
            "SELECT COALESCE(SUM(CASE WHEN p.stato <> 'annullato'
                                      THEN p.quantita * pr.prezzo ELSE 0 END), 0)
             FROM prenotazioni p
             JOIN prodotti pr ON pr.id = p.prodotto_id
             WHERE p.cliente_id = :cliente_id"
        );
        $statement->execute(['cliente_id' => $customerId]);
        return (float) $statement->fetchColumn();
    }

    public function all(): array
    {
        return $this->pdo->query(
            "SELECT p.id, p.stato, p.created_at, p.annullata_at,
                    u.nome AS cliente_nome, u.username, p.quantita,
                    pr.nome AS prodotto_nome, pr.prezzo,
                    (p.quantita * pr.prezzo) AS totale_stimato
             FROM prenotazioni p
             JOIN utenti u ON u.id = p.cliente_id
             JOIN prodotti pr ON pr.id = p.prodotto_id
             ORDER BY p.created_at DESC, p.id DESC"
        )->fetchAll();
    }

    public function create(int $customerId, int $productId, int $quantity): bool
    {
        $this->pdo->beginTransaction();
        try {
            $product = $this->pdo->prepare('SELECT quantita FROM prodotti WHERE id = :id');
            $product->execute(['id' => $productId]);
            $available = $product->fetchColumn();
            if ($available === false || (int) $available < $quantity) {
                $this->pdo->rollBack();
                return false;
            }

            $update = $this->pdo->prepare(
                'UPDATE prodotti SET quantita = quantita - :quantita WHERE id = :id AND quantita >= :quantita'
            );
            $update->execute(['quantita' => $quantity, 'id' => $productId]);
            if ($update->rowCount() !== 1) {
                $this->pdo->rollBack();
                return false;
            }

            $reservation = $this->pdo->prepare(
                "INSERT INTO prenotazioni (cliente_id, prodotto_id, quantita, stato)
                 VALUES (:cliente_id, :prodotto_id, :quantita, 'in_attesa')"
            );
            $reservation->execute([
                'cliente_id' => $customerId,
                'prodotto_id' => $productId,
                'quantita' => $quantity,
            ]);
            $this->pdo->commit();
            return true;
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function cancel(int $reservationId, int $customerId): bool
    {
        $this->pdo->beginTransaction();
        try {
            $query = $this->pdo->prepare(
                "SELECT p.stato, p.prodotto_id, p.quantita
                 FROM prenotazioni p
                 WHERE p.id = :id AND p.cliente_id = :cliente_id"
            );
            $query->execute(['id' => $reservationId, 'cliente_id' => $customerId]);
            $reservation = $query->fetch();
            if (!$reservation || $reservation['stato'] !== 'in_attesa') {
                $this->pdo->rollBack();
                return false;
            }
            $stock = $this->pdo->prepare('UPDATE prodotti SET quantita = quantita + :quantita WHERE id = :id');
            $stock->execute(['quantita' => $reservation['quantita'], 'id' => $reservation['prodotto_id']]);
            $cancel = $this->pdo->prepare(
                "UPDATE prenotazioni SET stato = 'annullato', annullata_at = CURRENT_TIMESTAMP WHERE id = :id"
            );
            $cancel->execute(['id' => $reservationId]);
            $this->pdo->commit();
            return true;
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $exception;
        }
    }
}
