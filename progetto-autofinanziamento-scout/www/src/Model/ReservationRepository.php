<?php

namespace Model;

use Util\Connection;

final class ReservationRepository
{
    public static function forCustomer(int $customerId): array
    {
        $statement = Connection::getInstance()->prepare(
            'SELECT p.id, p.cliente_id, p.stato, p.created_at, p.annullata_at,
                    p.quantita, pr.nome AS prodotto_nome, pr.prezzo,
                    (p.quantita * pr.prezzo) AS totale_stimato
             FROM prenotazioni p
             JOIN prodotti pr ON pr.id = p.prodotto_id
             WHERE p.cliente_id = :cliente_id
             ORDER BY p.created_at DESC, p.id DESC'
        );
        $statement->execute(['cliente_id' => $customerId]);
        return $statement->fetchAll();
    }

    public static function totalForCustomer(int $customerId): float
    {
        $statement = Connection::getInstance()->prepare(
            "SELECT COALESCE(SUM(CASE WHEN p.stato <> 'annullato'
                                      THEN p.quantita * pr.prezzo ELSE 0 END), 0)
             FROM prenotazioni p
             JOIN prodotti pr ON pr.id = p.prodotto_id
             WHERE p.cliente_id = :cliente_id"
        );
        $statement->execute(['cliente_id' => $customerId]);
        return (float) $statement->fetchColumn();
    }

    public static function all(): array
    {
        return Connection::getInstance()->query(
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

    public static function customersWithReservations(): array
    {
        return Connection::getInstance()->query(
            "SELECT u.id, u.nome, u.username, COUNT(p.id) AS prenotazioni_count
             FROM utenti u
             JOIN prenotazioni p ON p.cliente_id = u.id
             WHERE u.ruolo = 'cliente'
             AND p.stato = 'in_attesa'
             GROUP BY u.id, u.nome, u.username
             ORDER BY SUBSTRING_INDEX(u.nome, ' ', -1), u.nome"
        )->fetchAll();
    }

    public static function forCustomerAsAdmin(int $customerId): array
    {
        $statement = Connection::getInstance()->prepare(
            "SELECT p.id, p.cliente_id, p.stato, p.created_at, p.annullata_at,
                    p.quantita, u.nome AS cliente_nome, u.username,
                    pr.nome AS prodotto_nome, pr.prezzo,
                    (p.quantita * pr.prezzo) AS totale_stimato
             FROM prenotazioni p
             JOIN utenti u ON u.id = p.cliente_id
             JOIN prodotti pr ON pr.id = p.prodotto_id
             WHERE p.cliente_id = :cliente_id
             ORDER BY p.created_at DESC, p.id DESC"
        );
        $statement->execute(['cliente_id' => $customerId]);
        return $statement->fetchAll();
    }

    public static function setDelivered(int $reservationId, string $status): bool
    {
        if (!in_array($status, ['in_attesa', 'consegnato'], true)) return false;
        $statement = Connection::getInstance()->prepare(
            "UPDATE prenotazioni SET stato = :stato
             WHERE id = :id AND stato IN ('in_attesa', 'consegnato')"
        );
        $statement->execute(['stato' => $status, 'id' => $reservationId]);
        return $statement->rowCount() > 0;
    }

    public static function globalStatus(): array
    {
        return Connection::getInstance()->query(
            "SELECT pr.id, pr.nome, pr.quantita AS quantita_rimasta,
                    SUM(CASE WHEN p.stato <> 'annullato' THEN p.quantita ELSE 0 END) AS quantita_prenotata,
                    pr.quantita + SUM(CASE WHEN p.stato <> 'annullato' THEN p.quantita ELSE 0 END) AS quantita_totale
             FROM prodotti pr
             JOIN prenotazioni p ON p.prodotto_id = pr.id
             GROUP BY pr.id, pr.nome, pr.quantita
             HAVING quantita_prenotata > 0
             ORDER BY pr.nome"
        )->fetchAll();
    }

    public static function productsWithPendingReservations(): array
    {
        return Connection::getInstance()->query(
            "SELECT pr.id, pr.nome, SUM(p.quantita) AS quantita_in_attesa,
                    COUNT(p.id) AS prenotazioni_count
             FROM prodotti pr
             JOIN prenotazioni p ON p.prodotto_id = pr.id
             WHERE p.stato = 'in_attesa'
             GROUP BY pr.id, pr.nome
             ORDER BY pr.nome"
        )->fetchAll();
    }

    public static function pendingForProduct(int $productId): array
    {
        $statement = Connection::getInstance()->prepare(
            "SELECT p.id, p.quantita, p.created_at, u.nome AS cliente_nome, u.username,
                    pr.nome AS prodotto_nome, pr.prezzo,
                    (p.quantita * pr.prezzo) AS totale_stimato
             FROM prenotazioni p
             JOIN utenti u ON u.id = p.cliente_id
             JOIN prodotti pr ON pr.id = p.prodotto_id
             WHERE p.prodotto_id = :prodotto_id AND p.stato = 'in_attesa'
             ORDER BY p.created_at, p.id"
        );
        $statement->execute(['prodotto_id' => $productId]);
        return $statement->fetchAll();
    }

    public static function deliveredReport(): array
    {
        return Connection::getInstance()->query(
            "SELECT pr.id, pr.nome,
                    COALESCE(SUM(CASE WHEN p.stato = 'consegnato' THEN p.quantita ELSE 0 END), 0) AS quantita_consegnata,
                    COALESCE(SUM(CASE WHEN p.stato = 'consegnato' THEN p.quantita * pr.prezzo ELSE 0 END), 0) AS ricavo_totale
             FROM prodotti pr
             LEFT JOIN prenotazioni p ON p.prodotto_id = pr.id
             GROUP BY pr.id, pr.nome
             ORDER BY pr.nome"
        )->fetchAll();
    }

    public static function deliveredTotals(): array
    {
        return Connection::getInstance()->query(
            "SELECT COALESCE(SUM(p.quantita), 0) AS quantita,
                    COALESCE(SUM(p.quantita * pr.prezzo), 0) AS ricavo
             FROM prenotazioni p
             JOIN prodotti pr ON pr.id = p.prodotto_id
             WHERE p.stato = 'consegnato'"
        )->fetch() ?: ['quantita' => 0, 'ricavo' => 0];
    }

    public static function create(int $customerId, int $productId, int $quantity): bool
    {
        $pdo = Connection::getInstance();
        $pdo->beginTransaction();
        try {
            $update = $pdo->prepare(
                'UPDATE prodotti 
                        SET quantita = quantita - :quantita WHERE id = :id AND quantita >= :quantita_ric'
            );
            $update->execute([
                'quantita' => $quantity,
                'id' => $productId,
                'quantita_ric' => $quantity
            ]);
            if ($update->rowCount() !== 1) {
                $pdo->rollBack();
                return false;
            }
            $reservation = $pdo->prepare(
                "INSERT INTO prenotazioni (cliente_id, prodotto_id, quantita, stato)
                 VALUES (:cliente_id, :prodotto_id, :quantita, 'in_attesa')"
            );
            $reservation->execute([
                'cliente_id' => $customerId,
                'prodotto_id' => $productId,
                'quantita' => $quantity,
            ]);
            $pdo->commit();
            return true;
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $exception;
        }
    }

    public static function cancel(int $reservationId, int $customerId): bool
    {
        $pdo = Connection::getInstance();
        $pdo->beginTransaction();
        try {
            $query = $pdo->prepare(
                "SELECT p.stato, p.prodotto_id, p.quantita
                 FROM prenotazioni p
                 WHERE p.id = :id AND p.cliente_id = :cliente_id"
            );
            $query->execute(['id' => $reservationId, 'cliente_id' => $customerId]);
            $reservation = $query->fetch();
            if (!$reservation || $reservation['stato'] !== 'in_attesa') {
                $pdo->rollBack();
                return false;
            }
            $stock = $pdo->prepare('UPDATE prodotti SET quantita = quantita + :quantita WHERE id = :id');
            $stock->execute(['quantita' => $reservation['quantita'], 'id' => $reservation['prodotto_id']]);
            $cancel = $pdo->prepare(
                "UPDATE prenotazioni SET stato = 'annullato', annullata_at = CURRENT_TIMESTAMP WHERE id = :id"
            );
            $cancel->execute(['id' => $reservationId]);
            $pdo->commit();
            return true;
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $exception;
        }
    }
}
