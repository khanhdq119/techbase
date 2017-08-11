<?php
//get ipaddress
function getRealIpAddress(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif(!empty( $_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
    return "";
}
// execute post data to url
function curlPost($url,$data=null,$digest=false,$method='POST',$username = '', $password = '',$token='') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if($digest == true) {
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
    }
    if($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    if($token) {
        $header[] = 'Authorization: '.$token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    return curl_exec($ch);
}
//create digital signature
function makeSignature($data) {
    openssl_sign($data, $signature, PRIVATE_KEY, OPENSSL_ALGO_SHA1);
    return base64_encode($signature);
}
/*
 * clean data
 * prevent sql injection
 * prevent xss
*/
function cleanInput($data) {
    return htmlspecialchars($data, ENT_QUOTES);
}

/**
 * @param $token
 * @return bool
 * verify token
 */
function verifyToken($token) {
    openssl_private_decrypt(base64_decode($token),$data,openssl_get_privatekey(PRIVATE_KEY));
    $decode = json_decode($data);
    if(isset($decode->expired_time) && $decode->expired_time > time()) {
        return true;
    }
    return false;
}