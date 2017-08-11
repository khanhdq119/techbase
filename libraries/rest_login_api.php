<?php
namespace Projects\TechBase\Libraries;

use Projects\TechBase\Libraries\BaseApi;
/**
 * Class AbstractApi
 * @package Projects\TechBase\Libraries
 * rest api server
 * response json format
 */
class RestLoginApi extends BaseApi
{
    private $publicKey = '';

    public function __construct() {
        parent::__construct();
        $this->checkSecurity();
    }

    private function checkSecurity() {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $timestamp = $_POST['timestamp'];
        $signature = base64_decode($_POST['signature']);
        $data = $username.$password.$timestamp;
        $fp = fopen("keys/cert.crt", "r");
        $cert = fread($fp, 8192);
        if(openssl_verify($data, $signature, $cert, OPENSSL_ALGO_SHA1) == false){
            $this->response(array('status' => false, 'error' => 'Bad signature'), 200);
        }
        $this->login();
        $this->generateToken($username,$cert);
    }

    private function generateToken($username,$cert) {
        $jwt = array(
            'username' => $username,
            'expired_time' =>  time() + 60*30
        );
        openssl_public_encrypt(json_encode($jwt),$encrypted,$cert);
        $this->response(array('status' => true, 'token' => base64_encode($encrypted)), 200);
    }
    /**
     * check ip valid
     */
    protected function login(){
        $username = $_POST['username'];
        $password = $_POST['password'];
        if($username != USERNAME_LOGIN || $password != PASSWORD_LOGIN) {
            $this->response(array('status' => false, 'error' => 'Wrong Username or Password'), 200);
        }
    }
}