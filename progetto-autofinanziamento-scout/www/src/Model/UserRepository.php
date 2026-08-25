<?php

namespace Model;

use Util\Connection;

final class UserRepository
{
    public static function findByUsername(string $username): ?array
    {
        $statement = Connection::getInstance()->prepare('SELECT * FROM utenti WHERE username = :username LIMIT 1');
        $statement->execute(['username' => $username]);
        return $statement->fetch() ?: null;
    }

    public static function find(int $id): ?array
    {
        $statement = Connection::getInstance()->prepare('SELECT * FROM utenti WHERE id = :id');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }
}
