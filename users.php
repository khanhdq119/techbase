<?php
namespace Projects\TechBase;

use Projects\TechBase\Libraries\BaseApi;
use Projects\TechBase\Libraries\RestApi;

const ROOT_PATH = __DIR__;

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/helpers/common.php';
require_once ROOT_PATH .'/libraries/base_api.php';
require_once ROOT_PATH .'/libraries/rest_api.php';
if ( ! session_id() ) @ session_start();
$error = '';
if($_SERVER['REQUEST_METHOD'] == "POST") {
    $dataPost = array(
        'email' =>cleanInput($_POST['email']),
        'name' => cleanInput($_POST['name']),
        'address' => cleanInput($_POST['address']),
        'tel' => cleanInput($_POST['tel'])
    );
    $response = curlPost(BASE_URL.'/users_api.php',$dataPost,false,"POST",'','',$_SESSION['token']);
    if($response) {
        $rs = json_decode($response);
        if($rs->status == false) {
            if(isset($rs->code) && $rs->code == 303) {
                header("Location: ".BASE_URL.'/login.php');
            }
            $error = $rs->code;
        } else {
            header("Location: ".BASE_URL.'/users.php');
        }
    } else {
        $error = "Something went wrong";
    }
} else {
    if($_GET['email']) {
        $response = curlPost(BASE_URL.'/users_api.php?email='.$_GET['email'],null,false,"GET",'','',$_SESSION['token']);
        if($response) {
            $rs = json_decode($response);
            if($rs->status == false) {
                if(isset($rs->code) && $rs->code == 303) {
                    header("Location: ".BASE_URL.'/login.php');
                }
                $error = $rs->code;
            } else {
                $user = $rs->data;
            }
        } else {
            $error = "Something went wrong";
        }
    } else {
        $response = curlPost(BASE_URL.'/users_api.php',null,false,"GET",'','',$_SESSION['token']);
        $users = array();
        if($response) {
            $rs = json_decode($response);
            if($rs->status == false) {
                if(isset($rs->code) && $rs->code == 303) {
                    header("Location: ".BASE_URL.'/login.php');
                }
                $error = $rs->code;
            } else {
                $users = $rs->data;
            }
        } else {
            $error = "Something went wrong";
        }
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
    <?php if($_GET['email'])  { ?>
        <form action="<?php echo BASE_URL.'/users.php'; ?>" method="post">
            <div class="form-group">
                <label for="exampleInputEmail1">Email address</label>
                <input type="text" class="form-control" disabled value="<?php if(isset($user)) echo $user->email; ?>" id="exampleInputEmail1">
                <input type="hidden" class="form-control" name="email" value="<?php if(isset($user)) echo $user->email; ?>" id="exampleInputEmail1">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Name</label>
                <input type="text" class="form-control" name="name" value="<?php if(isset($user)) echo $user->name; ?>" id="exampleInputPassword1">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Address</label>
                <input type="text" class="form-control" name="address" value="<?php if(isset($user)) echo $user->address; ?>" id="exampleInputEmail1">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Name</label>
                <input type="text" class="form-control" name="tel" value="<?php if(isset($user)) echo $user->tel; ?>" id="exampleInputPassword1">
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
        </form>
    <?php } else { ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <td>Email</td>
                    <td>Name</td>
                    <td>Address</td>
                    <td>Tel</td>
                    <td>Action</td>
                </tr>
                </thead>
                <?php if(isset($users) && $users) { ?>
                    <tbody>
                    <?php foreach($users as $user) { ?>
                        <tr>
                            <td><?php echo $user->email; ?></td>
                            <td><?php echo $user->name; ?></td>
                            <td><?php echo $user->address; ?></td>
                            <td><?php echo $user->tel; ?></td>
                            <td><a href="<?php echo BASE_URL.'/users.php?email='.$user->email;?>">Edit</a> </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                <?php } ?>
            </table>
        </div>
    <?php } ?>
</div>
</body>
</html>
