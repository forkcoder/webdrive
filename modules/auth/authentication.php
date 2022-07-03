<?php
session_start();
require('../Servlets.php');
$session = new DBProxy();
$login_id = $_POST['fcoder-login-uid'];
$password = $_POST['fcoder-login-psw'];
$rows=array();
$rows['opts']['status']=false;

$rows['opts']['msg'] = 'Unauthorized Access to Login Page.';
$lk = isset($_SESSION['login_key']) ? $_SESSION['login_key'] : uniqid();
if ($_POST['auth_ph'] == "" || $_POST['auth_ph'] != $lk) {
  header("Location: /index.php");
  die();
} else if ($_POST['auth_ph'] == $_SESSION['login_key']) {
  $rows['errors'] = array();
	$errors['key'] = array();
    $con = $session->initDBConnection();
    $loginStat = $session->login($con, $login_id, $password, 'login');
    $session->closeDBConnection($con);
    if($loginStat == 0)
      $rows['opts']['status'] = true;
    else if($loginStat == 1){
      $errors['key'][] = 'fcoder-login-psw';
      $errors['msg'][] = 'Given Password is not correct.';
    }
    else if($loginStat == 2){
      $errors['key'][] = 'fcoder-login-uid';
      $errors['msg'][] = 'Given User ID is not exist.';
    }
    else {
      $errors['key'][] = 'fcoder-login-uid';
      $errors['msg'][] = 'Invalid User ID or Email ID.';
    }
  $rows['errors'] = $errors;
  $rows['errors']['total'] = count($errors['key']);
}
echo json_encode($rows);
