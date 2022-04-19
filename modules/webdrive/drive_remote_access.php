<?php
session_start();
require('../Servlets.php');
$session = new DBProxy();
$con = $session->initDBConnection();
$data = array();
$data['login_at'] = $login_at = $_POST['login_at'];
$data['userid'] = $userid = $_POST['userid'];
$data['client_ipaddress'] =$client_ipaddress = $_POST['client_ipaddress'];

$sql = "SELECT * from `bbank_access_log` where client_ipaddress = '$client_ipaddress' and u_id='$userid' and login_at='$login_at' and access_status='login'";
$result = mysqli_query($con, $sql) or die ("Could not fetch to access log");

if(mysqli_num_rows($result)==1)
  echo true;
else
  echo false;
$session->closeDBConnection($con);
?>
