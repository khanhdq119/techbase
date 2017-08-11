<?php
namespace Projects\TechBase;

use Projects\TechBase\Libraries\BaseApi;
use Projects\TechBase\Libraries\RestLoginApi;

const ROOT_PATH = __DIR__;

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/helpers/common.php';
require_once ROOT_PATH .'/libraries/base_api.php';
require_once ROOT_PATH .'/libraries/rest_login_api.php';

new RestLoginApi();
?>