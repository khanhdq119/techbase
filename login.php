<?php
namespace Projects\TechBase;

use Projects\TechBase\Libraries\BaseApi;
use Projects\TechBase\Libraries\RestLoginApi;

const ROOT_PATH = __DIR__;

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/helpers/common.php';
require_once ROOT_PATH .'/libraries/base_api.php';
require_once ROOT_PATH .'/libraries/rest_login_api.php';
if ( ! session_id() ) @ session_start();
$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = cleanInput($_POST['username']);
    $password = cleanInput($_POST['password']);
    $timestamp = time();
    $signature = makeSignature($username.$password.$timestamp);
    $data = array(
        'username' => $username,
        'password' => $password,
        'timestamp' => $timestamp,
        'signature' => $signature
    );
    $response = curlPost(BASE_URL.'/login_api.php',$data,false);
    if($response) {
        $data = json_decode($response);
        if($data->status == false) {
            $error = $data->error;
        } else {
            $_SESSION["token"] = $data->token;
            header("Location: ".BASE_URL.'/users.php');
        }
    } else {
        $error = "Something went wrong";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Bootstrap 101 Template</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<br/>
<div class="container">
    <br/>
    <?php if($error) {
        echo '<p class="bg-danger">'.$error.'</p>';
    } ?>
    <form action="<?php echo BASE_URL.'/login.php'; ?>" method="post">
        <div class="form-group">
            <label for="exampleInputEmail1">Email address</label>
            <input type="text" class="form-control" name="username" id="exampleInputEmail1" placeholder="Email">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input type="password" class="form-control" name="password" id="exampleInputPassword1" placeholder="Password">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
    </form>
</div>
</body>
</html>
