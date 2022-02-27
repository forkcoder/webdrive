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
  $nid_reg = $_SESSION['fcoder_userid'];
  print '<div id="mainContentDiv">
    <div class="general-main-form" id="body-webdriveModule"></div>
    </div>
    <div id="previewPane" class="supermodal" onclick="event.stopPropagation();exitSuperModal()">
    <div class="modal" >
      <div style="display:flex;flex-direction:column;align-items:center;" >
      <span id="wdrive-modal-caption"></span>
      <div id="wdrive-modal-content" class="modal-content" onclick="event.stopPropagation();" style="position:relative"></div>
      </div>
    </div>
  </div>';
  include("footer.php");
  echo '<script type="text/javascript"> window.onload = function() {webdriveModule.init();}; </script>';
}
