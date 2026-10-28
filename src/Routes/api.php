<?php

use App\Middlewares\Api\GuestMiddleware;
use App\Middlewares\Api\UserMiddleware;
use Slim\Routing\RouteCollectorProxy;

$app->group('/api', function (RouteCollectorProxy $group) {
 
$group->post('/upload/get', [App\Controllers\UploadControllerS3::class, 'get'])->setName('upload.get');

});


$app->group('/api/guest', function (RouteCollectorProxy $group) {

    $group->post('/login', [App\Controllers\Guest\LoginWebController::class, 'verify'])->setName('api.login');
    $group->post('/register', [App\Controllers\Guest\RegisterController::class, 'verify'])->setName('api.register');
    $group->post('/set-password', [App\Controllers\Guest\SetPasswordWebController::class, 'verify'])->setName('api.set-password');
    $group->post('/forgot-password', [App\Controllers\Guest\ForgotPasswordController::class, 'verify'])->setName('api.forgot-password');

})->add(new GuestMiddleware);

$app->group('/api/user', function (RouteCollectorProxy $group) {

    $group->get('/dashboard', [App\Controllers\User\DashboardController::class, 'get'])->setName('api.user.dashboard');
    $group->get('/me', [App\Controllers\User\MeController::class, 'index'])->setName('api.user.me');
    $group->post('/logout', [App\Controllers\User\LogoutController::class, 'index'])->setName('api.logout');

})->add(new UserMiddleware);

$app->group('/api/upload', function (RouteCollectorProxy $group) {

    $group->post('', [App\Controllers\UploadControllerS3::class, 'index'])
        ->setName('upload.save')->add(new UserMiddleware);
    $group->post('/remove', [App\Controllers\UploadControllerS3::class, 'remove'])
        ->setName('upload.remove')->add(new UserMiddleware);

})->add(new UserMiddleware);

