<?php
session_start();
require('modules/Servlets.php');
$session = new DBProxy();
$session->reloadSession();
if (isset($_SESSION['fcoder_userid'])==false || $_SESSION['fcoder_userid'] == '') {
    $session->removeSession();
    header("location: index.php");
    die();
  } else {
  $nid_reg = $_SESSION['fcoder_userid'];
    include("header.php");
    print '<div id="mainContentDiv">
    <div class="general-main-form" id="web-drive-table">
        <div id="webDriveDiv" class="notextselect"></div>
    </div>';
    include("footer.php");
    echo '<script type="text/javascript"> window.onload = function() {webdriveModule.init();}; </script>';
}
?>

