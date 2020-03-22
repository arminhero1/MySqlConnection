<?php


/**
 * Copyright (c) 2020.
 * Abolfazl Alizadeh Programming Code.
 * http://www.abolfazlalz.ir
 */

namespace MySqlConnection;


use mysqli;
use mysqli_result;

class TableControl
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var string
     */
    private $tableName;

    /**
     * TableControl constructor.
     * @param Connection $connection
     * @param string $tableName
     */
    public function __construct($connection, $tableName)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
    }

    /**
     * insert new value to table
     * @param $dataList
     * @return int
     */
    public function insert_query($dataList)
    {
        $this->connection->run_query_connection(QueryCreator::insert_query($this->tableName, $dataList), $conn);
        return mysqli_insert_id($conn);
    }

    /**
     * Update values from table
     * @param array $dataList
     * @param string $condition
     * @return bool|mysqli_result
     */
    public function update_query($dataList, $condition)
    {
        return $this->connection->run_query(QueryCreator::update_query($this->tableName, $dataList, $condition));
    }

    /**
     * Delete value from table
     * @param $condition
     * @return bool|mysqli_result
     */
    public function delete_query($condition)
    {
        return $this->connection->run_query(QueryCreator::delete_query($this->tableName, $condition));
    }

    public function value_exist($key, $value)
    {
        $selectQuery = new SelectQueryCreator($this->tableName);
        $condition = new ConditionBuilder();
        $condition->add($key, $value);
        $selectQuery->set_condition($condition);
        return count($this->select_query($selectQuery)) > 0;
    }

    public function values_exist($values) {
        $selectQuery = new SelectQueryCreator($this->tableName);
        $condition = new ConditionBuilder();
        foreach ($values as $key => $value) {
            $condition->addWithOperator($key, $value, ConditionBuilder::$AND);
        }

        return count($this->select_query($selectQuery)) > 0;
    }

    /**
     * @param SelectQueryCreator $selectQuery
     * @return array
     */
    public function select_query($selectQuery)
    {
        return $this->connection->run_select_query($selectQuery);
    }
}