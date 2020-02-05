<?php

namespace Framework;

class Response
{
    // Successful responses
    const STATUS_200        = '200';
    const STATUS_200_PHRASE = 'OK';
    const STATUS_201        = '201';
    const STATUS_201_PHRASE = 'Created';
    const STATUS_202        = '202';
    const STATUS_202_PHRASE = 'Accepted';
    const STATUS_204        = '204';
    const STATUS_204_PHRASE = 'No Content';

    // Redirection responses
    const STATUS_301        = '301';
    const STATUS_301_PHRASE = 'Moved Permanently';
    const STATUS_302        = '302';
    const STATUS_302_PHRASE = 'Found';
    const STATUS_304        = '304';
    const STATUS_304_PHRASE = 'Not Modified';
    const STATUS_307        = '307';
    const STATUS_307_PHRASE = 'Temporary Redirect';
    const STATUS_308        = '308';
    const STATUS_308_PHRASE = 'Permanent Redirect';

    // Client error responses
    const STATUS_400        = '400';
    const STATUS_400_PHRASE = 'Bad Request';
    const STATUS_401        = '401';
    const STATUS_401_PHRASE = 'Unauthorized';
    const STATUS_403        = '403';
    const STATUS_403_PHRASE = 'Forbidden';
    const STATUS_404        = '404';
    const STATUS_404_PHRASE = 'Not Found';
    const STATUS_405        = '405';
    const STATUS_405_PHRASE = 'Method Not Allowed';
    const STATUS_406        = '406';
    const STATUS_406_PHRASE = 'Not Acceptable';
    const STATUS_408        = '408';
    const STATUS_408_PHRASE = 'Request Timeout';
    const STATUS_409        = '409';
    const STATUS_409_PHRASE = 'Conflict';
    const STATUS_415        = '415';
    const STATUS_415_PHRASE = 'Unsupported Media Type';
    const STATUS_422        = '422';
    const STATUS_422_PHRASE = 'Unprocessable Entity';

    // Server error responses
    const STATUS_500        = '500';
    const STATUS_500_PHRASE = 'Internal Server Error';
    const STATUS_501        = '501';
    const STATUS_501_PHRASE = 'Not Implemented';
    const STATUS_502        = '502';
    const STATUS_502_PHRASE = 'Bad Gateway';
    const STATUS_503        = '503';
    const STATUS_503_PHRASE = 'Service Unavailable';

    /** @var array */
    public $errorMessages;

    /** @var integer */
    public $statusCode = 200;

    /** @var string  */
    public $status = 'success';

    /** @var string */
    public $reasonPhrase = 'OK';

    /** @var string */
    public $contentType = '';

    /** @var array */
    public $data = [];

    /** @var string */
    public $protocolVersion = '1.1';

    /** @var array */
    public $headers = [];

    public function __construct()
    {
        $this->errorMessages = [];
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $reasonPhrase
     */
    public function setReasonPhrase(string $reasonPhrase)
    {
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * @return string
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * @param string $contentType
     */
    public function setContentType(string $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $protocolVersion
     */
    public function setProtocolVersion(string $protocolVersion)
    {
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * @return string
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return bool
     */
    public function hasHeader($header)
    {
        return array_key_exists($this->headers[$header]);
    }

    /**
     * @param $header
     * @param $value
     */
    public function setHeader($header, $value)
    {
        $this->headers[$header] = $value;
    }

    public function setErrorMessage($errorCode, $errorMessage)
    {
        $this->status          = 'error';
        $this->errorMessages[] = [
            'code'    => $errorCode,
            'message' => $errorMessage,
        ];
    }

    public function getErrors()
    {
        return $this->errorMessages;
    }

    public function sendJsonResponse()
    {
        if (empty($this->contentType) === true) {
            $this->setContentType('application/json');
        }

        if (empty($this->statusCode) === true) {
            $this->setHttpStatus();
        }

        $finalContent = [
            'status' => $this->status,
        ];

        if (is_array($this->errorMessages) === true && count($this->errorMessages) > 0) {
            $finalContent['errors'] = $this->errorMessages;
        } else {
            $finalContent['data'] = $this->data;
        }

        header('HTTP/1.1 ' . $this->statusCode . ' ' . $this->reasonPhrase . '');
        header('Content-Type: ' . $this->contentType);
        echo json_encode($finalContent);
        exit;
    }

    /**
     * Redirect to chosen url.
     *
     * @param string $url
     *            the url to redirect to
     * @param boolean $fullpath
     *            if true use only url in redirect instead of using DIR
     */
    public static function redirect($url = null, $fullpath = false)
    {
        if ($fullpath == false) {
            $url = Box::getConfig()->application['url'] . '/' . $url;
        }

        header('Location: ' . $url);
        exit();
    }

    /**
     * @param $code
     * @param $phrase
     */
    public function setHttpStatus($code = 200, $phrase = 'OK') {
        $this->setStatusCode($code);
        $this->setReasonPhrase($phrase);
    }

    public function return404()
    {
        header("HTTP/1.0 404 Not Found");
        exit;
    }

    public function return403()
    {
        header("HTTP/1.1 403 Forbidden");
        exit;
    }

    public function return500()
    {
        header("HTTP/1.1 500 Internal Server Error");
        exit;
    }

}
