<?php
session_start();
include("../header.php");
$con = $session->initDBConnection();
$session->closeDBConnection($con);
if (isset($_SESSION['fcoder_userid']) == false || $_SESSION['fcoder_userid'] == '') {
  $session->removeSession();
  header("location: index.php");
  die();
} else {

  



}
include("../footer.php");
