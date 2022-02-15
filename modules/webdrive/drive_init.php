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
  $u_office = $_SESSION['fcoder_gspace'];
  $u_genid = $_SESSION['fcoder_genid'];
  $u_wstorage_limit = $_SESSION['fcoder_wstorage_limit'];
  $u_wshare_access = $_SESSION['fcoder_wshare_access'];
  
  $createUpload = '';
  $tdata = '<div id="webDriveDiv" class="notextselect"  onClick="event.stopPropagation();webdriveModule.dismissAll();">';
  if ($_SESSION['fcoder_wdrive_access'] == 1 ) {
    $_SESSION['active_tab']='webdriveModule';
    $tdata .= '<div id="webDriveLeftDiv">
    <div id="webDriveTree" class="general-scroll-bar-style"><img src="images\\loader.gif" style="width:20px;height:20px;align-self:center"></div>
    </div>';
    // $createUpload .= '<div class="hd-rldv" style="width:100%;">
    // <div id="createNewFolderID">
    // <img id="new-folder-img-id" onclick="webdriveModule.createNew()" src="images\\webdrive\\createnew.png">
    // <div id="new-folder-name-id" style="display:flex; flex-direction:column;justify-content:flex-end;visibility: hidden;opacity:0; transition:visibility 0s, opacity .7s linear"><div style="align-self:flex-start; margin-left:5px"> Press Enter to Create </div><input type="text" style="border:1px solid skyblue; width:150px;" class="roundCornerInput" onkeydown="webdriveModule.pressEnter(event, \'mkdir\')" id="wdrive_create_input" maxlength="50"></div>
    // </div>';
    $createUpload .= '';
    $users = array();
    $users['ids'] = array();
    $count_user = 0;
    if ( $u_wshare_access == 1) {
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

      $createUpload .= '<div  id="shareOrSendWithID"  onclick="event.stopPropagation();">
      <div class="hd-fcsc" style="height:100%;width:100%">
      <div id="wd-user-list-header"  class="hd-frsc" style="background-color:whitesmoke;height:25px;width:100%; cursor:pointer;flex-shrink: 0;">
      <img id="wd-user-list-move" style="height:16px;width:16px;margin:5px;cursor:move" title="Move Window" src="images\\webdrive\\movewindow.png">
      <img id="add-sharewith-img-id" style="height:16px;width:16px;margin:5px" onclick="webdriveModule.discardShareOrSend()" src="images\\webdrive\\sharewith.png">
      <input type="text" placeholder="Type Name, SAP ID or Contact Number" class="fcoder-card-input-text" id="wdrive_share_with_input" autocomplete="off" style="border: 1px solid lightgray"
      onkeydown="webdriveModule.pressEnter(event);"  onkeyup="webdriveModule.filterUsers();"/>
      <span onclick="webdriveModule.clearSearchUsers();" class="clearButton" > Clear </span>
      <span style="max-width:180px;min-width:180px;"> List of Selected File(s) </span>
      <div class="imageButtonStyle" id="wd-send-share-btn" onclick="webdriveModule.sendShareREQ()"><span id="wd-send-share-btn-text">Share</span><img src="images\\webdrive\\sendBtnImg.png"></div>
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

    // <div style="position:absolute;left:0;top:100%">
    // <span id="wdrive-newfileordir-create-id" style="min-width:60px;" onclick="webdriveModule.menuActHandler(\'mkdir\')" class="menuButton" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\createnew.png"> New </span>
    // <span id="wdrive-newfileordir-create-id" style="min-width:60px;" onclick="webdriveModule.menuActHandler(\'mkdir\')" class="menuButton" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\createnew.png"> New </span>
    // </div>
    $tdata .= '<div id="webDriveRightDiv">
    <div id="webDriveMenus" onclick="event.stopPropagation()">
    <span class="menuButton"  style="min-width:30px;vertical-align:middle" id="wdrive-grid-list-id" onclick="webdriveModule.listView(this)"><img style="height:20px;"  src="images\\webdrive\\list.png"></span>
    <span class="menuButton"  style="min-width:20px;" id="wdrive-back-btn-id" onclick="webdriveModule.backDir()"><img id="wdrive-back-img-id" style="height:20px;vertical-align:middle" src="images\\webdrive\\backinactive.png"></span>
    <span class="menuButton" style="min-width:20px"  id="wdrive-up-btn-id" onclick="webdriveModule.upDir()"><img id="wdrive-up-img-id" style="height:20px;vertical-align:middle"  src="images\\webdrive\\upinactive.png"></span>
    <div style="position:relative" >
    <span style="min-width:60px;" onclick="webdriveModule.createNew()" class="menuButton wdrive-menu-button" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\createnew.png"> New </span>
    </div>
    <div id="wdrive-singlefileordir-menu-id" class="wdrive-group-menu-style" >
    <span id="wdrive-singlefileordir-rename-id" onclick="webdriveModule.menuActHandler(\'rename\')" class="menuButton" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\rename.png"> Rename </span>
    </div>
    <div id="wdrive-common-menu-id" class="wdrive-group-menu-style" >
    <span id="wdrive-common-copy-id" style="min-width:55px;"  onclick="webdriveModule.menuActHandler(\'copy\')" class="menuButton  wdrive-menu-button" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\copy.png"> Copy </span>
    <span id="wdrive-common-download-id" onclick="webdriveModule.menuActHandler(\'download\')" class="menuButton wdrive-menu-button" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\download.png"> Download</span></div>
    <div  id="wdrive-caution-menu-id" class="wdrive-group-menu-style">';
    $tdata .= '<span id="wdrive-caution-move-id" style="min-width:55px;" onclick="webdriveModule.menuActHandler(\'move\')" class="menuButton wdrive-menu-button" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\move.png"> Move </span>';
    if ($u_wshare_access == 1)
      $tdata .= '<span id="wdrive-caution-share-id" style="min-width:60px;" onclick="webdriveModule.menuActHandler(\'share\')" class="menuButton  wdrive-menu-button" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\share.png"> Share</span>';
    $tdata .= '<span id="wdrive-caution-delete-id" style="min-width:60px;" onclick="webdriveModule.menuActHandler(\'delete\')" class="menuButton wdrive-menu-button" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\delete.png"> Delete </span>
    </div>
    <div id="wdrive-compress-menu-id" class="wdrive-group-menu-style" >
    <span id="wdrive-compress-compress-id" onclick="webdriveModule.menuActHandler(\'compress\')" class="menuButton wdrive-menu-button" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\compress.png"> Compress</span>
    </div>
    <div id="wdrive-extract-menu-id" class="wdrive-group-menu-style" >
    <span id="wdrive-extract-extract-id" style="min-width:60px;" onclick="webdriveModule.menuActHandler(\'extract\')" class="menuButton" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\extract.png"> Extract</span>
    </div>
    <div id="wdrive-paste-menu-id" class="wdrive-group-menu-style" >
    <span id="wdrive-paste-paste-id"  style="min-width:60px;" onclick="webdriveModule.menuActHandler(\'paste\')" class="menuButton" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\paste.png"> Paste</span>
    </div>
    </div>
    <div class="hd-rldv">
    <div id="context-menu-id" onclick="event.stopPropagation();"></div>
    </div>
    <div id="uploaderID" style="width:100%;height:100%;overflow-y:auto;overflow-x:hidden" onClick="event.stopPropagation();webdriveModule.dismissAll();" class="general-scroll-bar-style">
    <div id="webDriveDashboard" onclick="event.stopPropagation();webdriveModule.dismissAll()" ></div>
    <div class="hd-fccc" style="opacity:0.6;width:150px;margin-left:auto;margin-right:auto"  onmouseover="this.style.opacity=\'1.0\';" onmouseout="this.style.opacity=\'0.6\';">
    <span>Drop your Files Here</span>
    <span style="margin:7px auto">Or</span>
    <button style="display:block;width:120px; height:25px;cursor:pointer" onclick="document.getElementById(\'web-drive-file-upload-id\').click()">Upload Files</button>
     <input type="file" style="display:none;" id="web-drive-file-upload-id" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-powerpoint, application/vnd.openxmlformats-officedocument.presentationml.presentation, application/vnd.ms-xpsdocument, application/x-rar, application/x-rar-compressed, application/octet-stream, application/zip, application/x-zip, application/x-zip-compressed, image/x-png, image/jpeg, image/gif" onchange="webdriveModule.fileUpload()" multiple/>
    </div>
    </div>
    <div class="hd-rldv" style="width:100%;">' . $createUpload . '</div>
    </div>';
    $data['opts']['status'] = true;
    $data['app_storage']= getenv('APP_WDPROXY_SVR');
    $data['bytes_per_chunk']= (int)getenv('BYTES_PER_CHUNK');
  } else
  $data['opts']['msg'] = 'You are not authorized. Contact with ICTIMMD.';
  $tdata .='</div>
  <div class="hd-rldv">
  <div id="webDrivePWD"><img src="images\\loader.gif" style="width:20px;height:20px"></div>
  </div>';
  $data['body-webdriveModule'] = $tdata;
  $data['opts']['key'] = getenv('APP_CODEBASE_SVR').' '.$_SESSION['access_key'];
  $data['opts']['previewpath'] = "web_drive\\";
  echo json_encode($data);
  $session->closeDBConnection($con);
}
