<?php

use App\Middlewares\Web\GuestMiddleware;
use App\Middlewares\Web\UserMiddleware;
use Slim\Routing\RouteCollectorProxy;

$app->get('/', [App\Controllers\HomeController::class, 'index'])->setName('web.home');

$app->group('/guest', function (RouteCollectorProxy $group) {

    $group->get('/login-by-token', [App\Controllers\Guest\LoginByTokenController::class, 'index'])->setName('web.loginByToken');
    $group->get('/login', [App\Controllers\Guest\LoginController::class, 'index'])->setName('web.login');
    $group->get('/register', [App\Controllers\Guest\RegisterController::class, 'index'])->setName('web.register');
    $group->get('/set-password', [App\Controllers\Guest\SetPasswordController::class, 'index'])->setName('web.set-password');
    $group->get('/forgot-password', [App\Controllers\Guest\ForgotPasswordController::class, 'index'])->setName('web.forgot-password');

})->add(new GuestMiddleware);

$app->group('', function (RouteCollectorProxy $group) {

    $group->get('/dashboard', [App\Controllers\User\DashboardController::class, 'index'])->setName('web.dashboard');

})->add(new UserMiddleware);

$app->get('/404', [App\Controllers\ErrorController::class, 'web_not_found'])->setName('web.404');
$app->get('/500', [App\Controllers\ErrorController::class, 'web_app_error'])->setName('web.500');

$app->get('/terms', [App\Controllers\DocsController::class, 'terms'])->setName('docs.terms');
$app->get('/privacy', [App\Controllers\DocsController::class, 'privacy'])->setName('docs.privacy');
$app->get('/about', [App\Controllers\DocsController::class, 'about'])->setName('docs.about');
$app->get('/contact', [App\Controllers\DocsController::class, 'contact'])->setName('docs.contact');

// comment below lines on production use
$app->get('/migrate', [App\Database\Migration\IndexMigration::class, 'index'])->setName('migrate.index');
$app->get('/seed', [App\Database\Seeders\IndexSeeder::class, 'index'])->setName('seeder.index');
