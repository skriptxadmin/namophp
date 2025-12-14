<?php
namespace App\Helpers;

use function _\kebabCase;
use Medoo\Medoo as MedooDB; // for columns to work

class DB
{

    public $db;

    public function __construct()
    {

        if(empty($_ENV['DB_DATABASE'])){

            return;
        }
        
        $this->db = new MedooDB([
            // [required]
            'type'     => 'mysql',
            'host'     => 'localhost',
            'database' => $_ENV['DB_DATABASE'],
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],

            // [optional]
            // 'charset' => 'utf8mb4',
            // 'collation' => 'utf8mb4_general_ci',
            'port'     => $_ENV['DB_PORT'],

            // [optional] The table prefix. All table names will be prefixed as PREFIX_table.
            'prefix'   => $_ENV['DB_PREFIX'],

            // [optional] To enable logging. It is disabled by default for better performance.
            'logging'  => $_ENV['DB_LOGGING'],

            // [optional]
            // Error mode
            // Error handling strategies when the error has occurred.
            // PDO::ERRMODE_SILENT (default) | PDO::ERRMODE_WARNING | PDO::ERRMODE_EXCEPTION
            // Read more from https://www.php.net/manual/en/pdo.error-handling.php.
            // 'error' => PDO::ERRMODE_SILENT,

            // [optional]
            // The driver_option for connection.
            // Read more from http://www.php.net/manual/en/pdo.setattribute.php.
            // 'option' => [
            //     PDO::ATTR_CASE => PDO::CASE_NATURAL
            // ],

            // [optional] Medoo will execute those commands after the database is connected.
            // 'command' => [
            //     'SET SQL_MODE=ANSI_QUOTES'
            // ]
        ]);
        $timezone = $_ENV['MYSQL_TIMEZONE'];
        $this->db->pdo->exec("SET time_zone = '$timezone'");
    }

  


    public function create_slug($table, $title)
    {
        $random = new \App\Helpers\Random;
        $slug   = null;
        $loop   = 1;
        $char   = 4;
        do {
            $slug = $random->slug($title, $char);

            $count = $this->db->count($table, ['slug' => $slug]);

            if ($count) {

                $loop++;
            }
            if ($loop > 99) {

                $loop = 1;
                $char = 5;
            }

        } while ($count);

        return $slug;
    }

}
