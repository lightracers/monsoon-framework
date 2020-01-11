<?php

namespace App\Controllers;

use Config\Config;
use Framework\Box;
use Framework\View;

class IndexController extends \Framework\Controller
{
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function indexAction()
    {
        $this->locale->load('Index', $this->config->getApplication()['language']);

        Box::$data['title']           = $this->locale->get('welcome_text');
        Box::$data['welcome_message'] = $this->locale->get('welcome_message');

        $this->view->renderDefault('index/index');
    }

    public function subpageAction()
    {
        $this->locale->load('Index', $this->config->getApplication()['language']);

        Box::$data['title']           = $this->locale->get('subpage_text');
        Box::$data['welcome_message'] = $this->locale->get('subpage_message');

        // load views separately
        $this->view->renderLayout('header');
        $this->view->render('index/subpage');
        $this->view->renderLayout('footer');

    }
}
