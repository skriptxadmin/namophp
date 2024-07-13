<?php

define ('ROOT_DIR', __DIR__.'/../../');

require __DIR__.'/../../vendor/autoload.php';


$dotenv = \Dotenv\Dotenv::createImmutable(ROOT_DIR);

$dotenv->load();

require(__DIR__.'/./1-roles.php');

require(__DIR__.'/./2-users.php');