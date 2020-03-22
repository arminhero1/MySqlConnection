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
    public function update_query($dataList, $condition = '')
    {
        return $this->connection->run_query(QueryCreator::update_query($this->tableName, $dataList, $condition));
    }

    /**
     * Delete value from table
     * @param $condition
     * @return bool|mysqli_result
     */
    public function delete_query($condition = '')
    {
        return $this->connection->run_query(QueryCreator::delete_query($this->tableName, $condition));
    }

    /**
     * check a variable exist in a table.
     * @param string $key
     * @param $value
     * @return bool
     */
    public function value_exist($key, $value)
    {
        $selectQuery = new SelectQueryCreator($this->tableName);
        $condition = new ConditionBuilder();
        $condition->add($key, $value);
        $selectQuery->set_condition($condition);
        return count($this->select_query($selectQuery)) > 0;
    }

    /**
     * check multiple variables exist in a table
     * @param array $values like ['username' => 'abolfazl.alz', 'password'='1234']
     * @return bool
     */
    public function values_exist($values)
    {
        $selectQuery = new SelectQueryCreator($this->tableName);
        $condition = new ConditionBuilder();
        foreach ($values as $key => $value) {
            $condition->addWithOperator($key, $value, ConditionBuilder::$AND);
        }
        $selectQuery->set_condition($condition);
        return count($this->select_query($selectQuery)) > 0;
    }

    /**
     * get count of rows by condition from table
     * @param string|array $condition
     * @param string $column
     * @return int
     */
    public function select_count($condition = '', $column = '*')
    {
        $countValues = $this->connection->run_select_query(QueryCreator::select_count_query($condition));
        if(count($countValues) == 0 || !array_key_exists("COUNT($column)", $countValues)) {
            return 0;
        }

        return $countValues[0]["COUNT($column)"];
    }

    /**
     * Select values by SelectQueryCreator object
     * @param SelectQueryCreator $selectQuery
     * @return array
     */
    public function select_query($selectQuery)
    {
        return $this->connection->run_select_query($selectQuery);
    }

    /**
     * Select by condition from table
     * @param string|array $condition
     * @return array
     */
    public function select_with_condition($condition)
    {
        return $this->connection->run_select_query(QueryCreator::select_query($this->tableName, $condition));
    }

    /**
     * connection use for this TableControl
     * @return Connection
     */
    public function get_connection()
    {
        return $this->connection;
    }

    /**
     * first check value or values not exist in a table then insert value
     * @param array $dataList values for insert to table
     * @param array|string $itemsToCheck like ['username' => 'abolfazl']
     * @return int column index result
     */
    public function insert_if_not_exist($dataList, $itemsToCheck)
    {
        if ($this->select_count($itemsToCheck) > 0)
            return -1;
        return $this->insert_query($dataList);
    }

    /**
     * It first checks whether the value exists
     * Updates it if it exists
     * If it doesn't, it exaggerates
     * @param array $dataList
     * @param array|string $itemsToCheck like ['username' => 'abolfazl']
     * @return bool|int|mysqli_result
     */
    public function insert_if_exist_update($dataList, $itemsToCheck) {
        if ($this->select_count($itemsToCheck) > 0)
            return $this->update_query($dataList, $itemsToCheck);
        return $this->insert_query($dataList);
    }
}