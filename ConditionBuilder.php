<?php
/**
 * Copyright (c) 2020.
 * Abolfazl Alizadeh Programming Code.
 * http://www.abolfazlalz.ir
 */

namespace MySqlConnection;


class ConditionBuilder
{

    public static $AND = "and";
    public static $OR = "or";

    public static $EQUALS = "=";
    public static $HIGHER = ">";
    public static $LOWER = "<";
    public static $LIKE = "like";

    private $operators = array();
    private $conditions = array();
    private $conditionsStates = array();

    private $lastOperator = "and";


    public static function create_new_condition_builder()
    {
        return new ConditionBuilder();
    }


    /**
     * @param string $columnName
     * @param $value
     * @return ConditionBuilder check is added
     */
    public function add($columnName, $value)
    {
        return $this->addWithOperator($columnName, $value, $this->lastOperator);
    }

    /**
     * @param string $columnName
     * @param $value
     * @param string $operator
     * @return $this
     */
    public function addWithOperator($columnName, $value, $operator)
    {
        return $this->addWithOperatorAndCondition($columnName, $value, $operator, self::$EQUALS);
    }

    public function addWithOperatorAndCondition($columnName, $value, $operator, $condition)
    {
        if($condition == self::$LIKE && gettype($value) != "string") {
            return $this;
        }
        if(!array_key_exists($columnName, $this->conditions)) {
            $this->conditions[$columnName] = $value;
            $this->operators[$columnName] = $operator;
            $this->conditionsStates[$columnName] = $condition;
            $this->lastOperator = $operator;
        }
        return $this;
    }

    /**
     * Remove item from your condition by column name
     * @param $columnName
     */
    public function remove($columnName)
    {
        unset($this->conditions[$columnName]);
        unset($this->conditionsStates[$columnName]);
        unset($this->operators[$columnName]);
    }

    public function __toString()
    {
        return $this->condition();
    }

    public function condition()
    {
        $condition = "";
        foreach ($this->conditions as $columnName => $value) {
            if($condition != "") {
                $condition .= " " . $this->operators[$columnName] . " ";
            }

            $condition .= "`$columnName` " . $this->conditionsStates[$columnName] . " " . self::fix_value_format($value);
        }
        return $condition;
    }

    private static function fix_value_format($value)
    {
        if(gettype($value) == "string") {
            return "'$value'";
        } else {
            return $value;
        }
    }
}