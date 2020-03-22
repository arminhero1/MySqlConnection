<?php

include 'MySqlConnection.php';

use MySqlConnection\Connection;

$conn = Connection::create_connection("localhost", "root", "", "test");

//var_dump($conn->run_select_query("SELECT * FROM persons"));


//$data = ["name" => "Erfan", "lastname" => "Jafari", "age" => 18];
//$tblCtrl = new \MySqlConnection\TableControl($conn, "persons");


$selectQuery = new \MySqlConnection\SelectQueryCreator("persons");
$condition = new \MySqlConnection\ConditionBuilder();
$condition->addWithOperatorAndCondition("id", 3, "and", ">");
$selectQuery->set_condition($condition);


var_dump($selectQuery->run_query($conn));


