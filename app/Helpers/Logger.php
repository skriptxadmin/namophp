<?php

namespace App\Helpers;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;

class Logger
{

    public $logger;

    public function __construct()
    {

        $this->logger = new MonologLogger('namophp');

        $log_file_name = date('Y-m-d-H');

        $this->logger->pushHandler(new StreamHandler(ROOT_DIR . 'logs/' . $log_file_name . '.log'));

    }

    public function debug($content, $args = array()){

        $this->logger->debug($content, $args);
    }

    public function info($content, $args = array()){

        $this->logger->info($content, $args);
    }

    public function notice($content, $args = array()){

        $this->logger->notice($content, $args);
    }


    public function warning($content, $args = array()){

        $this->logger->warning($content, $args);
    }

    public function error($content, $args = array()){

        $this->logger->error($content, $args);
    }

    public function critical($content, $args = array()){

        $this->logger->critical($content, $args);
    }

    public function alert($content, $args = array()){

        $this->logger->alert($content, $args);
    }

    public function emergency($content, $args = array()){

        $this->logger->emergency($content, $args);
    }

}
