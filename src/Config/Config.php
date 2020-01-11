<?php

namespace Config;

use Framework\Box;

class Config extends \Framework\Config
{
    public function __construct()
    {
        // Return back if config is defined
        if (is_object(Box::$container) && Box::$container->has('\Config\Config') === true) {
            return;
        }

        // Load Framework Config
        parent::__construct();

        /*
         * Constants
         */
        defined('ROOT') or define('ROOT', realpath(dirname(__FILE__) . '/../../..') . DIRECTORY_SEPARATOR);
        defined('BASE_URL') or define('BASE_URL', $this->baseUrl);

        /*
         * Application configuration
         */
        $this->application = [
            'title'             => 'Monsoon',
            'url'               => BASE_URL,
            'layout'            => 'default',
            'timezone'          => 'Asia/Kolkata',
            'uploadMaxFilesize' => (100 * 1024 * 1024),

            'email'             => 'email@example.com',
            'language'          => 'en',
        ];

        /*
         * Environment
         */
        if (file_exists(__DIR__ . '/.env.php') === true) {
            $this->env = include __DIR__ . '/.env.php';
        }

        /*
         * Routes
         */
        if (file_exists(__DIR__ . '/.routes.php') === true) {
            $this->routes = include __DIR__ . '/.routes.php';
        }

    }
}
