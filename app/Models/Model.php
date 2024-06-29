<?php

namespace App\Models;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;

class Model extends \Illuminate\Database\Eloquent\Model
{

    public function __construct()
    {
        $capsule = new Capsule;

        $driver = $_ENV['DATABASE_DRIVER'];

        if($driver == 'sqlite'){

            $capsule->addConnection([
                'driver' => 'sqlite',
                'database' => ROOT_DIR.$_ENV['DATABASE_SQLITE'],
            ]);

        }

        if($driver == 'mysql'){
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

       
        
       

// Set the event dispatcher used by Eloquent models... (optional)

        $capsule->setEventDispatcher(new Dispatcher(new Container));

// Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $capsule->bootEloquent();
    }

}
