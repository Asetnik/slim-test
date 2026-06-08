<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginAction
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        session_start();

        $data = json_decode((string)$request->getBody(), true);

        $login = trim($data['login'] ?? '');
        $password = trim($data['password'] ?? '');

        if ($error = $this->validate($response, $login, $password)) {
            return $error;
        }

        $user = $this->userRepository->findByLogin($login);

        if (!$user) {
            return $this->error($response, 'Пользователь не найден');
        }

        if (!password_verify($password, $user['password'])) {
            return $this->error($response, 'Неверный пароль');
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'login' => $user['login']
        ];

        $response->getBody()->write(json_encode([
            'success' => true
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function validate(Response $response, string $login, string $password): ?Response
    {
        if (!$login || !$password) {
            return $this->error($response, 'Логин и пароль не должны быть пустыми');
        }

        return null;
    }

    private function error(Response $response, string $message): Response
    {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => $message
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}