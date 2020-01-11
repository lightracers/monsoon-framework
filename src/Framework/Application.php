<?php

namespace Framework;

class Application
{
    public function start(Container $container = null)
    {
        defined('ROOT') or define('ROOT', realpath(__DIR__ . '/../../'));

        //Initiate Dependency Injection Container
        if ($container == null) {
            $container = new Container();
        }

        // Proceed only if Config.php exists
        if (!file_exists(ROOT . '/src/Config/Config.php')) {
            exit('Error: Could not find ' . ROOT . '/src/Config/Config.php');
        }

        // Reference container in the box
        Box::$container = &$container;

        // load Config
        Box::$container->setAlias(\Config\Config::class, 'Config');
        $config = Box::$container->get('Config');

        // Start Profiler
        if (defined('PROFILER_SNAPSHOT') && isset($config->env['profiler']) && $config->env['profiler'] == true) {
            Profiler::start(PROFILER_SNAPSHOT);
        }

        // Set error_reporting
        error_reporting(($config->env['errorReporting'] ?? E_ALL));
        set_exception_handler('Framework\Logger::ExceptionHandler');
        set_error_handler('Framework\Logger::ErrorHandler');

        /*
         * Application specific initializations
         */
        if (class_exists('\App\Classes\Initialize')) {
            new \App\Classes\Initialize();
        }

        if (!defined('DISABLE_AUTO_ROUTING')) {
            // Turn on output buffering
            ob_start();

            // Find the route
            Router::dispatch($container);

            // Flush buffer
            ob_flush();
        }

        // Stop Profiler
        if (Profiler::isStarted()) {
            Profiler::stop();
        }
    }
}
