<?php
namespace Projects\TechBase\Libraries;

use Projects\TechBase\Libraries\BaseApi;
/**
 * Class RestApi
 * @package Projects\TechBase\Libraries
 * rest api server
 * response json format
 */

class RestApi extends BaseApi
{
    private $db;
    private $token;
    public function __construct($db) {
        parent::__construct();
        $this->checkUserLogin();
        $this->db = $db;

    }

    /**
     * check user login
     */
    private function checkUserLogin(){
        if(!verifyToken($_SERVER['HTTP_AUTHORIZATION'])) {
            $this->response(array('status' => false,'code' => "303", 'error' => 'Token is expired'), 200);
        }
    }

    public function getUser() {
        $query = "SELECT * FROM users";
        $result = mysqli_query($this->db, $query);
        $data = array();
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
                $data[] = $row;
            }
        }
        $this->response(array('status' => true,'data'=>$data), 200);
    }

    public function updateUser() {
        $query = "UPDATE users SET name='".$_POST['name']."',address='".$_POST['address']."', tel='".$_POST['tel']."' WHERE email='".$_POST['email']."'";
        if(mysqli_query($this->db, $query) == true) {
            $this->response(array('status' => true), 200);
        } else {
            $this->response(array('status' => false,'data'=>"Something went wrong"), 200);
        }
    }

    public function getByEmail($email) {
        $email = cleanInput($email);

        $query = "SELECT * FROM users where email='".$email."'";
        $result = mysqli_query($this->db, $query);
        $data = array();
        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
        }
        $this->response(array('status' => true,'data'=>$data), 200);
    }
}