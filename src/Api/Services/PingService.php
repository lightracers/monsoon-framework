<?php
namespace Api\Services;

class PingService extends \Framework\Service
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getIndexAction()
    {
        $data = [
            'ping' => 'ok',
            'time' => date('Y-m-d H:i:s'),
        ];

        $this->response->setData($data);
        $this->sendResponse();
    }
}
