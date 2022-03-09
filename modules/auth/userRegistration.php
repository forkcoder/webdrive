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
  $sql = "SELECT remember_token FROM fcoder_users where email_id='$email_id'";
  $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed.");

  if (mysqli_num_rows($result) >= 1) {
    if (mysqli_fetch_row($result) != '')
      $rows['opts']['msg'] = 'A mail has been sent already to ' . $email_id;
    else
      $rows['opts']['msg'] = 'An account already exist. Please login again.';
  } else {
    $token = generateRandomString(64);
    ini_set("include_path", '/home/realmg/php:' . ini_get("include_path").':.:/usr/local/share/pear/');
    // set_include_path(); 
    require_once "Mail.php";

    $from = "noreply@forkcoder.com";
    $to = $email_id;

    $host = "mail.forkcoder.com";
    $username = 'noreply@forkcoder.com';
    $password = 'i7PEzc^6F&BH';
    $link = "https://drive.forkcoder.com?token=".$token;
    $subject = "Registration at Web Drive";
    $body = "Welcome to Web Drive. Click following link to complete your registration.\n\n".$link."\n\nBest Regards\nICTIMMD, Head Office, Bangladesh Bank.";
    $headers = array('From' => $from, 'To' => $to, 'Subject' => $subject);
    $smtp = Mail::factory(
      'smtp',
      array(
        'host' => $host,
        'auth' => 'PLAIN',
        'port' => '26',
        'socket_options' => array('ssl' => array('verify_peer_name' => false)),
        'username' => $username,
        'password' => $password
      )
    );

    $mail = $smtp->send($to, $headers, $body);

    if (PEAR::isError($mail)) {
      // echo ($mail->getMessage());
      $rows['opts']['msg'] = $mail->getMessage();// 'Email has not been delivered successfully.';
    } else {
      $sql = "INSERT INTO fcoder_users (remember_token, email_id) VALUES('$token','$email_id')";
      mysqli_query($con, $sql) or die("Data could not be inserted into user table.");
       $rows['opts']['msg'] = 'A mail has been sent to your mail account. Check your Mail account.';
    }
  }
  $session->closeDBConnection($con);
  $rows['opts']['status'] = true;
}
echo json_encode($rows);
