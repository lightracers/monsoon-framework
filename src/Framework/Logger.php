<?php

/**
 * Logger class - Custom errors
 */

namespace Framework;

/**
 * Record and email/display errors or a custom error message.
 */
class Logger
{

    /**
     * Determines if error should be displayed.
     *
     * @var boolean
     */
    public static $printError = true;

    /**
     * Clear the errorlog.
     *
     * @var boolean
     */
    private static $clear = false;

    /**
     * File path of log
     *
     * @var string
     */
    private $logFilePath = '';

    /**
     * Path to error file.
     */
    public static function getCurrentErrorLog()
    {
        return ROOT . '/data/logs/log-' . date('Y-m-d') . '.log';
    }

    /**
     * Path to error file.
     */
    public function setLogFile($logFilePath)
    {
        $this->logFilePath = $logFilePath;
    }

    /**
     * In the event of an error show this message.
     */
    public static function customErrorMsg()
    {
        echo "\n<p>An error occured, The error has been reported.</p>";
        exit();
    }

    /**
     * Saved the exception and calls customer error function.
     *
     * @param \Exception $e
     */
    public static function exceptionHandler($e)
    {
        self::newMessage($e);
        self::customErrorMsg();
    }

    /**
     * Saves error message from exception.
     *
     * @param int $number
     *            error number
     * @param string $message
     *            the error
     * @param string $file
     *            file originated from
     * @param int $line
     *            line number
     *
     * @return int
     */
    public static function errorHandler($number, $message, $file, $line)
    {
        $msg = "$message in $file on line $line";

        if (($number !== E_NOTICE) && ($number < 2048)) {
            self::errorMessage($msg);
            self::customErrorMsg();
        }

        return 0;
    }

    /**
     * New exception.
     *
     * @param \Exception $exception
     * @return void
     */
    public static function newMessage($exception)
    {
        $trace = $exception->getTraceAsString();
        if (defined('DATABASE')) {
            $trace = str_replace(DATABASE['PASSWORD'], ' ***** ', $trace);
        }

        $logMessage = '[' . date('Y-m-d H:i:s') . '] log.ERROR: '
            . $exception->getMessage() . ' in ' . $exception->getFile()
            . ' on line ' . $exception->getLine() . "\n" . $trace . "\n";

        $errorFile = self::getCurrentErrorLog();

        if (is_file($errorFile) === false) {
            file_put_contents($errorFile, '');
        }

        if (self::$clear) {
            $f = fopen($errorFile, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }

            $content = null;
        } else {
            $content = file_get_contents($errorFile);
        }

        file_put_contents($errorFile, $logMessage . $content);

        if (self::$printError == true) {
            echo $logMessage;
            exit();
        }
    }

    /**
     * Custom error.
     *
     * @param string $error
     */
    public static function errorMessage($error)
    {
        $date       = date('Y-m-d H:i:s');
        $logMessage = "[$date] log.ERROR: $error \n";

        $errorFile = self::getCurrentErrorLog();

        if (is_file($errorFile) === false) {
            file_put_contents($errorFile, '');
        }

        if (self::$clear) {
            $f = fopen($errorFile, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }

            $content = null;
        } else {
            $content = file_get_contents($errorFile);
            file_put_contents($errorFile, $logMessage . $content);
        }

        if (self::$printError == true) {
            echo $logMessage;
            exit();
        }
    }

    /**
     * @param $messageType
     * @param $message
     * @param array $context
     */
    public function writeMessage($messageType, $message, $context = [])
    {
        $logFile = $this->logFilePath != '' ? $this->logFilePath : self::getCurrentErrorLog();

        $logMessage = $messageType . ': ' . $message;
        if (!empty($context)) {
            $logMessage .= ' {Context: ' . serialize($context) . '}';
        }

        $logMessage .= "\n";

        if (is_file($logFile) === false) {
            file_put_contents($logFile, '');
        }

        if (self::$clear) {
            $f = fopen($logFile, "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }

            $content = null;
        } else {
            $f = fopen($logFile, "a+");
            if ($f !== false) {
                fputs($f, $logMessage);
                fclose($f);
            }
        }

    }

    /**
     * @param string $message
     * @param array $context
     */
    public function emergency($message, array $context = [])
    {
        $this->writeMessage('emergency', $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = [])
    {
        $this->writeMessage('alert', $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = [])
    {
        $this->writeMessage('critical', $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = [])
    {
        $this->writeMessage('error', $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = [])
    {
        $this->writeMessage('warning', $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = [])
    {
        $this->writeMessage('notice', $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = [])
    {
        $this->writeMessage('info', $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = [])
    {
        $this->writeMessage('debug', $message, $context);
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function log($level = 'message', $message, array $context = [])
    {
        $this->writeMessage($level, $message, $context);
    }

}
