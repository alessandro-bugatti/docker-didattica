<?php

namespace Model;

use Util\Connection;

final class ProductRepository
{
    public static function all(): array
    {
        return Connection::getInstance()->query('SELECT * FROM prodotti ORDER BY created_at DESC, id DESC')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $statement = Connection::getInstance()->prepare('SELECT * FROM prodotti WHERE id = :id');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Connection::getInstance();
        $statement = $pdo->prepare(
            'INSERT INTO prodotti (nome, descrizione, immagine, prezzo, quantita)
             VALUES (:nome, :descrizione, :immagine, :prezzo, :quantita)'
        );
        $statement->execute($data);
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $statement = Connection::getInstance()->prepare(
            'UPDATE prodotti SET nome = :nome, descrizione = :descrizione,
             immagine = :immagine, prezzo = :prezzo, quantita = :quantita WHERE id = :id'
        );
        $statement->execute($data);
    }

    public static function delete(int $id): void
    {
        $statement = Connection::getInstance()->prepare('DELETE FROM prodotti WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
