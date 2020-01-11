<?php

namespace Framework;

/*
 * Entity = Database table
 */

class Entity extends Model
{
    /** @var string $thisTableName */
    public $thisTableName;

    /** @var string $thisIdField */
    public $thisIdField;

    /** @var array $data */
    public $data = [];

    /** @var string */
    private $_table;

    /**
     * Create a new instance of the database helper.
     */
    public function __construct(array $connectionParams = null)
    {
        parent::__construct($connectionParams);
    }

    /**
     * Table name getter setter
     */
    public function setTableName(string $tableName)
    {
        $this->thisTableName = $tableName;
    }

    public function getTableName()
    {
        return $this->thisTableName;
    }

    /**
     * ID field setter and getter
     */
    public function setIdField(string $columnName)
    {
        $this->thisIdField = $columnName;
    }

    public function getIdField()
    {
        return $this->thisIdField;
    }

    /**
     * Column names setter and getter
     */
    public function __set($name, $value)
    {
        $name = Utilities::convertCamelCaseToSnake($name);
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        $name = Utilities::convertCamelCaseToSnake($name);
        return $this->data[$name];
    }

    /**
     * Find row based on primary key
     */
    public function get($idFieldValue)
    {
        $sql = 'SELECT * FROM ' . $this->thisTableName . ' WHERE ' . $this->thisIdField . ' = ? limit 1';
        $this->db->sql(
            $sql,
            [$idFieldValue]
        );
        foreach ($this->db->result->fields as $fieldName) {
            $this->{$fieldName} = $this->db->result->rows[0][$fieldName];
        }
    }

    /**
     * saving the data
     */
    public function save()
    {
        // know the class vars set
        $fieldData = [];

        $classVars = $this->data;
        foreach ($classVars as $fieldName => $fieldValue) {
            $fieldData[$fieldName] = $fieldValue;
        }

        // check which statement to run
        if (FALSE != $this->{$this->thisIdField}) {
            // update
            return $this->db->update(
                $this->thisTableName,
                $fieldData,
                [
                    $this->thisIdField => $this->{$this->thisIdField},
                ]
            );
        } else {
            // insert
            $this->{$this->thisIdField} = $this->db->insert($this->thisTableName, $fieldData);
            return $this->{$this->thisIdField};
        }
    }

    /**
     * deleting data
     * @param $limit
     */
    public function remove($limit = 1)
    {
        if ($this->{$this->thisIdField} === null) {
            return;
        } else {
            $this->delete(
                $this->_table,
                [
                    $this->thisIdField => $this->{$this->thisIdField},
                ],
                $limit
            );
        }
    }
}
