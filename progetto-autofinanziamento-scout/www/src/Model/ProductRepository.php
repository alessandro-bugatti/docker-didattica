<?php

namespace Model;

use PDO;

final class ProductRepository
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        return $this->pdo->query('SELECT * FROM prodotti ORDER BY created_at DESC, id DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM prodotti WHERE id = :id');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO prodotti (nome, descrizione, immagine, prezzo, quantita)
             VALUES (:nome, :descrizione, :immagine, :prezzo, :quantita)'
        );
        $statement->execute($data);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $statement = $this->pdo->prepare(
            'UPDATE prodotti SET nome = :nome, descrizione = :descrizione,
             immagine = :immagine, prezzo = :prezzo, quantita = :quantita WHERE id = :id'
        );
        $statement->execute($data);
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM prodotti WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
