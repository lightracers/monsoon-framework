<?php

/**
 * Router - routing urls to closures and controllers -
 * modified from https://github.com/NoahBuscher/Macaw
 */

namespace Framework;

/**
 * Router class will load requested controller / closure based on url.
 */
class Router
{
    /**
     * Fallback for auto dispatching feature.
     *
     * @var boolean $fallback
     */
    public static $fallback = true;

    /**
     * If true - do not process other routes when match is found
     *
     * @var boolean $halts
     */
    public static $halts = true;

    /**
     * Array of routes
     *
     * @var array $routes
     */
    public static $routes = [];

    /**
     * Array of methods
     *
     * @var array $methods
     */
    public static $methods = [];

    /**
     * Array of callbacks
     *
     * @var array $callbacks
     */
    public static $callbacks = [];

    /**
     * Set an error callback
     *
     * @var null $errorCallback
     */
    public static $errorCallback;

    /**
     * Set route patterns
     *
     * @var array $patterns
     */
    public static $patterns = [
        ':any'    => '[^/]+',
        ':num'    => '-?[0-9]+',
        ':all'    => '.*',
        ':hex'    => '[[:xdigit:]]+',
        ':uuidV4' => '\w{8}-\w{4}-\w{4}-\w{4}-\w{12}',
    ];

    /** @var array  */
    public static $framework =[];

    /**
     * Defines a route with or without callback and method.
     *
     * @param string $method
     * @param
     *            array @params
     */
    public static function __callstatic($method, $params)
    {
        $uri      = dirname($_SERVER['PHP_SELF']) . '/' . $params[0];
        $callback = $params[1];

        array_push(self::$routes, $uri);
        array_push(self::$methods, strtoupper($method));
        array_push(self::$callbacks, $callback);
    }

    public static function setAutomaticRoutes($automaticRouting)
    {
        self::$fallback = $automaticRouting;
    }

    /**
     * Add routes in bulk as an array
     *
     * @param array $routesList
     */
    public static function addCustomRoutes($routesList = [])
    {
        foreach ($routesList as $route => $callback) {
            array_push(self::$routes, $route);
            array_push(self::$methods, 'ANY');
            array_push(self::$callbacks, $callback);
        }
    }

    /**
     * Defines callback if route is not found.
     *
     * @param string $callback
     */
    public static function error($callback)
    {
        self::$errorCallback = $callback;
    }

    /**
     * Don't load any further routes on match.
     *
     * @param boolean $flag
     */
    public static function haltOnMatch($flag = true)
    {
        self::$halts = $flag;
    }

    /**
     * Runs the callback for the given request.
     */
    public static function dispatch(Container &$container)
    {
        self::$framework = $container->get('Config')->framework;

        if ($_SERVER['REQUEST_URI'] == '') {
            return;
        }

        // get the custom routes
        self::addCustomRoutes($container->get('Config')->routes);

        $uri      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method   = $_SERVER['REQUEST_METHOD'];
        $searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);

        self::$routes = str_replace(['//', '/index.php/'], '/', self::$routes);
        $foundRoute   = false;

        // parse query parameters
        $query = '';
        if (strpos($uri, '&') > 0) {
            $query = substr($uri, (strpos($uri, '&') + 1));
            $uri   = substr($uri, 0, strpos($uri, '&'));
            $qArr  = explode('&', $query);
            foreach ($qArr as $q) {
                $qobj   = explode('=', $q);
                $qArr[] = [$qobj[0] => $qobj[1]];
                if (!isset($_GET[$qobj[0]])) {
                    $_GET[$qobj[0]] = $qobj[1];
                }
            }
        }

