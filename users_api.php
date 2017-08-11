<?php
namespace Projects\TechBase;

use Projects\TechBase\Libraries\RestApi;
use Projects\TechBase\Libraries\ConnectDatabase;

const ROOT_PATH = __DIR__;

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/helpers/common.php';
require_once ROOT_PATH . '/libraries/connect_database.php';
require_once ROOT_PATH . '/libraries/base_api.php';
require_once ROOT_PATH . '/libraries/rest_api.php';
$db = new ConnectDatabase();
$users = new RestApi($db->connect());

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $users->updateUser();
} else {
    if($_GET['email']) {
        $users->getByEmail($_GET['email']);
    } else {
        $users->getUser();
    }

}
?>