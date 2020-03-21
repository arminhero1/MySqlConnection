<?php

namespace MySqlConnection;

use mysqli;
use mysqli_result;

if (!class_exists("Server"))
    include_once "Server.php";

class Connection
{
    /**
     * @var Server
     */
    private $server;
    /**
     * @var string
     */
    private $database;

    /**
     * Connection constructor.
     * @param Server $server
     * @param string $database
     */
    public function __construct($server, $database)
    {
        $this->server = $server;
        $this->database = $database;
    }

    /**
     * @return mysqli
     */
    private function get_connection()
    {
        return new mysqli($this->server->getServer(), $this->server->getUsername(), $this->server->getPassword(), $this->database);
    }

    /**
     * @return string
     */
    public function get_connect_error()
    {
        return $this->get_connection()->connect_error;
    }

    /**
     * @param string $query
     * @return bool|mysqli_result
     */
    public function run_query($query)
    {
        $conn = $this->get_connection();
        if($conn->connect_error) {
            return false;
        }

        return $conn->query($query);
    }

    /**
     * @param string $query
     * @return array
     */
    public function run_select_query($query)
    {
        $selectValues = array();

        $result = $this->run_query($query);
        if($result != false && $result->num_rows > 0 && $result->fetch_assoc() != null) {
            while ($row = $result->fetch_assoc()) {
                $select = array();

                foreach (array_keys($row) as $item) {
                    $select[$item] = $row[$item];
                }

                $selectValues[] = $select;
            }
        }

        return $selectValues;
    }
}