        // check if route is defined without regex
        if (in_array($uri, self::$routes)) {
            $routePos = array_keys(self::$routes, $uri);

            // foreach route position
            foreach ($routePos as $route) {
                if (self::$methods[$route] == $method || self::$methods[$route] == 'ANY') {
                    $foundRoute = true;
                    // if route is not an object
                    if (!is_object(self::$callbacks[$route])) {
                        // call object controller and method
                        self::invokeObject(self::$callbacks[$route], null, null, $container);
                        if (self::$halts) {
                            return;
                        }
                    } else {
                        // call closure
                        call_user_func(self::$callbacks[$route]);
                        if (self::$halts) {
                            return;
                        }
                    }
                }
            }
        } else {
            // check if defined with regex
            $pos = 0;

            // foreach routes
            foreach (self::$routes as $route) {
                $route = str_replace('//', '/', $route);

                if (strpos($route, ':') !== false) {
                    $route = str_replace($searches, $replaces, $route);
                }

                if (preg_match('#^' . $route . '$#', $uri, $matched)) {
                    if (self::$methods[$pos] == $method || self::$methods[$pos] == 'ANY') {
                        $foundRoute = true;

                        // remove $matched[0] as [1] is the first parameter.
                        array_shift($matched);

                        if (!is_object(self::$callbacks[$pos])) {
                            // call object controller and method
                            self::invokeObject(self::$callbacks[$pos], $matched, null, $container);
                            if (self::$halts) {
                                return;
                            }
                        } else {
                            // call closure
                            call_user_func_array(self::$callbacks[$pos], $matched, null);
                            if (self::$halts) {
                                return;
                            }
                        }
                    }
                }

                $pos++;
            }
        }

        if (self::$fallback) {
            // call the auto dispatch method
            $foundRoute = self::autoDispatch($container);
        }

