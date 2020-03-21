<?php
/**
 * Copyright (c) 2020.
 * Abolfazl Alizadeh Programming Code.
 * http://www.abolfazlalz.ir
 */

namespace MySqlConnection {

    class QueryCreator
    {
        /**
         * Create Insert mysql Query
         * @param string $tableName
         * @param array $dataList
         * @return string
         */
        public static function insert_query($tableName, $dataList)
        {
            $keys = "";
            $values = "";

            foreach ($dataList as $key => $value) {
                if($keys != "" && $values != "") {
                    $keys .= ", ";
                    $values .= ", ";
                }

                $keys .= $key;
                $values .= self::fix_value_format($value);
            }

            return "INSERT INTO " . $tableName . " (" . $keys . ") VALUES (" . $values . ")";
        }

        /**
         * Create Update Query
         * @param string $tableName
         * @param array $dataList
         * @param string $condition
         * @return string
         */
        public static function update_query($tableName, $dataList, $condition)
        {
            $values = "";
            foreach ($dataList as $key => $value) {
                if($values != "") {
                    $values .= ", ";
                }
                $values .= "`$key`=" . self::fix_value_format($value);
            }

            return "UPDATE $tableName SET $values" . self::get_condition($condition);
        }

        /**
         * create default select mysql Query
         * @param string $tableName
         * @param string $condition
         * @param string $orderBy
         * @return string
         */
        public static function select_query($tableName, $condition, $orderBy = '')
        {
            return "SELECT * FROM $tableName" . self::get_condition($condition);
        }

        /**
         * create delete mysql Query
         * @param string $tableName
         * @param string $condition
         * @return string
         */
        public static function delete_query($tableName, $condition)
        {
            return "DELETE FROM $tableName" . self::get_condition($condition);
        }

        /**
         * query for create database
         * @param string $name new database name
         * @return string
         */
        public static function create_database_query($name)
        {
            return "CREATE DATABASE $name";
        }

        /**
         * create a format of condition for queries
         * @param $condition
         * @return string
         */
        private static function get_condition($condition)
        {
            return (($condition == "") ? "" : (" WHERE " . $condition));
        }

        /**
         * fix format for values for example if value is string with "String Text" value its changing to "'String Text'"
         * String and Date Time has String value
         *
         * @param $value
         * @return string
         */
        private static function fix_value_format($value)
        {
            if(gettype($value) == "string") {
                return "'$value'";
            } else if(gettype($value) == "date") {
                return date_format($value, "y-M-d");
            } else {
                return $value;
            }
        }
    }

    class SelectQueryCreator
    {
        const ORDER_BY_OPTION_DESC = "DESC";
        const ORDER_BY_OPTION_ACS = "ACS";

        const JOIN_ALIGN_LEFT = "LEFT";
        const JOIN_ALIGN_RIGHT = "RIGHT";


        private $condition;
        private $orderBy;
        private $limit;
        private $group;
        private $columns = "*";
        private $joinDatabase;
        private $table;

        /**
         * SelectQueryCreator constructor.
         * @param string $table
         */
        public function __construct($table)
        {
            $this->table = $table;
        }

        public static function new_select_query($table)
        {
            return new SelectQueryCreator($table);
        }


        /**
         * set condition for select from table
         * @param $condition
         * @return $this
         */
        public function set_condition($condition)
        {
            $this->condition = "WHERE $condition";
            return $this;
        }

        /**
         * Order select query
         * @param $column
         * @param string $order_option default value is 'DESC'
         * @return SelectQueryCreator
         */
        public function set_order_by($column, $order_option = 'DESC')
        {
            if(strtolower($order_option) != self::ORDER_BY_OPTION_ACS || strtolower($order_option) != self::ORDER_BY_OPTION_DESC)
                return $this;
            $this->orderBy = "ORDER BY $column $order_option";
            return $this;
        }

        /**
         * Remove define order type and use default
         * @return $this
         */
        public function clear_order_by()
        {
            $this->orderBy = '';
            return $this;
        }

        /**
         * Set a limit for select from table by max and min
         * @param int $min
         * @param int $max
         * @return $this
         */
        public function set_limit_max($min, $max)
        {
            $this->limit = "LIMIT $min, $max";
            return $this;
        }

        /**
         * Set a limit minimum is zero (0)
         * @param $limit
         * @return $this
         */
        public function set_limit($limit)
        {
            $this->limit = "LIMIT $limit";
            return $this;
        }

        /**
         * clear limit from query
         * @return $this
         */
        public function clear_limit()
        {
            $this->limit = '';
            return $this;
        }

        /**
         * set group by column name
         * @param string $columnName
         * @return $this
         */
        public function set_group($columnName)
        {
            $this->group = "GROUP BY $columnName";
            return $this;
        }

        /**
         * remove defined group from query
         * @return $this
         */
        public function clear_group()
        {
            $this->group = '';
            return $this;
        }

        /**
         * select defined column from table
         * like 'SELECT `some_column` from some_table'
         * @param string|string[] $columns
         * @return $this
         */
        public function select_columns($columns)
        {
            if(gettype($columns) == "array") {

                $strColumns = "";

                foreach ($columns as $column) {
                    if($columns != "") {
                        $strColumns .= ", ";
                    }
                    $strColumns .= "`$column`";
                }

                $this->columns = $strColumns;
            } else if(gettype($columns) == "string") {
                $this->columns = $columns;
            }
            return $this;
        }

        /**
         * select all of column from database
         * its can remove concat
         * like 'SELECT * FROM `some_table`'
         * @return $this
         */
        public function clear_select()
        {
            $this->columns = "*";
            return $this;
        }

        /**
         * join a database to your query
         * @param string $database second database's name
         * @param string|string[] $using tables you want to join your query
         * @param string $align it's should have 'RIGHT' or 'LEFT' value
         * @return $this
         */
        public function join_database($database, $using, $align = 'RIGHT')
        {
            if(strtolower($align) != self::JOIN_ALIGN_LEFT || strtolower($align) != self::JOIN_ALIGN_RIGHT)
                return $this;
            $using_str = "";

            if(gettype($using) == 'string') {
                $using_str = $using;
            } elseif(gettype($using) == 'string[]' or gettype($using) == 'array') {
                foreach ($using as $item) {
                    if($using_str != "")
                        $using_str .= ', ';
                    $using_str .= $item;
                }
            }

            $this->joinDatabase = "$align JOIN `$database` USING $using_str";
            return $this;
        }

        /**
         * clear defined join query from query
         * @return $this
         */
        public function clear_join()
        {
            $this->joinDatabase = "";
            return $this;
        }

        /**
         * add concat or custom column to your query
         * like 'SELECT (`column_1`, `column_2`) AS columns FROM `table`'
         * @param $tables
         * @param $contactName
         * @return $this
         */
        public function set_concat($tables, $contactName)
        {
            $tablesStr = "";

            if(gettype($tables) == "array" or gettype($tables) == "string[]") {
                foreach ($tables as $table) {
                    if($tablesStr != "")
                        $tablesStr .= ", ";
                    $tablesStr .= "$table";
                }
            } else if(gettype($tables) == "string") {
                $tablesStr = $tables;
            }

            $this->columns = "CONCAT ($tablesStr) AS $contactName";
            return $this;
        }

        /**
         * @return string query result
         */
        public function __toString()
        {
            return "SELECT " . $this->columns . " FROM " . $this->table . " " . $this->joinDatabase . " " . $this->condition . " " . $this->orderBy . " " . $this->group . " " . $this->limit;
        }


    }
}