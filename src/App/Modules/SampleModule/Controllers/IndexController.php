<?php

namespace App\Modules\SampleModule\Controllers;

use Framework\Box;
use Framework\View;

class IndexController extends \Framework\Controller
{
    public function indexAction()
    {
        Box::$data['title'] = 'Sub Module Page';
        View::renderDefault('SampleModule/views/index/index');
    }
}
