<?php

namespace Framework;

class Profiler
{
    /** @var array */
    public static $time = [];

    /** @var array */
    public static $memory = [];

    /** @var array */
    public static $events = [];

    /**
     * @return bool
     */
    public static function isStarted()
    {
        return isset(self::$time['start']);
    }

    /**
     * @param array $profiler
     */
    public static function start(array $profiler = [])
    {
        self::$time['start']   = ($profiler['time'] ?? microtime());
        self::$memory['start'] = ($profiler['memory'] ?? memory_get_usage());
    }

    /**
     * @return void
     */
    public static function stop()
    {
        self::$time['end']   = ($startTime ?? microtime());
        self::$memory['end'] = memory_get_usage();
    }

    /**
     * @param string $eventName
     */
    public static function startEvent(string $eventName)
    {
        self::$events[$eventName]['start'] = [
            'time'   => microtime(),
            'memory' => memory_get_usage(),
        ];
    }

    /**
     * @param string $eventName
     */
    public static function stopEvent(string $eventName)
    {
        self::$events[$eventName]['end'] = [
            'time'   => microtime(),
            'memory' => memory_get_usage(),
        ];
    }

    /**
     * @param string $eventName
     * @return array
     */
    public static function getEvent(string $eventName)
    {
        return [
            'time'   => (self::$events[$eventName]['end']['time'] - self::$events[$eventName]['start']['time']),
            'memory' => (self::$events[$eventName]['end']['memory'] - self::$events[$eventName]['start']['memory']),
        ];
    }

    /**
     * @return array
     */
    public static function getAllEvents()
    {
        $eventsArray = [];
        foreach (self::$events as $eventName) {
            $eventsArray[$eventName] = self::getEvent($eventName);
        }

        return $eventsArray;
    }

    /**
     * @return array
     */
    public static function getCurrentState()
    {
        return [
            'time'   => (microtime() - self::$time['start']),
            'memory' => (memory_get_usage() - self::$memory['start']),
        ];
    }
}
