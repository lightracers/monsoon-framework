<?php

namespace App\Factories;

use Config\Config;
use App\Controllers\IndexController;
use Framework\Container;
use Framework\Interfaces\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
    public static function create(Container $container)
    {
        return new IndexController(
            $container->get(Config::class)
        );
    }
}
