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

        $this->smarty->assign('app_version', $_ENV['APP_VERSION']);

        $this->smarty->assign('framework_name', 'NAMO PHP');

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
}
