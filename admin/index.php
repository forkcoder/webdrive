<?php
session_start();
include("../header.php");
$con = $session->initDBConnection();
$session->closeDBConnection($con);
if (isset($_SESSION['bbank_userid']) == false || $_SESSION['bbank_userid'] == '') {
  $session->removeSession();
  header("location: index.php");
  die();
} else {

  



}
include("../footer.php");
