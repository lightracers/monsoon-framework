<?php

namespace Framework\Interfaces;

use Framework\Container;

interface FactoryInterface
{
    public static function create(Container $container);
}