        // run the error callback if the route was not found
        if (!$foundRoute) {
            if (!self::$errorCallback) {
                self::$errorCallback = function () {
                    header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");

                    Box::$data['title'] = '404';
                    Box::$data['error'] = "Oops! Page not found..";

                    View::renderLayout('header');
                    View::render('error/404');
                    View::renderLayout('footer');
                };
            }

            if (!is_object(self::$errorCallback)) {
                // call object controller and method
                self::invokeObject(self::$errorCallback, null, 'No routes found.', $container);
                if (self::$halts) {
                    return;
                }
            } else {
                call_user_func(self::$errorCallback);
                if (self::$halts) {
                    return;
                }
            }
        }
    }

    /**
     * Ability to call controllers in their controller/view/param way.
     */
    public static function autoDispatch(Container &$container)
    {
        $uri        = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $scriptPath = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);

        if ($scriptPath !== '/') {
            $uri = str_replace($scriptPath, '', $uri);
        }

        if (defined('BASE_URL') && strpos($uri, str_replace('index.php', '', $_SERVER['SCRIPT_NAME'])) === 0) {
            $uri = substr($uri, strlen(strpos($uri, BASE_URL)));
        }

        $uri   = trim($uri, ' /');
        $uri   = ($amp = strpos($uri, '&')) !== false ? substr($uri, 0, $amp) : $uri;
        $parts = explode('/', $uri);

        /*
         * Check api routes
         */
        if ($parts[0] == 'api') {
            // Log the request
            $logger = new Logger();
            $logger->setLogFile(ROOT . '/data/logs/api/api-log-' . date('Y-m-d') . '.log');
            $logger->writeMessage(Security::getClientIP().' - - [' . date('c') . '] INFO', '"' . $_SERVER['REQUEST_METHOD'] . ' ' . $uri . '"', file_get_contents('php://input'));

            $folderName = array_shift($parts);
            $service    = array_shift($parts);
            $service    = !empty($service) ? $service : self::$framework['service'];
            $service    = str_replace(' ', '_', ucwords(str_replace('-', ' ', $service)));

            if (file_exists(ROOT . "/src/Api/Services/{$service}Service.php")) {
                $service = "\Api\\Services\\{$service}Service";
            } else {
                $folderName = $service;
                $service    = array_shift($parts);
                $service    = !empty($service) ? $service : self::$framework['service'];
                $service    = str_replace(' ', '', ucwords(str_replace('-', ' ', $service)));

                // check whether there is any module with that name
                if (file_exists(ROOT . "/src/Api/Services/$folderName/{$service}Service.php")) {
                    $service = "Api\\Services\\$folderName\\{$service}Service";
                } else {
                    $routeNotFound = true;
                }
            }

            $requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
            $methodName    = array_shift($parts);
            $methodName    = str_replace(' ', '', ucwords(str_replace('-', ' ', $methodName)));
            $methodName    = $requestMethod . (!empty($methodName) ? $methodName : self::$framework['action']) . 'Action';

            $args = !empty($parts) ? $parts : [];
            $c    = new $service();
            if (method_exists($c, $methodName)) {
                call_user_func_array([$c, $methodName], $args);
                // found method so stop
                return true;
            } else {
                $httpMethodNotAllowed = true;
            }

            if ($routeNotFound || $httpMethodNotAllowed) {
                $response = new \Framework\Response();
                if ($routeNotFound) {
                    $response->setStatusCode('404');
                    $response->setReasonPhrase('Not Found');
                } else if ($httpMethodNotAllowed) {
                    $response->setStatus('error');
                    $response->setStatusCode('405');
                    $response->setReasonPhrase('Method Not Allowed');
                    $response->errorMessages = ['message' => 'Method Not Allowed'];
                }

                $response->sendJsonResponse();
            }
        } else {
            $controller = array_shift($parts);
            $controller = !empty($controller) ? $controller : self::$framework['controller'];

            $controller = str_replace(' ', '', ucwords(str_replace('-', ' ', $controller)));

            // Check for file in top Controllers folder
            if (file_exists(ROOT . "/src/App/Controllers/{$controller}Controller.php")) {
                $factory    = "App\\Factories\\{$controller}ControllerFactory";
                $controller = "App\\Controllers\\{$controller}Controller";
            } else {
                // check if there is any module with that name
                if (file_exists(ROOT . "/src/App/Modules/{$controller}")) {
                    $moduleName = $controller;

                    $moduleController = array_shift($parts);
                    $moduleController = !empty($moduleController) ? $moduleController : self::$framework['controller'];
                    $moduleController = str_replace(' ', '', ucwords(str_replace('-', ' ', $moduleController)));

                    if (file_exists(ROOT . "/src/App/Modules/$moduleName/Controllers/{$moduleController}Controller.php")) {
                        $factory    = "App\\Modules\\$moduleName\\Factories\\{$moduleController}ControllerFactory";
                        $controller = "App\\Modules\\$moduleName\\Controllers\\{$moduleController}Controller";
                    } else {
                        return false;
                    }
                } else {
                    // check in sub folder beneath Contollers folder
                    $subFolderName = $controller;

                    $controller = array_shift($parts);
                    $controller = !empty($controller) ? $controller : self::$framework['controller'];
                    $controller = str_replace(' ', '', ucwords(str_replace('-', ' ', $controller)));

                    if (file_exists(ROOT . "/src/App/Controllers/$subFolderName/{$controller}Controller.php")) {
                        $factory    = "App\\Factories\\$subFolderName\\{$controller}ControllerFactory";
                        $controller = "App\\Controllers\\$subFolderName\\{$controller}Controller";
                    } else {
                        return false;
                    }
                }
            }

            $method = array_shift($parts);
            $method = lcFirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $method))));
            $method = (!empty($method) ? $method : self::$framework['action']) . 'Action';
            $args   = !empty($parts) ? $parts : [];

            // invoke factory if exists
            if (class_exists($factory)) {
                $con = $factory::create($container);
            } else {
                $con = new $controller();
            }

            // Invoke with container
            $con($container);

            if (method_exists($con, $method)) {
                call_user_func_array([$con, $method], $args);
                // found method so stop
                return true;
            }
        }

        return false;
    }

    /**
     * Call object and instantiate.
     *
     * @param object $callback
     * @param array $matched
     *            array of matched parameters
     * @param string $msg
     */
    public static function invokeObject($callback, $matched = null, $msg = null, Container $container = null)
    {
        $last = explode('/', $callback);
        $last = end($last);

        $segments = explode('@', $last);

        $controller = $segments[0];
        $method     = $segments[1];

        $factory = str_replace('Controllers', 'Factories', $controller).'Factory';

        if (class_exists($factory)) {
            $controller = $factory::create($container);
        } else {
            $controller = new $controller($msg);
        }

        // Inject container
        $controller($container);

        call_user_func_array([$controller, $method], $matched ? $matched : []);
    }

    public static function autoloadRegister($name)
    {
        $name = str_replace('\\', DIRECTORY_SEPARATOR, $name);
        if (file_exists('../' . $name . '.php')) {
            include_once('../' . $name . '.php');
        }
    }

}

spl_autoload_register(__NAMESPACE__ . '\Router::autoloadRegister');
