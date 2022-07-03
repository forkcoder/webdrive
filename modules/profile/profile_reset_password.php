<?php
session_start();
require('../Servlets.php');
$session = new DBProxy();
if (isset($_SESSION['fcoder_access_id']) == false || isset($_SESSION['fcoder_email_id']) == false) {
	header("Location: /index.php");
	die();
} else {
	date_default_timezone_set("Asia/Dhaka");
	$token = $_POST['unique-token-id'];
	$email_id = $_SESSION['fcoder_email_id'];
	$password = $_POST['userinfo_password'];
	$confirm_password = $_POST['userinfo_confirm_password'];

	$con = $session->initDBConnection();
	$rows = array();
	$rows['errors'] = array();
	$rows['opts']['status'] = false;
	$errors['key'] = array();
	if (strlen($password) < 7) {
		$errors['key'][] = 'userinfo_password';
		$errors['msg'][] = 'Minimum length of password is 8.';
	} else if ($password != $confirm_password) {
		$errors['key'][] = 'userinfo_confirm_password';
		$errors['msg'][] = 'Password missmatched';
	}
	if (count($errors['key']) == 0) {
		$sql = "SELECT id, contact_no, email_id, userid FROM fcoder_users where reset_lock =1 and email_id='$email_id' and remember_token='$token' limit 1";
		$result = mysqli_query($con, $sql) or die("Fetching users from DB is failed ");
		if (mysqli_num_rows($result) == 1) {
			$data = mysqli_fetch_assoc($result);
			$id = $data['id'];
			$userid = $data['userid'];
			$today = date("Y-m-d H:m:s");
			//Update password for reset 
			$password = password_hash($confirm_password, PASSWORD_BCRYPT, ['cost' => 12]);
			$sql = "UPDATE fcoder_users  set password_hash='$password', updated_at='$today', reset_lock=0 where  id=$id and email_id='$email_id'";
			$result = mysqli_query($con, $sql) or die("Update user to DB is failed");
			
			if ($session->login($con, $userid, $confirm_password, 'login') == 0) {
				$rows['opts']['status'] = true;
			} else{
				$rows['opts']['msg'] = 'Password Reset Error. Please Contact with Site Administrator.';
			}
		} else
			$rows['opts']['msg'] = 'Failed to reset Password. Please Contact with site Administrator.';
	}
	$rows['errors'] = $errors;
	$rows['errors']['total'] = count($errors['key']);
	echo json_encode($rows);
	$session->closeDBConnection($con);
}
