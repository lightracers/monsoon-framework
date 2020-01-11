<?php

/**
 * IndexController test case.
 */

namespace App\Tests;

use Framework\Box;
use Framework\Container;
use \PHPUnit\Framework\TestCase;
use Config\Config;

class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        defined('ROOT') or define('ROOT', realpath(dirname(__FILE__) . '/../../..') . DIRECTORY_SEPARATOR);
        defined('BASE_URL') or define('BASE_URL', '');

        if (empty($_SERVER["SERVER_NAME"])) {
            $_SERVER["SERVER_NAME"] = 'phpunit';
        }

        $this->configMock    = $this->createMock(Config::class);
        $this->containerMock = $this->createMock(Container::class);

        Box::$container = &$this->containerMock;

        parent::setUp();
    }

    public function __destruct()
    {

    }
}
