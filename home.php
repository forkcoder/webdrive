<?php
session_start();
include("header.php");
$con = $session->initDBConnection();
$session->reloadSession($con);
$session->closeDBConnection($con);
if (isset($_SESSION['fcoder_userid']) == false || $_SESSION['fcoder_userid'] == '') {
  $session->removeSession();
  header("location: index.php");
  die();
} else {
  print '<div id="mainContentDiv">
    <div class="general-main-form" id="body-webdriveModule"></div>
    </div>
    <div id="previewPane" class="supermodal" onclick="event.stopPropagation();exitSuperModal()">
    <div class="modal" id="wdrive-modal-content">
    </div>
  </div>';
  print  '<script type="text/javascript"> window.onload = function() {webdriveModule.init();}; </script>';
  include("footer.php");
}
