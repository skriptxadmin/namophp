<?php

use Slim\Routing\RouteCollectorProxy;

$app->group('/cronjobs', function (RouteCollectorProxy $group) {

    $group->get('/reports', [App\Controllers\CronJobs\IndexController::class, 'index'])
        ->setName('cronjob.reports');

  
});
