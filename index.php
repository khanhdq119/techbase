<?php
if ( ! session_id() ) @ session_start();
const BASE_URL = 'http://localhost/techbase';
if($_SESSION['token']) {
    header("Location: ".BASE_URL.'/users.php');
    exit();
} else {
    header("Location: ".BASE_URL.'/login.php');
    exit();
}
