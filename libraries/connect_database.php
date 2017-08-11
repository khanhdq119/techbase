<?php
namespace Projects\TechBase\Libraries;

/**
 * Class ConnectDatabase
 * @package Projects\TechBase\Libraries
 * execute connect database
 * return connection
 */
class ConnectDatabase
{
    public $conn = null;
    /**
     * check ip valid
     */
    public function connect(){
        if($this->conn == null) {
            $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD,DB_DATABASE);
            if (mysqli_connect_error()) {
                throw new Exception(mysqli_connect_error());
            }
        }
        return $conn;
    }
}