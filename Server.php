<?php
/**
 * Copyright (c) 2020.
 * Abolfazl Alizadeh Programming Code.
 * http://www.abolfazlalz.ir
 */

namespace MySqlConnection;

class Server
{
    /**
     * @var string
     */
    private $server;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;
    /**
     * @var int
     */
    private $port;

    /**
     * Server constructor.
     * @param string $server
     * @param string $username
     * @param string $password
     * @param int $port
     */
    public function __construct($server, $username, $password, $port)
    {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
    }

    /**
     * @return Server
     */
    public static function get_default_localhost()
    {
        return new Server("localhost", "root", "", 3306);
    }

    /**
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }
}