<?php
namespace Framework;

class Datasource
{

    /** @var integer $rows */
    public $rows;

    /** @var array $fields */
    public $fields;

    /** @var boolean $indexRows */
    public $indexRows;

    /** @var integer $rowCount */
    public $rowCount;

    /** @var integer $colCount */
    public $colCount;

    /** @var string $id */
    public $id;

    /** @var boolean */
    private $expectResult;

    /** @var mixed */
    public $lastInsertId;

    /** @var mixed */
    public $affectedRows;

    public function __construct ($id = false)
    {
        if ($id) {
            $this->id = $id;
        }

        $this->rowCount = 0;
        $this->colCount = 0;
        $this->rows     = [];
        $this->fields   = [];

        $this->sqlType      = '';
        $this->expectResult = false;

        $this->error     = '';
        $this->indexRows = true;
    }

    public function reset ()
    {
        $this->__construct();
    }

    public function addRow ($indexedArrayData)
    {
        if (is_array($indexedArrayData)) {
            $this->rows[] = $indexedArrayData;
        }
    }

    public function prepare ()
    {
        $this->rowCount = count($this->rows);

        // if there are any rows
        if ($this->rowCount > 0) {
            // know the field names from the first row
            $this->fields   = array_keys($this->rows[0]);
            $this->colCount = count($this->fields);
        }
    }

    public function getSQLType ($sqlQuery)
    {
        $sqlType = strtolower(substr($sqlQuery, 0, 10));

        if (strstr($sqlType, "show datab")) {
            $sqlType = 'show';
        } else if (strstr($sqlType, "select")) {
            $sqlType = 'select';
        } else if (strstr($sqlType, "exec")) {
            $sqlType = 'exec';
        } else if (strstr($sqlType, "insert int")) {
            $sqlType = 'insert';
        } else if (strstr($sqlType, "update")) {
            $sqlType = 'update';
        } else if (strstr($sqlType, "delete")) {
            $sqlType = 'delete';
        } else if (strstr($sqlType, "create tab")) {
            $sqlType = 'createTable';
        } else if (strstr($sqlType, "create dat")) {
            $sqlType = 'createDatabase';
        } else if (strstr($sqlType, "create ind")) {
            $sqlType = 'createIndex';
        } else if (strstr($sqlType, "create vie")) {
            $sqlType = 'createView';
        } else if (strstr($sqlType, "create use")) {
            $sqlType = 'createUser';
        } else if (strstr($sqlType, "drop table")) {
            $sqlType = 'dropTable';
        } else if (strstr($sqlType, "drop datab")) {
            $sqlType = 'dropDatabase';
        } else if (strstr($sqlType, "drop index")) {
            $sqlType = 'dropIndex';
        } else if (strstr($sqlType, "truncate")) {
            $sqlType = 'truncate';
        } else if (strstr($sqlType, "alter")) {
            $sqlType = 'alter';
        }

        return $sqlType;
    }
}
