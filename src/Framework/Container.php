<?php

namespace Framework;

class Container
{
    /** @var array */
    private $objects = [];

    /** @var array */
    private $aliases = [];

    /**
     * @param string $className
     * @return mixed
     */
    public function get($className)
    {
        // if alias exists
        if (array_key_exists($className, $this->aliases)) {
            $className = $this->aliases[$className];
        }

        if (isset($this->objects[$className])) {
            return $this->objects[$className];
        } else {
            return $this->objects[$className] = new $className();
        }
    }

    /**
     * @param string $className
     * @param array $params
     * @return mixed
     */
    public function getWithParams(string $className, array $params = [])
    {
        // if alias exists
        if (array_key_exists($className, $this->aliases)) {
            $className = $this->aliases[$className];
        }

        if (isset($this->objects[$className])) {
            return $this->objects[$className];
        } else {
            return $this->objects[$className] = new $className(...$params);
        }
    }

    /**
     * @param string $className
     * @return bool
     */
    public function has($className)
    {
        if (array_key_exists($className, $this->aliases)) {
            return isset($this->objects[$this->aliases[$className]]);
        } else {
            return isset($this->objects[$className]);
        }
    }

    /**
     * @param $className
     * @param $object
     */
    public function set($className, $object)
    {
        $this->objects[$className] = $object;

    }

    public function setAlias($className, $aliasName)
    {
        $this->aliases[$aliasName] = $className;
    }

}
