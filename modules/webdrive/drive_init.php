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
  $u_genid = $_SESSION['fcoder_genid'];
  $u_wshare_access = $_SESSION['fcoder_wshare_access'];
  $shareWith = '';
  $users = array();
  $users['ids'] = array();
  $count_user = 0;
  if ($u_wshare_access == 1) {
    $sql = "SELECT id, name, email_id, userid, avater_count, contact_no, genid from fcoder_users where role!='Inactive' and genid!='$u_genid' order by name desc";
    $result = mysqli_query($con, $sql) or die("User info could not be fetched.");
    while ($r = mysqli_fetch_assoc($result)) {
      $genid = $r['genid'];
      $users['ids'][] = $genid;
      $users[$genid] = $r;
      $count_user++;
    }
    $users['ids']['total'] = $count_user;
    $data['users'] = $users;

    $data['opts']['ul'] = $_SESSION['fcoder_upload_limit'];
    $data['opts']['tu'] = $_SESSION['fcoder_total_uploads'];
    $data['opts']['tr'] = $_SESSION['fcoder_total_recipients'];

    $shareWith .= '<div  id="shareWithID"  onclick="event.stopPropagation();">
      <div class="hd-fcsc" style="height:100%;width:100%">
      <div id="wd-user-list-header"  class="hd-frsc" style="background-color:whitesmoke;height:25px;width:100%; cursor:pointer;flex-shrink: 0;">
      <img id="wd-user-list-move" style="height:16px;width:16px;margin:5px;cursor:move" title="Move Window" src="images\\webdrive\\movewindow.png">
      <img id="add-sharewith-img-id" style="height:16px;width:16px;margin:5px" onclick="webdriveModule.discardShare()" src="images\\webdrive\\sharewith.png">
      <input type="text" placeholder="Type Name, SAP ID or Contact Number" class="fcoder-card-input-text" id="wdrive_share_with_input" autocomplete="off" style="border: 1px solid lightgray"
      onkeydown="webdriveModule.pressEnter(event);"  onkeyup="webdriveModule.filterUsers();"/>
      <span onclick="webdriveModule.clearSearchUsers();" class="clearButton" > Clear </span>
      <span style="max-width:180px;min-width:180px;"> List of Selected File(s) </span>
      <div class="imageButtonStyle" id="wd-share-btn" onclick="webdriveModule.shareReq()"><span id="wd-share-btn-text">Share</span><img src="images\\webdrive\\sendBtnImg.png"></div>
      </div>
      <div class="hd-frss" style="width:100%;">
      <div id="wd-user-list-id" class="general-scroll-bar-style" ></div>
      <div id="existing-sharewith-id" class="general-scroll-bar-style"></div>
      </div>
      </div>
      </div>';
  } else {
    $users['ids']['total'] = $count_user;
    $data['users'] = $users;
  }
  $data['shareWith'] = $shareWith;
  $data['opts']['status'] = true;
  $data['app_storage'] = getenv('APP_WDPROXY_SVR');
  $data['bytes_per_chunk'] = (int)getenv('BYTES_PER_CHUNK');

  $data['opts']['key'] = getenv('APP_CODEBASE_SVR') . ' ' . $_SESSION['access_key'];
  $data['opts']['previewpath'] = "web_drive\\";
  echo json_encode($data);
  $session->closeDBConnection($con);
}
