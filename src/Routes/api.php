<?php

use Slim\Routing\RouteCollectorProxy;
use App\Middlewares\Api\GuestMiddleware;
use App\Middlewares\Api\UserMiddleware;

$app->group('/api/v1/guest', function (RouteCollectorProxy $group) {

    $group->post('/login', [App\Controllers\Guest\LoginControllerApi::class, 'verify'])->setName('api.guest.verify');
    $group->post('/register', [App\Controllers\Guest\RegisterController::class, 'verify'])->setName('api.guest.register');
    $group->post('/set-password', [App\Controllers\Guest\SetPasswordController::class, 'verify'])->setName('api.guest.set-password');
    $group->post('/forgot-password', [App\Controllers\Guest\ForgotPasswordController::class, 'verify'])->setName('api.guest.forgot-password');

})->add(new GuestMiddleware);


$app->group('/api/v1/user', function (RouteCollectorProxy $group) {

    $group->get('/dashboard', [App\Controllers\User\DashboardController::class, 'get'])->setName('api.user.dashboard');
    $group->get('/me', [App\Controllers\User\MeController::class, 'index'])->setName('api.user.me');
    $group->post('/logout', [App\Controllers\User\LogoutController::class, 'index'])->setName('api.logout');

})->add(new UserMiddleware);

$app->group('/api/v1/roles', function (RouteCollectorProxy $group) {

    $group->get('', [App\Controllers\Roles\IndexController::class, 'list'])->setName('api.roles.index');

})->add(new UserMiddleware(['Administrator']));
