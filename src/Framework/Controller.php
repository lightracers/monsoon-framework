<?php

/**
 * Controller - base controller
 */

namespace Framework;

abstract class Controller
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var View $view
     */
    public $view;

    /**
     * @var Locale
     */
    protected $locale;

    public function __construct()
    {
        // .. nothing
    }

    /**
     * @param Container $container
     */
    public function __invoke(Container &$container)
    {
        $this->invokeController($container);
    }

    public function invokeController(Container &$container)
    {
        $this->view   = $container->get('Framework\View');
        $this->locale = $container->get('Framework\Locale');
    }
}
