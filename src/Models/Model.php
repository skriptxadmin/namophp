<?php 
namespace App\Models;


class Model{

    public $db;


    public function __construct(){

        $dbconn = new \App\Helpers\DB;

        $this->db = $dbconn->db;

    }
}