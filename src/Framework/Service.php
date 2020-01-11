<?php

/**
 * Service - base service
 */

namespace Framework;

abstract class Service
{
    /** @var boolean */
    public $isAuthenticated = false;

    /** @var Logger */
    public $logger;

    /** @var Response */
    public $response;

    public function __construct()
    {
        $this->logger = new Logger();
        $this->logger->setLogFile(ROOT . '/data/logs/api/api-log-' . date('Y-m-d') . '.log');

        $this->response = new Response;
        $apiAuth        = Box::getEnv('api')['auth'];

        if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && !empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authentication = base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6));
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', $authentication);
        }

        //authentication is mandatory for all requests
        $authUsername = preg_replace("/[^a-zA-Z0-9]+/", "", Security::escapeString($_SERVER['PHP_AUTH_USER']));
        $authPassword = preg_replace("/[^a-zA-Z0-9]+/", "", Security::escapeString($_SERVER['PHP_AUTH_PW']));

        if ($authUsername == '' || $authPassword == '') {
            $this->isAuthenticated = false;
        } else if ($authUsername != $apiAuth['username'] || $authPassword != $apiAuth['password']) {
            $this->isAuthenticated = false;
        } else {
            $this->isAuthenticated = true;
        }

        if ($this->isAuthenticated == false) {
            $this->logger->writeMessage(Security::getClientIP().' - - [' . date('c') . '] ERROR', 'Invalid Auth credentials');
        }
    }

    public function sendResponse()
    {
        // set status if not authenticated
        if ($this->isAuthenticated == false) {
            $this->response->setErrorMessage('INVALID_AUTH', 'Invalid API credentials');
            $this->response->setStatusCode('401');
            $this->response->setReasonPhrase('Unauthorized');
        }
        $this->logger->writeMessage(Security::getClientIP().' - - [' . date('c') . '] INFO', 'RESPONSE:', [
            $this->response->getStatusCode(),
            $this->response->getReasonPhrase(),
            [$this->response->getData(), $this->response->getErrors()],
        ]);

        $this->response->sendJsonResponse();
    }
}
