<?php

declare(strict_types=1);

namespace App\Domain\User;

use JsonSerializable;

class User implements JsonSerializable
{
    private ?int $id;

    private string $login;
    private string $passwordHash;

    public function __construct(?int $id, string $login, string $passwordHash)
    {
        $this->id = $id;
        $this->login = strtolower($login);
        $this->passwordHash = $passwordHash;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
        ];
    }
}
