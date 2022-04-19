<?php
session_start();
$lk = isset($_SESSION['login_key']) ? $_SESSION['login_key'] : uniqid();
if ($_GET['auth_ph'] == "" || $_GET['auth_ph'] != $lk) {
	header("Location: /index.php");
	die();
} else if ($_GET['auth_ph'] == $_SESSION['login_key']) {
	if (!isset($_SESSION['bbank_userid'])) echo 0;
	else {
		$sxid = uniqid();
		$_SESSION['session_key'] = $sxid;
		$res = "1" . $sxid;
		echo $res;
	}
}
?>