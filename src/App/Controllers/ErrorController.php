<?php

namespace App\Controllers;

use Framework\View;

class ErrorController extends \Framework\Error
{
    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
        View::renderDefault('error/500', []);
    }
}
