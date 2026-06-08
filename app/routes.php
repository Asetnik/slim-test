<?php

declare(strict_types=1);

use App\Application\Actions\Auth\LoginAction;
use App\Application\Actions\Auth\LoginViewAction;
use App\Application\Actions\Auth\LogoutAction;
use App\Application\Actions\Auth\ProfileViewAction;
use App\Application\Actions\Auth\RegisterAction;
use App\Application\Actions\Auth\RegisterViewAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->group('/register', function (Group $group) {
        $group->get('', RegisterViewAction::class);
        $group->post('', RegisterAction::class);
    });

    $app->group('/login', function (Group $group) {
        $group->get('', LoginViewAction::class);
        $group->post('', LoginAction::class);
    });

    $app->get('/profile', ProfileViewAction::class);
    $app->get('/logout', LogoutAction::class);
};
