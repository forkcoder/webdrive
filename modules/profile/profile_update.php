<?php
session_start();
require('../Servlets.php');
$session = new DBProxy();
if ($_REQUEST['auth_ph'] != $_SESSION['login_key'] ||  isset($_SESSION['fcoder_remember_token']) == false) {
	header("Location: /index.php");
	die();
} else {
	date_default_timezone_set("Asia/Dhaka");
	$token = $_SESSION['fcoder_remember_token'];
	$email_id = $_SESSION['fcoder_email_id'];

	$name = $_POST['userinfo_name'];
	$userid = $_POST['userinfo_userid'];
	$gspace = $_POST['userinfo_gspace'];
	$lspace = $_POST['userinfo_lspace'];
	$role = 'User';
	$actype = $_POST['userinfo_actype'];
	$contactno = $_POST['userinfo_contact_no'];
	if (isset($_POST['userinfo_password_current']))
		$current_password = $_POST['userinfo_password_current'];
	else $current_password = '';
	$password = $_POST['userinfo_password'];
	$confirm_password = $_POST['userinfo_confirm_password'];
	$con = $session->initDBConnection();
	$rows = array();
	$rows['errors'] = array();
	$errors['key'] = array();

	$rows['opts']['status'] = false;
	if ($actype != 'Business' && $actype != 'Education') {
		$errors['key'][] = 'userinfo_actype';
		$errors['msg'][] = 'Given Account Type is not Correct.';
	}
	if (strlen($name) == 0) {
		$errors['key'][] = 'userinfo_name';
		$errors['msg'][] = 'Given name is empty.';
	} else if (strlen($name) > 0 && strlen($name) < 100 && preg_match("/[\'£$%^&*}{@:\'#~?><>,;@\|\\=+¬\`]/", $name)) {
		$errors['key'][] = 'userinfo_name';
		$errors['msg'][] = 'Given name is not in correct format.';
	}
	if (strlen($gspace) >= 100) {
		$errors['key'][] = 'userinfo_gspace';
		$errors['msg'][] = 'Given Employer Name must be lesser than 100 characters.';
	} else if (strlen($gspace) > 0 && strlen($gspace) < 100 && preg_match("/[\'£$%^&*}{@:\'#~?><>,;@\|\\=+¬\`]/", $gspace)) {
		$errors['key'][] = 'userinfo_gspace';
		$errors['msg'][] = 'Given Employer is not in correct format.';
	}

	if (strlen($lspace) >= 100) {
		$errors['key'][] = 'userinfo_lspace';
		$errors['msg'][] = 'Given Department Name must be lesser than 100 characters.';
	} else if (strlen($lspace) > 0 && strlen($lspace) < 100 && preg_match("/[\'£$%^&*}{@:\'#~?><>,;@\|\\=+¬\`]/", $lspace)) {
		$errors['key'][] = 'userinfo_lspace';
		$errors['msg'][] = 'Given Department is not in correct format.';
	}
	if (strlen($userid) == 0) {
		$errors['key'][] = 'userinfo_uderid';
		$errors['msg'][] = 'Given User ID is empty.';
	} else if (strlen($userid) <= 4 && strlen($userid) >= 100) {
		$errors['key'][] = 'userinfo_uderid';
		$errors['msg'][] = 'Given User must contain more than four(4) characters.';
	} else if (!preg_match("/^[a-z\d_.]{2,20}$/i", $userid)) {
		$errors['key'][] = 'userinfo_uderid';
		$errors['msg'][] = 'Given User Id is not in correct format.';
	} else {
		$sql = "SELECT id, contact_no, email_id, userid FROM fcoder_users where (userid='$userid' || contact_no='$contactno' || email_id='$email_id') and remember_token!='$token'";
		$result = mysqli_query($con, $sql) or die("Fetching users from DB is failed ");
		$rows['sql'] = $sql;
		if (mysqli_num_rows($result) != 0) {
			$data =  array();
			while ($r = mysqli_fetch_assoc($result)) {
				$data['emails'][] = $r['email_id'];
				$data['contacts'][] = $r['contact_no'];
				$data['userids'][] = $r['userid'];
			}
			if (in_array($userid, $data['userids'])) {
				$errors['key'][] = 'userinfo_uderid';
				$errors['msg'][] = 'Given Userid has been used already.';
			}
			if (in_array($email_id, $data['emails'])) {
				$errors['key'][] = 'userinfo_email_id';
				$errors['msg'][] = 'Given Email has been used already.';
			}
			if (in_array($contactno, $data['contacts'])) {
				$errors['key'][] = 'userinfo_contact_no';
				$errors['msg'][] = 'Given Contact No has been used already.';
			}
		} else {

			if ($contactno == "") {
				$errors['key'][] = 'userinfo_contact_no';
				$errors['msg'][] = 'Given Contact Number is empty.';
			} else if (strlen($contactno) <= 5 && strlen($contactno) >= 15) {
				$errors['key'][] = 'userinfo_contact_no';
				$errors['msg'][] = 'Given Contact Number must contain more than 5 digits.';
			} else if (!preg_match("/^[0-9\-\+]*$/", $contactno)) {
				$errors['key'][] = 'userinfo_contact_no';
				$errors['msg'][] = 'Given Contact Number is not in correct format.';
			}
			if (isset($_POST['userinfo_password_current'])) {
				if ($current_password == '') {
					$errors['key'][] = 'userinfo_password_current';
					$errors['msg'][] = 'Current Password must be Given.';
				} else {
					if (strlen($password) > 0 || strlen($confirm_password) > 0) {
						if (strlen($password) < 7) {
							$errors['key'][] = 'userinfo_password';
							$errors['msg'][] = 'Minimum length of password is 8.';
						} else if ($password != $confirm_password) {
							$errors['key'][] = 'userinfo_confirm_password';
							$errors['msg'][] = 'Password missmatched';
						}
					}
				}
			} else {
				if (strlen($password) < 7) {
					$errors['key'][] = 'userinfo_password';
					$errors['msg'][] = 'Minimum length of password is 8.';
				} else if ($password != $confirm_password) {
					$errors['key'][] = 'userinfo_confirm_password';
					$errors['msg'][] = 'Password missmatched';
				}
			}
		}
	}
	$genid = time();

	
	if (count($errors['key']) == 0) {
		$token = $_SESSION['fcoder_remember_token'];
		$sql = "SELECT id, userid, password_hash FROM fcoder_users where remember_token='$token' and email_id='$email_id'";
		$result = mysqli_query($con, $sql) or die("Fetching users from DB is failed ");
		if (mysqli_num_rows($result) == 1) {
			$data = mysqli_fetch_assoc($result);
			$id = $data['id'];
			if ((isset($_SESSION['fcoder_userid']) == true && $data['userid'] == $_SESSION['fcoder_userid']) || (isset($_SESSION['fcoder_userid']) == false && $data['userid'] == '')) {

				$today = date("Y-m-d H:m:s");
				//Update first time for registration
				$loginStat = 3;
				if (isset($_SESSION['fcoder_userid']) == false && $data['userid'] == '') {
					$password = password_hash($confirm_password, PASSWORD_BCRYPT, ['cost' => 12]);
					$sql = "UPDATE fcoder_users  set gspace= '$gspace', lspace='$lspace', role='$role', name='$name', userid='$userid', contact_no='$contactno', actype='$actype', password_hash='$password', updated_at='$today', genid ='$genid' where  id=$id and email_id='$email_id'";
					$result = mysqli_query($con, $sql) or die("Update user to DB is failed");
					$loginStat = $session->login($con, $userid, $confirm_password, 'login');
					if ($loginStat == 0) {
						$rows['opts']['status'] = true;
					} else if ($loginStat == 1) {
						$rows['opts']['msg'] = 'Password Mismatch Error. Please Contact with Site Administrator.';
					} else if ($loginStat == 2) {
						$rows['opts']['msg'] = 'User ID is not Exist. Please Contact with Site Administrator.';
					} else {
						$rows['opts']['msg'] = 'Invalid User ID.  Please Contact with Site Administrator.';
					}
				}
				//Updated data 
				else if (isset($_POST['userinfo_password_current'])) {
					if (password_verify($_POST['userinfo_password_current'], $data['password_hash'])) {
						if ($password != '') {
							$password = password_hash($confirm_password, PASSWORD_BCRYPT, ['cost' => 12]);
							$sql = "UPDATE fcoder_users  set gspace= '$gspace', lspace='$lspace', role='$role', name='$name', contact_no='$contactno', actype='$actype'  , password_hash='$password', updated_at='$today' where  id=$id and email_id='$email_id'";
						} else {
							$sql = "UPDATE fcoder_users  set gspace= '$gspace', lspace='$lspace', role='$role', name='$name', contact_no='$contactno', actype='$actype' , updated_at='$today' where  id=$id and email_id='$email_id'";
						}
						$result = mysqli_query($con, $sql) or die("Update user to DB is failed");
						$loginStat = $session->login($con, $userid, $_POST['userinfo_password_current'], 'update');
						$rows['opts']['status'] = true;
					} else {
						$errors['key'][] = 'userinfo_password_current';
						$errors['msg'][] = 'Given Password is Incorrect.';
					}
				} else {
					$errors['key'][] = 'userinfo_password_current';
					$errors['msg'][] = 'Given Password is Incorrect.';
				}
			} else if (isset($_SESSION['fcoder_userid']) == true && $data['userid'] != $_SESSION['fcoder_userid']) {
				$rows['opts']['msg'] = 'Unauthorized Access to Information Update.';
			} else {
				$rows['opts']['msg'] = 'Token has been used already. Please Contact with site Administrator..';
			}
		} else
			$rows['opts']['msg'] = 'Failed to Update Your Information. Please Contact with site Administrator.' . $sql;
	}
	$rows['errors'] = $errors;
	$rows['errors']['total'] = count($errors['key']);
	echo json_encode($rows);
	$session->closeDBConnection($con);
}
