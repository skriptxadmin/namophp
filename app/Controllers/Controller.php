<?php

namespace App\Controllers;

use Smarty\Smarty;

class Controller
{

    protected $smarty;

    public function __construct()
    {

        $this->smarty = new Smarty();

        $this->smarty->setTemplateDir(ROOT_DIR.'app/Views/');

        $this->smarty->assign('site_url', $_ENV['SITE_URL']);

        $this->smarty->assign('site_title', $_ENV['SITE_TITLE']);

        $this->smarty->assign('app_version', $_ENV['APP_VERSION']);

        $this->smarty->assign('csrf_token', csrf_token());

        $this->smarty->assign('meta_title', $_ENV['META_TITLE']);

        $this->smarty->assign('meta_description', $_ENV['META_DESCRIPTION']);

        $actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $this->smarty->assign('meta_url', $actual_link);

        $this->smarty->assign('meta_image', $_ENV['META_IMAGE']);

        $this->smarty->registerFilter("output", [$this, "minify_html"]);


    }

    function minify_html($tpl_output,  $template) {
        $tpl_output = preg_replace('![\t ]*[\r\n]+[\t ]*!', '', $tpl_output);
        return $tpl_output;
    }

    public function view($view, $args = null)
    {

        if (!empty($args) && gettype($args) == 'array') {

            foreach($args as $key => $value){

                $this->smarty->assign($key, $value);
            }
        }

        $this->smarty->display($view.'.html');
    }

    public function json($data, $error_code = 200){

        header('Content-Type: application/json; charset=utf-8');
        
        http_response_code($error_code);

        echo json_encode($data);

        return;
    }

    public function redirect($path){

        header('Location: '.$_ENV['SITE_URL'].'/'.$path);
    }
}
