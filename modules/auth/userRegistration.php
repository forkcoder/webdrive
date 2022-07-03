<?php
session_start();
require('../Servlets.php');
$session = new DBProxy();
function generateRandomString($length)
{
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}
$rows = array();
$rows['opts']['msg'] = '';
$rows['opts']['status'] = false;
$errors = array();
$errors['key'] = array();
$email_id = $_REQUEST['email_id'];

if ($email_id == '' || strlen($email_id) >= 100 || filter_var($email_id, FILTER_VALIDATE_EMAIL) === false) {
  $errors['key'][] = 'fdrive-login-uid';
  $errors['msg'][] = 'Email ID must be given in correct format.';
  $rows['errors'] = $errors;
  $rows['errors']['total'] = 1;
} else {
  $con = $session->initDBConnection();
  $sql = "SELECT remember_token, userid FROM fcoder_users where email_id='$email_id'";
  $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed.");

  if (mysqli_num_rows($result) >= 1) {
    $data = mysqli_fetch_assoc($result);
    if ( $data['userid']!= '')
      $rows['opts']['msg'] = '<div class="loginInputFieldStyle" >Account with this '.$email_id.' is already exist.</div><div class="loginInputFieldStyle" ><span onClick="logintoHelpdesk()" class="backlinks">Back to Login</span></div>';
    else 
      $rows['opts']['msg'] = '<div class="loginInputFieldStyle" >An Email has been sent already to ' . $email_id.'</div><div class="loginInputFieldStyle" ><span onClick="logintoHelpdesk()" class="backlinks">Back to Login</span></div>';
  } else {
    date_default_timezone_set("Asia/Dhaka");
    $today = date("Y-m-d H:m:s");
    $token = generateRandomString(64);
    ini_set("include_path", '/home/realmg/php:' . ini_get("include_path").':.:/usr/local/share/pear/');
    // set_include_path(); 
    require_once "Mail.php";

    $from = getenv('RESET_MAIL_FROM');
    $to = $email_id;

    $host = getenv('RESET_MAIL_HOST');
    $port = getenv('RESET_MAIL_PORT');
    $username = getenv('RESET_MAIL_USER');
    $password = getenv('RESET_MAIL_PSWD');
    $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"."?token=".$token;
    $subject = "Registration at Web Drive";
    $body = "Welcome to Web Drive. Click following link to complete your registration.\n\n".$link."\n\nBest Regards\nICTIMMD, Head Office, Bangladesh Bank.";
    $headers = array('From' => $from, 'To' => $to, 'Subject' => $subject);
    $smtp = Mail::factory(
      'smtp',
      array(
        'host' => $host,
        'port' => $port,
        'auth' => true,
        'socket_options' => array('ssl' =>  array(
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true
      )),
        'username' => $username,
        'password' => $password
      )
    );

    $mail = $smtp->send($to, $headers, $body);

    if (PEAR::isError($mail)) {
      // echo ($mail->getMessage());
      $rows['opts']['msg'] = $mail->getMessage();// 'Email has not been delivered successfully.';
    } else {
      $sql = "INSERT INTO fcoder_users (remember_token, email_id, created_at) VALUES('$token','$email_id', '$today')";
      mysqli_query($con, $sql) or die("Data could not be inserted into user table.".$sql);
       $rows['opts']['msg'] = '<div class="loginInputFieldStyle" >Registration link has been sent to '. $email_id.'. Check mail Inbox and click on Link to finish Registration.</div><div class="loginInputFieldStyle" ><span onClick="logintoHelpdesk()" class="backlinks">Back to Login</span></div>';
    }
  }
  $session->closeDBConnection($con);
  $rows['opts']['status'] = true;
}
echo json_encode($rows);
