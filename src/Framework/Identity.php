<?php

namespace Framework;

class Identity
{
    /** @var array  */
    public $info = [];

    /** @var array  */
    public $roles = [];

    /** @var array  */
    public $permissions = [];

    /**
     * @param array $info
     */
    public function setInfo(array $info)
    {
        $this->info = $info;
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }

    /**
     * @param array $permissions
     */
    public function setPermissions(array $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions);
    }


    /**
     * @param $property
     * @return mixed
     */
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    /**
     * @param $property
     * @param $value
     */
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }

    /**
     * Automatic getters and setters
     *
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $arguments)
    {
        $property = lcfirst(substr($method, 3));

        if (strncasecmp($method, "get", 3) === 0) {
            return $this->$property;
        } else if (strncasecmp($method, "set", 3) === 0) {
            $this->$property = $arguments[0];
        } else {
            throw new \Exception('Method not found - ' . $method);
        }
    }
}
