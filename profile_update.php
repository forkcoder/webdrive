<?php
session_start();
require('modules/Servlets.php');
$session = new DBProxy();
if ($_GET['auth_ph']!=$_SESSION['login_key'] ||  isset($_SESSION['bbank_remember_token'])== false) {
	header("Location: /index.php");
	die();
} else {
	date_default_timezone_set("Asia/Dhaka");
	$name = $_REQUEST['name'];
	$userid = $_REQUEST['userid'];
	$role = 'User';
	$actype = $_REQUEST['actype'];
	$contactno = $_REQUEST['contact_no'];
	$email_id = $_REQUEST['email_id'];
	$password = $_REQUEST['password'];
	$confirm_password = $_REQUEST['confirm_password'];
	$token = $_SESSION['bbank_remember_token'];

	$con = $session->initDBConnection();
	$rows = array();
	$rows['opts']['success'] = false;
	$faultyData = true;
	if ($actype == 'Business' || $actype == 'Education'){
		if (strlen($name) > 0 && strlen($name) < 100 && !preg_match("/[\'£$%^&*}{@:\'#~?><>,;@\|\\=+¬\`]/", $name)) {
			if (strlen($userid) > 4 && strlen($userid) < 100 && preg_match("/^[a-z\d_.]{2,20}$/i", $userid)) {
				$sql = "SELECT id FROM bbank_users where userid='$userid' and remember_token!='$token'";
				$result = mysqli_query($con, $sql) or die("Fetching users from DB is failed ");
				if (mysqli_num_rows($result) == 0) {
					if ($contactno != "" && strlen($contactno) > 5 && strlen($contactno) < 15 && preg_match("/^[0-9\-\+]*$/", $contactno)) {
						if ($email_id != "" && filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
							if(strlen($password)>7){
								if($password != $confirm_password){
									$rows['opts']['msg'] = 'Password missmatched is 8.';
								}
								else
								$faultyData = false;
							}
							else 
							$rows['opts']['msg'] = 'Minimum length of password is 8.';
						}
						else
						$rows['opts']['msg'] = 'Your Email ID is Invalid. Try Again.';
					} else
						$rows['opts']['msg'] = 'Given Contact Number is not in correct format.';
				} else
					$rows['opts']['msg'] = 'Given Userid has been used already. Try Again.';
			} else
				$rows['opts']['msg'] = 'Given User Id is not in correct format. Example: sajib.mitra';
		} else{
			if($name=="")
			$rows['opts']['msg'] = 'Given name is either empty.';
			else 
			$rows['opts']['msg'] = 'Given name is not in correct format. Example: Sajib Mitra, etc.';
		}
	} else
		$rows['opts']['msg'] = 'Given Account Type is not Correct. Try Again.';
	$genid = time();// $_SESSION['bbank_genid'];

	if ($faultyData == false) {
		$token = $_SESSION['bbank_remember_token'];
		$sql = "SELECT id FROM bbank_users where remember_token='$token' and email_id='$email_id'";
		$result = mysqli_query($con, $sql) or die("Fetching users from DB is failed ");
		if (mysqli_num_rows($result) == 1 && isset($_SESSION['bbank_userid']) == false) {
			$data = mysqli_fetch_assoc($result);
			$id = $data['id'];
			$password =password_hash($confirm_password, PASSWORD_BCRYPT,['cost'=>12] );
			$gspace = $_SESSION['clientInfo']['country'];
			$lspace = $_SESSION['clientInfo']['city'];
			$today = date("Y-m-d H:m:s");
			$sql = "UPDATE bbank_users  set gspace= '$gspace', lspace='$lspace', role='$role', name='$name', userid='$userid', contact_no='$contactno', updated_at = '$today' , userid='$userid', actype='$actype', password_hash='$password' , genid ='$genid' where  id=$id and email_id='$email_id'";
			$result = mysqli_query($con, $sql) or die("Update user to DB is failed");
		
			$login_at = date('Y-m-d H:i:s', time());
			$client_browser = $_SESSION['clientInfo']['name'];
			$client_version = $_SESSION['clientInfo']['version'];
			$client_ipaddress = $_SESSION['clientInfo']['ipaddress'];
			$client_hostname = $_SESSION['clientInfo']['hostname'];
			$client_platform = $_SESSION['clientInfo']['platform'];
			$geo_gspace = $geo_country  = $_SESSION['clientInfo']['country'];
			$geo_city = $_SESSION['clientInfo']['city'];
			$geo_latitude = $_SESSION['clientInfo']['latitude']?:0.0;
			$geo_longitude = $_SESSION['clientInfo']['longitude']?:0.0;
			$geo_currency = $_SESSION['clientInfo']['currency'];
			$geo_currencycode = $_SESSION['clientInfo']['currencycode'];
			$geo_timezone = $_SESSION['clientInfo']['timezone'];
			$login_key = $_SESSION['login_key'];
			$sql = "INSERT INTO `bbank_access_log` (`u_id`,`genid`,`login_at`,`access_status`, `client_browser`,`client_version`, `client_ipaddress`,`client_hostname`,`client_platform`, `geo_gspace`, `geo_country`, `geo_city`, `geo_latitude`, `geo_longitude`, `geo_currency`, `geo_currencycode`, `geo_timezone`,`login_key` )
			VALUES('$userid','$genid','$login_at','login','$client_browser','$client_version', '$client_ipaddress','$client_hostname','$client_platform', '$geo_gspace', '$geo_country', '$geo_city', $geo_latitude, $geo_longitude, '$geo_currency', '$geo_currencycode', '$geo_timezone','$login_key')";
			mysqli_query($con, $sql) or die("could not inserted to access log");
			$_SESSION['access_key'] = $userid . $login_at;
			$_SESSION['bbank_email_id']= $email_id;
			$_SESSION['bbank_userid'] = $userid;
			unset($_SESSION['bbank_remember_token']);
			$rows['opts']['success'] = true;
			$rows['opts']['msg'] = 'User info has been Updated successfully.';
		} else
			$rows['opts']['msg'] = 'Failed to Update Your Information. Please Contact with site Administrator..';
	}
	echo json_encode($rows);
	$session->closeDBConnection($con);
}
