<?php
session_start();
require('../Servlets.php');
$session = new DBProxy();
if ($session->validate($_GET['auth_ph'], $_GET['ph']) == false) {
  header("Location: /index.php");
  die();
} else {

  $con = $session->initDBConnection();
  $data = array();
  $data['opts']['status'] = false;
  $u_genid = $_SESSION['bbank_genid'];
  $u_wshare_access = $_SESSION['bbank_wshare_access'];
  $shareWith = '';
  $users = array();
  $users['ids'] = array();
  $count_user = 0;
  if ($u_wshare_access == 1) {
    $sql = "SELECT id, name, email_id, userid, avater_count, contact_no, genid from bbank_users where role!='Inactive' and genid!='$u_genid' order by name desc";
    $result = mysqli_query($con, $sql) or die("User info could not be fetched.");
    while ($r = mysqli_fetch_assoc($result)) {
      $genid = $r['genid'];
      $users['ids'][] = $genid;
      $users[$genid] = $r;
      $count_user++;
    }
    $users['ids']['total'] = $count_user;
    $data['users'] = $users;

    $data['opts']['ul'] = $_SESSION['bbank_upload_limit'];
    $data['opts']['tu'] = $_SESSION['bbank_total_uploads'];
    $data['opts']['tr'] = $_SESSION['bbank_total_recipients'];

    $shareWith .= '<div class="hd-fcct">
    <div class="hd-fcss" style="align-items:stretch">
      <div class="wd-title-list-header">
      List of selected File(s)
      </div>
      <div id="existing-sharewith-id" class="general-scroll-bar-style"></div>
      </div>
    <div class="hd-fcss" style="align-items:stretch">
      <div class="wd-title-list-header"  class="hd-frsc">
      <img id="add-sharewith-img-id" style="height:24px;width:24px;margin:5px" onclick="webdriveModule.discardShare()" src="images\\webdrive\\sharewith.png">
      <input type="text" placeholder="Type Name, SAP ID or Contact Number" class="bbank-card-input-text" id="wdrive_share_with_input" autocomplete="off" style="border: 1px solid lightgray"
      onkeydown="webdriveModule.pressEnter(event);"  onkeyup="webdriveModule.filterUsers();"/>
      <span onclick="webdriveModule.clearSearchUsers();" class="clearButton" > Clear </span>
      <span>Users: <span id="wd-count-totalusers">0</span></span>
      </div>
      <div id="wd-user-list-id" class="general-scroll-bar-style" ></div>
      </div>
      </div>';
  } else {
    $users['ids']['total'] = $count_user;
    $data['users'] = $users;
  }
  $data['shareWithID'] = $shareWith;
  $data['opts']['status'] = true;
  $data['app_storage'] = getenv('APP_WDPROXY_SVR');
  $data['bytes_per_chunk'] = (int)getenv('BYTES_PER_CHUNK');

  $data['opts']['key'] = getenv('APP_CODEBASE_SVR') . ' ' . $_SESSION['access_key'];
  $data['opts']['previewpath'] = "web_drive\\";
  echo json_encode($data);
  $session->closeDBConnection($con);
}
