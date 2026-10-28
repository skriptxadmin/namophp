<?php

use Slim\Routing\RouteCollectorProxy;

$app->get('/sitemaps',[App\Controllers\Sitemaps\IndexController::class, 'index'])
        ->setName('sitemaps.index');
