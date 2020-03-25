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

    private $conditionKeysCount = array();
    /**
     * @var bool
     */
    private $fix_value;

    /**
     * ConditionBuilder constructor.
     * @param bool $fix_value
     */
    public function __construct($fix_value = true)
    {
        $this->fix_value = $fix_value;
    }

    public static function create_new_condition_builder($fix_value = true)
    {
        return new ConditionBuilder($fix_value);
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
        if($condition == self::$LIKE && gettype($value) != "string") {
            return $this;
        }

        $columnName = "`$columnName`";

        if (!array_key_exists($columnName, $this->conditionKeysCount)) {
            $this->conditionKeysCount[$columnName] = 1;
        } else {
            $this->conditionKeysCount[$columnName]++;
            $columnName .= '/*' . $this->conditionKeysCount[$columnName] . '*/';
        }

        $this->conditions[$columnName] = $value;
        $this->operators[$columnName] = $operator;
        $this->conditionsStates[$columnName] = $condition;
        $this->lastOperator = $operator;
        return $this;
    }

    public function addWithCondition($columnName, $value, $condition)
    {
        return $this->addWithOperatorAndCondition($columnName, $value, $this->lastOperator, $condition);
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

            $condition .= "$columnName " . $this->conditionsStates[$columnName] . " " . $this->fix_value ? self::fix_value_format($value) : $value;
        }
        return $condition;
    }

    /**
     * convert array condition to string
     * example 1: if $fix_value is true
     *      input: array['id' => 2, 'name' => 'Abolfazl']
     *      output: 'id=2 and name='Abolfazl''
     * example 2: if $fix_value is false
     *      input: array['id' => 2, 'name' => 'Abolfazl']
     *      output: 'id=2 and name=abolfazl'
     * @param array $conditions
     * @param bool $fix_value fix values by that type
     * @return string
     */
    public static function array_condition_to_string($conditions, $fix_value = true)
    {
        $conditionString = "";

        foreach ($conditions as $key => $value) {
            if($conditionString != "")
                $conditionString .= " and ";
            $conditionString .= "$key=" . ($fix_value ? self::fix_value_format($value) : $value);
        }

        return $conditionString;
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