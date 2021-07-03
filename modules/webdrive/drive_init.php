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
  $u_gspace = $_SESSION['fcoder_gspace'];
  $u_genid = $_SESSION['fcoder_genid'];
  $u_wstorage_limit = $_SESSION['fcoder_wstorage_limit '];
  $u_wshare_access = $_SESSION['fcoder_wshare_access'];
  $u_ftransfer_access = $_SESSION['fcoder_ftransfer_access'];
  $createUpload = '';
  $tdata = '';
  if ($_SESSION['fcoder_wdrive_access'] == 1) {
    $tdata = '<div id="webDriveLeftDiv">
    <div class="webdrive-header-title">Web Z-Drive</div>
    <div id="webDriveTree" class="general-scroll-bar-style"><img src="images\\loader.gif" style="width:20px;height:20px;align-self:center"></div>
    <div id="webDriveStatus">
    <div class="webdrive-header-title">Usages Stats</div>
    <div id="wrapper">
    <svg id="meter">
    <circle id="border" r="32" cx="50%" cy="50%" stroke="#F6F6F6" stroke-width="24" stroke-dasharray="100, 200" fill="none"/>
    <circle id="avg" r="32" cx="50%" cy="50%" stroke="#7CCCE5" stroke-width="24" stroke-dasharray="100, 200" fill="none"/>
    <circle id="wdrive-fs-factor" r="32" cx="50%" cy="50%" stroke="#FDE47F" stroke-width="24" stroke-dasharray="10, 200" fill="none"/>
    </svg>
    </div>';
    $tdata .= '<div id="wdrive-fs-percentage"></div>
    <div id="wdrive-status-detail-id">
    <div><span>Free: </span><span id="wdrive-free-space-id"></span></div>
    <div><span>Total: </span><span id="wdrive-storage-quanta-id">' . $u_wstorage_limit . ' MB' . '</span> </div>';
    if ($u_wshare_access == 1) {
      $tdata .= '<div><span>Shared: </span><span id="wdrive-myshare-size-id"></span></div>';
    }
    $tdata .= '</div>
    </div>
    </div>';
    $createUpload .= '<div class="hdesk-relative-div" style="width:100%;">
    <div id="createNewFolderID">
    <img id="new-folder-img-id" onclick="webdriveModule.createNew()" src="images\\webdrive\\createnew.png">
    <div id="new-folder-name-id" style="display:flex; flex-direction:column;justify-content:flex-end;visibility: hidden;opacity:0; transition:visibility 0s, opacity .7s linear"><div style="align-self:flex-start; margin-left:5px"> Press Enter to Create </div><input type="text" style="border:1px solid skyblue; width:150px;" class="roundCornerInput" onkeydown="webdriveModule.pressEnter(event, \'createnew\')" id="wdrive_create_input" maxlength="50"></div>
    </div>';
    $users = array();
    $users['ids'] = array();
    $count_user = 0;
    if ($u_ftransfer_access == 1 || $u_wshare_access == 1) {
      $sql = "SELECT id, name, actype, userid, avater_count, contact_no, genid from fcoder_users where role!='Inactive' and genid!='$u_genid' and gspace = '$u_gspace' order by IF(FIELD(actype, 'Governor','Deputy Governor','Executive Director','General Manager', 'Deputy General Manager','Joint Director', 'Deputy Director','Assistant Director', 'Officer','Sr. Data Entry/Control Operator','Data Entry/Control Operator')=0,'Assistant Director',actype) desc, name ASC";
      $result = mysqli_query($con, $sql) or die("User info could not be fetched ");
      while ($r = mysqli_fetch_assoc($result)) {
        $genid = $r['genid'];
        $users['ids'][] = $genid;
        $users[$genid] = $r;
        $count_user++;
      }
      $users['ids']['total'] = $count_user;
      $data['users'] = $users;
      if ($u_ftransfer_access == 1) {
        $data['opts']['ul'] = $_SESSION['fcoder_upload_limit'];
        $data['opts']['tu'] = $_SESSION['fcoder_total_uploads'];
        $data['opts']['tr'] = $_SESSION['fcoder_total_recipients'];
        $data['opts']['ff'] = "application/vnd.openxmlformats-gspacedocument.spreadsheetml.sheet, application/vnd.ms-excel, application/pdf, application/msword, application/vnd.openxmlformats-gspacedocument.wordprocessingml.document, application/vnd.ms-powerpoint, application/vnd.openxmlformats-gspacedocument.presentationml.presentation, application/vnd.ms-xpsdocument";
      }
      $createUpload .= '<div id="shareOrSendWithID">
      <div class="hdesk-flex-col-start-center" style="height:100%;width:100%">
      <div id="wd-user-list-header" class="hdesk-flex-row-start-center" style="background-color:whitesmoke;height:25px;width:100%; cursor:pointer;flex-shrink: 0;">
      <img id="add-sharewith-img-id" style="height:16px;width:16px;margin:5px" onclick="webdriveModule.discardShareOrSend()" src="images\\webdrive\\sharewith.png">
      <input type="text" placeholder="Search User with Name, SAP ID or Contact Number" class="hdesk-card-input-text" id="wdrive_share_with_input" autocomplete="off"
      onkeydown="webdriveModule.pressEnter(event, \'share\')"  onkeyup="webdriveModule.filterUsers();"
      onclick="event.stopPropagation();webdriveModule.filterUsers();"
      onfocus="event.stopPropagation();webdriveModule.filterUsers();"/>
      <span style="max-width:180px;min-width:180px;"> List of Selected File(s) </span>
      <div class="updateButton"  id="wd-send-share-btn" onclick="webdriveModule.sendShareREQ()">Share</div>
      </div>
      <div class="hdesk-flex-row-start-start" style="width:100%;">
      <div id="wd-user-list-id" class="general-scroll-bar-style" ></div>
      <div id="existing-sharewith-id" class="general-scroll-bar-style"></div>
      </div>
      </div>
      </div>';
    } else {
      $users['ids']['total'] = $count_user;
      $data['users'] = $users;
    }

    $createUpload .= '<div id="uploadFileID">
    <div class="uploadBox" id="uploaderID">
    <!-- DROP ZONE -->
    <div style="display:flex;flex-direction:column;justify-content:center;flex-grow:1">Drop your Files Here</div>
    <!-- FALLBACK -->
    <div id="upload-fallback-id"></div>
    </div>
    <img id="upload-folder-img-id" onclick="webdriveModule.uploadDir();" src="images\\webdrive\\upload-inactive.png">
    </div>
    </div>';
    $tdata .= '<div id="webDriveRightDiv">
    <div style="display:flex;flex-direction:row;">
    <span class="menuButton"  style="min-width:20px;" id="wdrive-back-btn-id" onclick="webdriveModule.backDir()"><img id="wdrive-back-img-id" style="height:20px;vertical-align:middle" src="images\\webdrive\\backinactive.png"></span>
    <span class="menuButton" style="min-width:20px"  id="wdrive-up-btn-id" onclick="webdriveModule.upDir()"><img id="wdrive-up-img-id" style="height:20px;vertical-align:middle"  src="images\\webdrive\\upinactive.png"></span>
    <div id="webDrivePWD"><img src="images\\loader.gif" style="width:20px;height:20px"></div>
    </div>
    <div id="webDriveMenus">
    <span class="menuButton"  style="min-width:30px;vertical-align:middle" id="wdrive-grid-list-id" onclick="webdriveModule.listView(this)"><img style="height:20px;"  src="images\\webdrive\\list.png"></span>
    <div id="wdrive-singlefileordir-menu-id" class="wdrive-group-menu-style" >
    <span id="wdrive-singlefileordir-rename-id" onclick="webdriveModule.menuActHandler(\'rename\')" class="menuButton" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\rename.png"> Rename </span>
    </div>
    <div id="wdrive-common-menu-id" class="wdrive-group-menu-style" >
    <span id="wdrive-common-copy-id" style="min-width:55px;"  onclick="webdriveModule.menuActHandler(\'copy\')" class="menuButton" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\copy.png"> Copy </span>
    <span id="wdrive-common-download-id" onclick="webdriveModule.menuActHandler(\'download\')" class="menuButton" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\download.png"> Download</span>';
    if ($u_ftransfer_access == 1) {
      $tdata .= '<span id="wdrive-common-send-id" style="min-width:55px;" onclick="webdriveModule.menuActHandler(\'send\')" class="menuButton" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\transfer.png"> Send</span>';
    }
    $tdata .= '</div>
    <div  id="wdrive-caution-menu-id" class="wdrive-group-menu-style">
    <span id="wdrive-caution-move-id" style="min-width:55px;" onclick="webdriveModule.menuActHandler(\'move\')" class="menuButton" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\move.png"> Move </span>';
    if ($u_wshare_access == 1) {
      $tdata .= '<span id="wdrive-caution-share-id" style="min-width:60px;" onclick="webdriveModule.menuActHandler(\'share\')" class="menuButton" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\share.png"> Share</span>';
    }
    $tdata .= '<span id="wdrive-caution-delete-id" style="min-width:60px;" onclick="webdriveModule.menuActHandler(\'delete\')" class="menuButton" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\delete.png"> Delete</span>
    </div>
    <div id="wdrive-compress-menu-id" class="wdrive-group-menu-style" >
    <span id="wdrive-compress-compress-id" onclick="webdriveModule.menuActHandler(\'compress\')" class="menuButton" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\compress.png"> Compress</span>
    </div>
    <div id="wdrive-extract-menu-id" class="wdrive-group-menu-style" >
    <span id="wdrive-extract-extract-id" style="min-width:60px;" onclick="webdriveModule.menuActHandler(\'extract\')" class="menuButton" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\extract.png"> Extract</span>
    </div>
    <div id="wdrive-paste-menu-id" class="wdrive-group-menu-style" >
    <span id="wdrive-paste-paste-id"  style="min-width:60px;" onclick="webdriveModule.menuActHandler(\'paste\')" class="menuButton" ><img style="height:20px;vertical-align:middle" src="images\\webdrive\\paste.png"> Paste</span>
    </div>
    </div>
    <div class="hdesk-relative-div">
    <div id="context-menu-id"></div>
    </div>
    <div id="webDriveDashboard" onclick="webdriveModule.dismissAll()" class="general-scroll-bar-style"></div>' . $createUpload . '
    </div>';
    $data['opts']['status'] = true;
  } else
    $data['opts']['msg'] = 'You are not authorized. Contact with webmaster@forkcoder.com.';
  $data['webDriveDiv'] = $tdata;
  echo json_encode($data);
  $session->closeDBConnection($con);
}
