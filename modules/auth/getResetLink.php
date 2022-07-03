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
$rows['opts']['msg'] = 'Failed to send reset link. Please contact with site administrator.';
$rows['opts']['status'] = false;
$errors = array();
$errors['key'] = array();
$email_id = $_REQUEST['email_id'];

if ($email_id == '' || strlen($email_id) >= 100 || filter_var($email_id, FILTER_VALIDATE_EMAIL) === false) {
  $errors['key'][] = 'fdrive-login-uid';
  $errors['msg'][] = 'Email ID must be given in correct format.';
} else {
  $con = $session->initDBConnection();
  $sql = "SELECT remember_token, password_hash, userid, reset_lock FROM bbank_users where email_id='$email_id'";
  $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed.");
  $data = mysqli_fetch_assoc($result);
  if (mysqli_num_rows($result) >= 1) {
    if ($data['reset_lock'] == 1)
      $rows['opts']['msg'] = 'An email has already been sent to ' . $email_id.'.';
    else {
      date_default_timezone_set("Asia/Dhaka");
      $today = date("Y-m-d H:m:s");
      $token = $data['remember_token'];
      $phash = $data['password_hash'];
      ini_set("include_path", ini_get("include_path") . ':.:/usr/local/share/pear/');
      // ini_set("include_path", '/home/realmg/php:' . ini_get("include_path").':.:/usr/local/share/pear/');
      // set_include_path(); 
      require_once "Mail.php";

      $from = "sajib.mitra@bb.org.bd";
      $to = $email_id;

      $host = "mail.bb.org.bd";
      $username = 'sajib';
      $password = 'Megamind@1985';
      $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . "?token=" . $token . '&Acs=' . $phash;
      $subject = "Web Drive Password Recovery";
      $body = "Welcome to Web Drive. Click following link to reset your password.\n\n" . $link . "\n\nBest Regards\nICTIMMD, Head Office, Bangladesh Bank.";
      $headers = array('From' => $from, 'To' => $to, 'Subject' => $subject);
      $smtp = Mail::factory(
        'smtp',
        array(
          'host' => $host,
          'port' => '587',
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
        $rows['opts']['msg'] = $mail->getMessage(); // 'Email has not been delivered successfully.';
      } else {
        $rows['opts']['status'] = true;
        $sql = "UPDATE bbank_users set reset_lock = 1 where email_id='$email_id' and remember_token = '$token'";
        mysqli_query($con, $sql) or die("Data could not be updated into user table." . $sql);
        $rows['opts']['msg'] = '<div class="loginInputFieldStyle" >A password reset link has been sent to ' . $email_id . '.</div><div class="loginInputFieldStyle" ><span onClick="logintoHelpdesk()" class="backlinks">Back to Login</span></div>';
      }
    }
  }
}
$rows['errors'] = $errors;
$rows['errors']['total'] = count($errors['key']);
echo json_encode($rows);
$session->closeDBConnection($con);
