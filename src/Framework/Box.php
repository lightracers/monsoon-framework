<?php

namespace Framework;

class Box
{
    /** @var Container */
    public static $container;

    /** @var array */
    public static $config = [];

    /** @var array */
    public static $data = [];

    /** @var Identity */
    public static $identity;

    /**
     * @param Container $container
     */
    public static function setContainer(Container &$container)
    {
        self::$container = $container;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return self::$container;
    }

    /**
     * @param array $data
     */
    public static function setData(array $data)
    {
        self::$data = array_merge(self::$data, $data);
    }

    /**
     * @return array
     */
    public static function getData()
    {
        return self::$data;
    }

    /**
     * @param Identity $identity
     */
    public static function setIdentity(Identity $identity)
    {
        self::$identity = $identity;
    }

    /**
     * @return Identity
     */
    public static function getIdentity()
    {
        return self::$identity;
    }

    /**
     * @return array
     */
    public static function getEnv($key = null)
    {
        if (isset($key)) {
            return self::$container->get('Config')->env[$key];
        } else {
            return self::$container->get('Config')->env;
        }
    }

    /**
     * @param $key
     * @param $data
     */
    public static function setEnv($key, $data)
    {
        self::getConfig()->env[$key] = $data;
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function getConfig($key = null)
    {
        $config = self::$container->get('Config');
        if (isset($config->$key)) {
            return $config->$key;
        } else {
            return $config;
        }
    }

    /**
     * @return array
     */
    public static function getApplication($key)
    {
        return self::getConfig()->application[$key];
    }

    public static function freezeConfig()
    {
        if (is_array(self::$config) && count(self::$config) == 0) {
            self::$config = (array) self::$container->get('Config');
        }
    }

}
