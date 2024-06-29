<?php

define('ROOT_DIR', __DIR__ . '/../');

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;

$dotenv = \Dotenv\Dotenv::createImmutable(ROOT_DIR);

$dotenv->load();

$capsule = new Capsule;

$driver = $_ENV['DATABASE_DRIVER'];

if ($driver == 'sqlite') {

    $capsule->addConnection([
        'driver' => 'sqlite',
        'database' => ROOT_DIR . $_ENV['DATABASE_SQLITE'],
    ]);

}

if ($driver == 'mysql') {
    $capsule->addConnection([
        'driver' => 'mysql',
        'host' => $_ENV['DATABASE_MYSQL_HOST'],
        'database' => $_ENV['DATABASE_MYSQL_DB'],
        'username' => $_ENV['DATABASE_MYSQL_USERNAME'],
        'password' => $_ENV['DATABASE_MYSQL_PASSWORD'],
        'charset' => $_ENV['DATABASE_MYSQL_CHARSET'],
        'collation' => $_ENV['DATABASE_MYSQL_COLLATION'],
        'prefix' => $_ENV['DATABASE_MYSQL_PREFIX'],
    ]);

}

$capsule->setAsGlobal();

/**
 * Edit from here. Do not edit above codes
 *
 */

Capsule::schema()->drop('users'); //only if you want to delete the table

Capsule::schema()->create('users', function ($table) {
    $table->increments('id');
    $table->string('name')->unique();
    $table->timestamps();
});

Capsule::schema()->drop('roles'); //only if you want to delete the table

Capsule::schema()->create('roles', function ($table) {
    $table->increments('id');
    $table->string('name')->unique();
    $table->string('test')->unique();
    $table->timestamps();
});
