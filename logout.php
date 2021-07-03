<?php
session_start();
require('modules/Servlets.php');
$session = new DBProxy();

$_SESSION['login_key'] = $_GET['auth_ph'];
unset($_SESSION['login_key']);
$con = $session->initDBConnection();

$session->removeSession();

session_destroy();
$res = 1;
if(isset($_SESSION['mgmt_userid'])){
$u_userid = $_SESSION['mgmt_userid'];
$login_at = str_replace($u_userid, "", $_SESSION['access_key']);

$sql = "UPDATE `hdesk_access_log` SET logout_at=NOW(), access_status='logout' where u_id='$u_userid' and login_at='$login_at' and access_status='login'";
mysqli_query($con, $sql) or die("could not inserted to access log");
}

echo $res;
$session->closeDBConnection($con);
