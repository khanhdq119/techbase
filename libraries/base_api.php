<?php
namespace Projects\TechBase\Libraries;

/**
 * Class AbstractApi
 * @package Projects\TechBase\Libraries
 * base api server include common function for security
 * response json format
 */
abstract class BaseApi
{
    public function __construct() {
        $this->checkIpWhiteList();
    }

    /**
     * check ip valid
     */
    protected function checkIpWhiteList(){
        if (! in_array(getRealIpAddress(), IP_WHITE_LIST)) {
            $this->response(array('status' => false, 'error' => 'IpAddress is invalid.'), 401);
        }
    }

    /*
     * using Digest HTTP Authentication
     */
    protected function digestAuthenticate() {
        //check server authentication variable to use because the PHP ISAPI module in IIS acts different from CGI
        if ($_SERVER['PHP_AUTH_DIGEST']) {
            $digestString = $_SERVER['PHP_AUTH_DIGEST'];
        } elseif ($_SERVER['HTTP_AUTHORIZATION']){
            $digestString = $_SERVER['HTTP_AUTHORIZATION'];
        } else {
            $digestString = "";
        }
        $nonce = uniqid();
        if (empty($digestString)) {
            $this->forceAuthenticate($nonce);
        }
        //retrieve authentication informations from the auth variable
        preg_match_all('@(username|nonce|uri|nc|cnonce|qop|response)=[\'"]?([^\'",]+)@', $digestString, $matches);
        $digest = array_combine($matches[1], $matches[2]);
        $validLogin = REST_VALID_LOGIN;
        if (!$this->checkAuthenticate($digest,$validLogin)) {
            $this->forceAuthenticate($nonce);
        }
        $validPass = $validLogin[$digest['username']];

        // This is the valid response expected
        $a1 = md5($digest['username'].':'.REST_REALM.':'.$validPass);
        $a2 = md5(strtoupper($_SERVER['REQUEST_METHOD']).':'.$digest['uri']);
        $validResponse = md5($a1.':'.$digest['nonce'].':'.$digest['nc'].':'.$digest['cnonce'].':'.$digest['qop'].':'.$a2);

        if ($digest['response'] != $validResponse) {
            header('HTTP/1.0 401 Unauthorized');
            header('HTTP/1.1 401 Unauthorized');
            exit;
        }
    }

    /**
     * @param $nonce
     * open http digest authentication
     */
    protected function forceAuthenticate($nonce) {
        header('WWW-Authenticate: Digest realm="' . REST_REALM . '", qop="auth", nonce="' . $nonce . '", opaque="' . md5(REST_REALM) . '"');
    }

    protected function checkAuthenticate($digest,$validLogin)
    {
        $username = $digest['username'];
        if(!array_key_exists('username', $digest) || !$username) {
            return FALSE;
        }

        if (!array_key_exists($username, $validLogin)) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * @param array $data
     * @param null $httpCode
     * response data
     */
    public function response($data = array(), $httpCode = null) {
        // If data is empty and not code provide, error and bail
        if (empty($data) && $httpCode === null) {
            $httpCode = 404;
            $output = $data;
        } else {
            // all the compression settings must be done before sending any headers
            if (@ini_get('zlib.output_compression') == FALSE) {
                if (extension_loaded('zlib')) {
                    // if the client supports GZIP compression
                    if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) {
                        ob_start('ob_gzhandler');
                    }
                }
            }
            header('Content-type: application/json');
            $output = json_encode($data);
        }

        if(is_array($data) && isset($data['error'])){
                header('HTTP/1.1 ' . $httpCode);
        } else {
            header('HTTP/1.1 ' . $httpCode);
            header('Status: ' . $httpCode);
            header('Content-Length: ' . strlen($output));
        }
        exit($output);
    }

}