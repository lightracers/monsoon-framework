<?php

/**
 * Model - the base model
 *
 */

namespace Framework;

/**
 * Base model class all other models will extend from this base.
 */
class Model
{

    /**
     * Hold the database connection.
     *
     * @var object
     */
    protected $db;

    /**
     * Create a new instance of the database helper.
     *
     * @var object
     */
    public function __construct(array $connectionParams = null)
    {
        /*
         * connect to PDO here.
         */
        $this->db = Database::get($connectionParams);
    }
}
