<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserRepository;
use App\Domain\User\UserNotFoundException;
use PDO;

class MysqlUserRepository implements UserRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM users");
        $rows = $stmt->fetchAll();

        return array_map(function ($row) {
            return new User(
                (int)$row['id'],
                $row['login'],
                $row['password'],
            );
        }, $rows);
    }

    public function findUserOfId(int $id): User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        if (!$row) {
            throw new UserNotFoundException();
        }

        return new User(
            (int)$row['id'],
            $row['login'],
            $row['password'],
        );
    }

    public function findByLogin(string $login): ?User
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM users WHERE login = :login
        ");

        $stmt->execute(['login' => $login]);

        $row = $stmt->fetch();

        return new User(
            (int)$row['id'],
            $row['login'],
            $row['password'],
        ) ?: null;
    }

    public function create(string $login, string $passwordHash): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (login, password)
            VALUES (:login, :password)
        ");

        $stmt->execute([
            'login' => $login,
            'password' => $passwordHash,
        ]);
    }
}