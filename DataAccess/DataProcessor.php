<?php

/**
 * Created by PhpStorm.
 * User: HongYang
 * Date: 2016/12/16
 * Time: 10:31
 */

class DataProcessor {
    private $serverName = "localhost:3306";
    private $username = "yhong0726";
    private $password = "dengluri";
    private $dbName = "party_yanghong";
    private $inConnection = false;
    private $connection;

    public function connectMySQL() {
        $this->connection = new mysqli($this->serverName, $this->username, $this->password, $this->dbName);
        if ($this->connection->connect_error) {
            return false;
        }
        $this->inConnection = true;
        return true;
    }

    public function getConn() {
        return $this->connection;
    }

    public function closeConn() {
        $this->connection->close();
    }
}