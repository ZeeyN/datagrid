<?php
class Database{

    public $connection;

    public function __construct(){

        $this->open_db_connection();

    }

    public function open_db_connection(){

        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if($this->connection->connect_errno){

            die("Database connection failed badly".$this->connection->connect_error);

        }
    }

    public function query($sql){

        $result = $this->connection->query($sql);

        return $result;
    }


}
$database = new Database();