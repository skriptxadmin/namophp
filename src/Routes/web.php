<?php

use Slim\Routing\RouteCollectorProxy;
use App\Middlewares\Web\GuestMiddleware;
use App\Middlewares\Web\UserMiddleware;


$app->get('/', [App\Controllers\HomeController::class, 'index'])->setName('web.home');
$app->get('/login', [App\Controllers\Guest\IndexController::class, 'index'])
->setName('web.login')->add(new GuestMiddleware);

$app->get('/404', [App\Controllers\ErrorController::class, 'web_not_found'])->setName('web.404');
$app->get('/500', [App\Controllers\ErrorController::class, 'web_app_error'])->setName('web.500');

$app->get('/terms', [App\Controllers\DocsController::class, 'terms'])->setName('docs.terms');
$app->get('/privacy', [App\Controllers\DocsController::class, 'privacy'])->setName('docs.privacy');
$app->get('/about', [App\Controllers\DocsController::class, 'about'])->setName('docs.about');
$app->get('/contact', [App\Controllers\DocsController::class, 'contact'])->setName('docs.contact');

// comment below lines on production use
$app->get('/migrate', [App\Database\Migration\IndexMigration::class, 'index'])->setName('migrate.index');
$app->get('/seed', [App\Database\Seeders\IndexSeeder::class, 'index'])->setName('seeder.index');