<?php

use Slim\Routing\RouteCollectorProxy;
use App\Middlewares\Api\GuestMiddleware;
use App\Middlewares\Api\UserMiddleware;

$app->group('/guest', function (RouteCollectorProxy $group) {

    $group->post('/login', [App\Controllers\Guest\LoginController::class, 'index'])->setName('guest.login');
    $group->post('/register', [App\Controllers\Guest\RegisterController::class, 'index'])->setName('guest.register');
    $group->post('/set-password', [App\Controllers\Guest\SetPasswordController::class, 'index'])->setName('guest.set-password');
    $group->post('/forgot-password', [App\Controllers\Guest\ForgotPasswordController::class, 'index'])->setName('guest.forgot-password');

})->add(new GuestMiddleware);

$app->group('/user', function (RouteCollectorProxy $group) {

})->add(new UserMiddleware);