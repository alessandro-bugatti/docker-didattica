<?php

namespace Model;

use PDO;

final class UserRepository
{
    public function __construct(private PDO $pdo) {}

    public function findByUsername(string $username): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM utenti WHERE username = :username LIMIT 1');
        $statement->execute(['username' => $username]);
        return $statement->fetch() ?: null;
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM utenti WHERE id = :id');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }
}
