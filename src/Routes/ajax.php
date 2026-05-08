<?php

use App\Middlewares\Ajax\CsrfMiddleware;
use App\Middlewares\Ajax\GoogleRecaptchaMiddleware;
use App\Middlewares\Ajax\GuestMiddleware;
use App\Middlewares\Ajax\UserMiddleware;
use Slim\Routing\RouteCollectorProxy;

$app->group('/ajax/guest', function (RouteCollectorProxy $group) {

    $group->post('/login', [App\Controllers\Guest\LoginController::class, 'verify'])->setName('ajax.login');
    $group->post('/register', [App\Controllers\Guest\RegisterController::class, 'verify'])->setName('ajax.register');
    $group->post('/set-password', [App\Controllers\Guest\SetPasswordController::class, 'verify'])->setName('ajax.set-password');
    $group->post('/forgot-password', [App\Controllers\Guest\ForgotPasswordController::class, 'verify'])->setName('ajax.forgot-password');

})->add(new CsrfMiddleware)->add(new GoogleRecaptchaMiddleware)->add(new GuestMiddleware);

$app->group('/ajax/user', function (RouteCollectorProxy $group) {

    $group->get('/dashboard', [App\Controllers\User\DashboardController::class, 'get'])->setName('ajax.user.dashboard');
    $group->get('/me', [App\Controllers\User\MeController::class, 'index'])->setName('ajax.user.me');
    $group->post('/logout', [App\Controllers\User\LogoutController::class, 'index'])->setName('ajax.logout');

})->add(new CsrfMiddleware)->add(new UserMiddleware);

$app->group('/ajax/upload', function (RouteCollectorProxy $group) {

    $group->post('', [App\Controllers\UploadController::class, 'index'])->setName('upload');
    $group->post('/remove', [App\Controllers\UploadController::class, 'remove'])->setName('upload.remove');

})->add(new CsrfMiddleware)->add(new UserMiddleware);
