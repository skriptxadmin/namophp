<?php

define('ROOT_DIR', __DIR__.'/../');


require ROOT_DIR.'vendor/autoload.php';

use Josantonius\Session\Session;

use App\Helpers\Logger;

$dotenv = \Dotenv\Dotenv::createImmutable(ROOT_DIR);

$dotenv->load();

date_default_timezone_set($_ENV['TIMEZONE']);

$session = new Session();

$session->start();

$logger = new Logger();

$actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$logger->info($actual_link, ['routing'], ['public/index']);

$router = new \Bramus\Router\Router();

require ROOT_DIR.'routes/web.php';

$router->set404('\App\Controllers\PageNotFoundController@index');

$router->run();