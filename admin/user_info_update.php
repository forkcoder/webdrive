<?php
session_start();
require('../modules/Servlets.php');
$session = new DBProxy();
if (($session->validate($_REQUEST['auth_ph'], $_REQUEST['ph']) === true) || isset($_SESSION['fcoder_hadmin_access'])==false || $_SESSION['fcoder_hadmin_access'] != 1 || $_SESSION['fcoder_userid']=='') {
	header("Location: /index.php");
	die();
} else {
	date_default_timezone_set("Asia/Dhaka");
	$token = $_POST['unique-token-id'];
	$name = $_POST['userinfo_name'];
	$userid = $_POST['userinfo_userid'];
	$role = 'User';
	$actype = $_POST['userinfo_actype'];
	$contactno = $_POST['userinfo_contact_no'];
	$email_id = $_POST['userinfo_email_id'];
	$gspace = $_POST['userinfo_gspace'];
	$lspace = $_POST['userinfo_lspace'];
	$con = $session->initDBConnection();
	$rows = array();
	$rows['errors'] = array();
	$errors['key'] = array();

	$rows['opts']['status'] = false;
	if ($actype != 'Business' && $actype != 'Education') {
		$errors['key'][] = 'userinfo_actype';
		$errors['msg'][] = 'Given Account Type is not Correct.';
	}
	if (strlen($name) >= 100) {
		$errors['key'][] = 'userinfo_name';
		$errors['msg'][] = 'Given Name must be less than 100 characters.';
	} else if (strlen($name) == 0) {
		$errors['key'][] = 'userinfo_name';
		$errors['msg'][] = 'Given name is empty.';
	} else if (strlen($name) > 0 && strlen($name) < 100 && preg_match("/[\'£$%^&*}{@:\'#~?><>,;@\|\\=+¬\`]/", $name)) {
		$errors['key'][] = 'userinfo_name';
		$errors['msg'][] = 'Given name is not in correct format. Example: Sajib Mitra, etc.';
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
		$errors['msg'][] = 'Given User Id is not in correct format. Example: sajib.mitra';
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
			if (filter_var($email_id, FILTER_VALIDATE_EMAIL) == false) {
				$errors['key'][] = 'userinfo_email_id';
				$errors['msg'][] = 'Your Email ID is Invalid.';
			}
		}
	}
	$genid = time();

	$rows['errors'] = $errors;
	if (count($errors['key']) == 0) {
		$rows['errors']['total'] = 0;
		$sql = "SELECT id, userid FROM fcoder_users where remember_token='$token' limit 1";
		$result = mysqli_query($con, $sql) or die("Fetching users from DB is failed ");
		if (mysqli_num_rows($result) == 1) {
			$today = date("Y-m-d H:m:s");
			$sql = "UPDATE fcoder_users  set gspace= '$gspace', lspace='$lspace', role='$role', name='$name', contact_no='$contactno', actype='$actype', userid='$userid', email_id='$email_id'  , updated_at='$today' where  remember_token='$token'";
			$result = mysqli_query($con, $sql) or die("Update user to DB is failed".$sql);
			$rows['opts']['status'] = true;
			$rows['opts']['msg'] = 'User Info has been updated Successfully.';
		} else
			$rows['opts']['msg'] = 'Failed to Update Your Information. Please Contact with site Administrator..';
	} else
		$rows['errors']['total'] = count($errors['key']);
	echo json_encode($rows);
	$session->closeDBConnection($con);
}
