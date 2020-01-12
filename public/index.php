<?php

ini_set('display_errors', 1);

/*
 * Basic constants
 */
define('PROFILER_SNAPSHOT', ['time' => microtime(), 'memory' => memory_get_usage()]);

/*
 * Autoloader
 */
if (file_exists(__DIR__ . '/../vendor/autoload.php') === true) {
    include __DIR__ . '/../vendor/autoload.php';
} else {
    spl_autoload_register(
        function ($class) {
        $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
        }
    );
}

/*
 * Start
 */
(new Framework\Application())->start();

// Handy function
function _x($data, $exit = true, $usePreTag = true)
{
    echo $usePreTag == true ? '<pre>' : '';

    print_r($data);

    echo $usePreTag == true ? '</pre>' : '';

    if ($exit) {
        exit();
    }
}
