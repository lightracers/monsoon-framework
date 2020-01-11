<?php

namespace Framework;

class Config
{
    /** @var array */
    public $application = [];

    /** @var array */
    public $framework = [];

    /** @var array */
    public $routes = [];

    /** @var array */
    public $env = [];

    /** @var string */
    public $baseUrl = '';

    public function __construct()
    {
        // PATH
        $this->baseUrl = ((isset($_SERVER['HTTPS']) === true && $_SERVER['HTTPS'] === 'on' ||
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) === true && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'))
                ? 'https://' : 'http://') .
            $_SERVER["SERVER_NAME"] .
            (!empty($_SERVER['SERVER_PORT']) && !in_array($_SERVER['SERVER_PORT'], ['80', '443']) ? ':' . $_SERVER['SERVER_PORT'] : '') .
            str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);

        $this->setFramework(
            [
                'sessionsTableName' => 'sessions',
                'sessionPrefix'     => 'app_',

                // Default names
                'controller'        => 'Index',
                'service'           => 'Index',
                'action'            => 'index',
            ]
        );

    }


    /**
     * @param array $framework
     */
    public function setFramework(array $framework)
    {
        $this->framework = $framework;
    }

    /**
     * @return array
     */
    public function getFramework()
    {
        return $this->framework;
    }

    /**
     * @param array $application
     */
    public function setApplication(array $application)
    {
        $this->application = $application;
    }

    /**
     * @return array
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setEnv($name, $value)
    {
        $this->env[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getEnv($name)
    {
        if (isset($this->env[$name])) {
            return $this->env[$name];
        } else {
            return null;
        }
    }

    /**
     * @param $pattern
     * @param $callback
     */
    public function setRoute($pattern, $callback)
    {
        $this->routes[$pattern] = $callback;
    }

    /**
     * @param array $routes
     */
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

}
