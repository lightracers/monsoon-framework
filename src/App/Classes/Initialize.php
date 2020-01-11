<?php

namespace App\Classes;

use Framework\Box;
use Framework\Identity;

class Initialize
{

    public function __construct()
    {
        $config        = Box::getConfig();
        Box::$identity = new Identity();

        // ...
    }

}
