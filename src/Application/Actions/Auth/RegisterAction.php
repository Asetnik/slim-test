<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RegisterAction
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function __invoke(
        Request $request,
        Response $response
    ): Response {
        $data = json_decode((string)$request->getBody(), true);

        $login = trim($data['login'] ?? '');
        $password = trim($data['password'] ?? '');

        if ($error = $this->validate($response, $login, $password)) {
            return $error;
        }

        $this->userRepository->create(
            $login,
            password_hash($password, PASSWORD_DEFAULT)
        );

        $payload = json_encode([
            'success' => true
        ]);

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function validate(Response $response, string $login, string $password): ?Response
    {
        if (!preg_match('/^[a-zA-Z0-9]{2,20}$/', $login)) {
            return $this->error($response, 'Логин должен состоять из латинских символов и цифр. Не менее 2 символов и не более 20');
        }

        if (strlen($password) < 5 || !preg_match('/\D/', $password)) {
            return $this->error($response, 'Пароль должен быть не менее 5 символов и в пароле должны быть символы, кроме цифр');
        }

        if ($this->userRepository->findByLogin($login)) {
            return $this->error($response, 'Пользователь уже существуне');
        }

        return null;
    }

    private function error(Response $response, string $message): Response
    {
        $payload = json_encode([
            'success' => false,
            'message' => $message
        ]);

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }
}