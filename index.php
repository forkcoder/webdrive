<?php
session_start();
include("header.php");
if (isset($_SESSION['bbank_userid']) == false || $_SESSION['bbank_userid'] == '') {
  if (isset($_GET['token']) == true) {
    $_SESSION['bbank_remember_token'] = $token = $_GET['token'] ?? '';
    $sql = "SELECT email_id FROM bbank_users where remember_token='$token' and userid=''";
    $con = $session->initDBConnection();
    $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed.");
    $session->closeDBConnection($con);
    if (mysqli_num_rows($result) == 1 && isset($_SESSION['bbank_userid']) == false) {
      $email_id = mysqli_fetch_row($result)[0];
      print '<div id="mainContentDiv" class="general-main-form">
  <div  class="loginModuleStyle" >
   <div class="general-main-form-row" style="align-items:center;justify-content:center;font-size:15px;padding:20px 0;">
     <h3> Update Basic Information</h3>
   </div>
  <div id="first-time-login-infoupdate" class="general-main-form-body  general-scroll-bar-style" style="justify-content:flex-start;">
   <div class="general-main-form-row">
     <div class="general-main-form-header"><span style="color:red;">*</span><span>Name:</span></div>
     <input class="general-form-input-w200"  type="text" placeholder="Name" id="userinfo_name" name="userinfo_name" value="" maxlength="100">
   </div>
   <div class="general-main-form-row">
     <div class="general-main-form-header"><span style="color:red;">*</span><span>User ID:</span></div>
     <input class="general-form-input-w200"  type="text" placeholder="User ID" id="userinfo_uderid" name="userinfo_userid" value="" maxlength="50" >
   </div>
   <div class="general-main-form-row">
   <div class="general-main-form-header"><span>Account Type:</span></div>
   <select class="general-form-input-w200"  id="userinfo_actype" name="userinfo_actype" value="Education" >
   <option value="Education" > Education </option> 
   <option value="Business" > Business </option> 
   </select>
  </div>
   <div class="general-main-form-row">
     <div class="general-main-form-header"> <span style="color:red;">*</span><span>Contact No:</span></div>
     <input class="general-form-input-w200"  type="text" placeholder="Contact No." id="userinfo_contact_no" name="userinfo_contact_no" value="" maxlength="15">
   </div>
   <div class="general-main-form-row">
     <div class="general-main-form-header"><span>Email ID:</span></div>
     <input class="general-form-input-w200"  type="text" disabled id="userinfo_email_id" name="userinfo_email_id" value="' . $email_id . '" maxlength="50">
   </div>
   <div class="general-main-form-row">
     <div class="general-main-form-header"> <span style="color:red;">*</span><span>New Password:</span></div>
     <input class="general-form-input-w200" name="userinfo_password" id="userinfo_password" type="password" onkeyup="checkpasswd();" />
   </div>
   <div class="general-main-form-row">
     <div class="general-main-form-header"> <span style="color:red;">*</span><span>Confirm Password:</span></div>
     <input type="password" class="general-form-input-w200" name="userinfo_confirm_password" id="userinfo_confirm_password" onkeyup="checkpasswd();" />
     <span id="userinfo_message"></span>
   </div>
   <div class="general-main-form-row" style="justify-content:center;align-items:center;margin-top:20px;">
     <span class="updateButton" onclick="profileUpdateUserinfo();"> Save and Continue </span>
   </div>
  </div>
  </div>
  </div>';
    } else {
      unset($_SESSION['bbank_remember_token']);
      header("Location:index.php");
      die();
    }
  } else {
    print '<div id="mainContentDiv">
    <div id="menu-index-page">
    <div style="display:flex; justify-content:center">
    <form method="post" name="login_form" action="index.php">
    <div class="loginModuleStyle">
    <div style="font-size:20px;padding-left: 20px;">Sign In</div>
    <hr style="background-color:darkgray;width:90%;margin:5px">
    <div class="loginInputFieldStyle">
    <div>User ID:</div>
    <div><input class="roundCornerInput" placeholder="Email or User ID" style="margin-right:50px" type="text" id="bbank-login-uid" name="nid_reg" onKeyup="check_submit(event,this,\'Login\', \'login_form\')" /></div>
    </div>
    <div class="loginInputFieldStyle">
    <div>Password:</div>
    <div><input class="roundCornerInput" placeholder="Type Password" style="margin-right:50px" type="password" id="bbank-login-psw" name="password" onKeyup="check_submit(event,this,\'Login\', \'login_form\')"></div>
    </div>
    <div class="loginInputFieldStyle" style="justify-content:center;align-self:flex-end;"> <input class="login-button-style" type="button" value="Login" name="login" onclick="LoginRequest(this,this.value,\'login_form\');"></div>
    </div>
    </form>
    </div>
    <div class="hd-frcc"> Default Id: sajibmitra, Password: 987654321 </div>
    </div>';
  }
} else {
?>
  <div id="mainContentDiv" class="notextselect" onClick="event.stopPropagation();webdriveModule.dismissAll();">
    <div id="webDriveRightDiv">
      <div id="webDriveMenus" onclick="event.stopPropagation()">
        <span class="menuButton" style="min-width:30px;vertical-align:middle" id="wdrive-grid-list-id" onclick="webdriveModule.listView(this)"><img style="height:20px;" src="images\\webdrive\\list.png"></span>
        <span class="menuButton" style="min-width:20px;" id="wdrive-back-btn-id" onclick="webdriveModule.backDir()"><img id="wdrive-back-img-id" style="height:20px;vertical-align:middle" src="images\\webdrive\\backinactive.png"></span>
        <span class="menuButton" style="min-width:20px" id="wdrive-up-btn-id" onclick="webdriveModule.upDir()"><img id="wdrive-up-img-id" style="height:20px;vertical-align:middle" src="images\\webdrive\\upinactive.png"></span>
        <div style="position:relative">
          <div onclick="webdriveModule.createNew()" class="menuButton wdrive-menu-button"><img src="images\\webdrive\\createnew.png"><span> New</span></div>
        </div>
        <div id="wdrive-singlefileordir-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-singlefileordir-rename-id" onclick="webdriveModule.menuActHandler('rename')" class="menuButton wdrive-menu-button"><img src="images\\webdrive\\rename.png"> <span>Rename</span> </div>
        </div>
        <div id="wdrive-common-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-common-copy-id" onclick="webdriveModule.menuActHandler('copy')" class="menuButton  wdrive-menu-button"><img src="images\\webdrive\\copy.png"><span> Copy</span></div>
          <div id="wdrive-common-download-id" onclick="webdriveModule.menuActHandler('download')" class="menuButton wdrive-menu-button"><img src="images\\webdrive\\download.png"><span> Download</span></div>
        </div>
        <div id="wdrive-caution-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-caution-move-id" onclick="webdriveModule.menuActHandler('move')" class="menuButton wdrive-menu-button"><img src="images\\webdrive\\move.png"><span> Move<span></div>
          <?php if ($_SESSION['bbank_wshare_access'] == 1) { ?>
            <div id="wdrive-caution-share-id" onclick="webdriveModule.menuActHandler('share')" class="menuButton  wdrive-menu-button"><img src="images\\webdrive\\share.png"><span> Share</span></div>
          <?php } ?>
          <div id="wdrive-caution-delete-id" onclick="webdriveModule.menuActHandler('delete')" class="menuButton wdrive-menu-button"><img src="images\\webdrive\\delete.png"><span> Delete</span></div>
        </div>
        <div id="wdrive-compress-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-compress-compress-id" onclick="webdriveModule.menuActHandler('compress')" class="menuButton wdrive-menu-button"><img src="images\\webdrive\\compress.png"><span> Compress</span></div>
        </div>
        <div id="wdrive-extract-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-extract-extract-id" onclick="webdriveModule.menuActHandler('extract')" class="menuButton wdrive-menu-button"><img src="images\\webdrive\\extract.png"><span> Extract</span></div>
        </div>
        <div id="wdrive-paste-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-paste-paste-id" onclick="webdriveModule.menuActHandler('paste')" class="menuButton wdrive-menu-button"><img src="images\\webdrive\\paste.png"><span> Paste</span></div>
        </div>
      </div>
      <div id="webDrivePWD">
        <div class="rlink rootlink" onClick="webdriveModule.renderWebDrive(' + webdriveModule.getRnode() + ',' + sharedFlag + ');webdriveModule.driveReload();">My Drive</div>
      </div>
      <div class="hd-rldv">
        <div id="context-menu-id" onclick="event.stopPropagation();"></div>
      </div>
      <div id="uploaderID" style="width:100%;height:100%;overflow-y:auto;overflow-x:hidden" onClick="event.stopPropagation();webdriveModule.dismissAll();" class="general-scroll-bar-style">
        <div id="webDriveDashboard" onclick="event.stopPropagation();webdriveModule.dismissAll()"></div>
        <div class="hd-fccc" style="opacity:0.6;width:150px;margin-left:auto;margin-right:auto" onmouseover="this.style.opacity='1.0';" onmouseout="this.style.opacity='0.6';">
          <span>Drop your Files Here</span>
          <span style="margin:7px auto">Or</span>
          <button style="display:block;width:120px; height:25px;cursor:pointer" onclick="document.getElementById('web-drive-file-upload-id').click()">Upload Files</button>
          <input type="file" style="display:none;" id="web-drive-file-upload-id" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-powerpoint, application/vnd.openxmlformats-officedocument.presentationml.presentation, application/vnd.ms-xpsdocument, application/x-rar, application/x-rar-compressed, application/octet-stream, application/zip, application/x-zip, application/x-zip-compressed, image/x-png, image/jpeg, image/gif" onchange="webdriveModule.fileUpload()" multiple />
        </div>
      </div>
    </div>
    <div id="webDriveLeftDiv">
      <div id="webDriveTree" class="general-scroll-bar-style">
        <div class="tnodeStyle"><img id="tnode-img" src="images\\webdrive\\mydrive.png" height="20" /><span>My Drive</span></div>
      </div>
    </div>
  </div>
  <div id="supermodal" onclick="event.stopPropagation();exitSuperModal()">
    <div class="modal" id="wdrive-modal-content">
    </div>
    <div class="modal" id="shareWithID" onclick="event.stopPropagation();">
    </div>
  </div>
  <script type="text/javascript">
    window.onload = function() {
      webdriveModule.init();
    };
  </script>
<?php }

$u_agent = $_SERVER['HTTP_USER_AGENT'];
$bname = 'Unknown';
$platform = 'Unknown';
$version = "";
//First get the platform?
if (preg_match('/linux/i', $u_agent)) {
  $platform = 'linux';
} elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
  $platform = 'mac';
} elseif (preg_match('/windows|win32/i', $u_agent)) {
  $platform = 'windows';
}

// Next get the name of the useragent yes seperately and for good reason
if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
  $bname = 'Internet Explorer';
  $ub = "MSIE";
} elseif (preg_match('/Firefox/i', $u_agent)) {
  $bname = 'Mozilla Firefox';
  $ub = "Firefox";
} elseif (preg_match('/OPR/i', $u_agent)) {
  $bname = 'Opera';
  $ub = "Opera";
} elseif (preg_match('/Chrome/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
  $bname = 'Google Chrome';
  $ub = "Chrome";
} elseif (preg_match('/Safari/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
  $bname = 'Apple Safari';
  $ub = "Safari";
} elseif (preg_match('/Netscape/i', $u_agent)) {
  $bname = 'Netscape';
  $ub = "Netscape";
} elseif (preg_match('/Edge/i', $u_agent)) {
  $bname = 'Edge';
  $ub = "Edge";
} elseif (preg_match('/Trident/i', $u_agent)) {
  $bname = 'Internet Explorer';
  $ub = "MSIE";
}

// finally get the correct version number
$known = array('Version', $ub, 'other');
$pattern = '#(?<browser>' . join('|', $known) .
  ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
if (!preg_match_all($pattern, $u_agent, $matches)) {
  // we have no matching number just continue
}
// see how many we have
$i = count($matches['browser']);
if ($i != 1) {
  //we will have two since we are not using 'other' argument yet
  //see if version is before or after the name
  if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
    $version = $matches['version'][0];
  } else {
    $version = $matches['version'][1];
  }
} else {
  $version = $matches['version'][0];
}

// check if we have a number
if ($version == null || $version == "") {
  $version = "?";
}
$ipaddress = '';
if (getenv('HTTP_CLIENT_IP'))
  $ipaddress = getenv('HTTP_CLIENT_IP');
else if (getenv('HTTP_X_FORWARDED_FOR'))
  $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
else if (getenv('HTTP_X_FORWARDED'))
  $ipaddress = getenv('HTTP_X_FORWARDED');
else if (getenv('HTTP_FORWARDED_FOR'))
  $ipaddress = getenv('HTTP_FORWARDED_FOR');
else if (getenv('HTTP_FORWARDED'))
  $ipaddress = getenv('HTTP_FORWARDED');
else if (getenv('REMOTE_ADDR'))
  $ipaddress = getenv('REMOTE_ADDR');
else
  $ipaddress = $_SERVER['REMOTE_ADDR'];
$hostname = $ipaddress;

$_SESSION['geo_gspace'] = $ipdat->geoplugin_countryName ?? '';

$_SESSION['clientInfo'] = array(
  'userAgent' => $u_agent,
  'name'      => $bname,
  'version'   => $version,
  'platform'  => $platform,
  'pattern'    => $pattern,
  'ipaddress' => $ipaddress,
  'hostname' => $hostname,
  'country' => '',
  'city' => '',
  'latitude' => '',
  'longitude' => '',
  'currency' => '',
  'currencycode' => '',
  'timezone' => '',
);

// $ipdat = @json_decode(file_get_contents(
//   "http://www.geoplugin.net/json.gp?ip=" . $ipaddress
// ));

// if ($ipdat != null || $ipdat != '') {
//   $_SESSION['country'] = $ipdat->geoplugin_countryName;
//   $_SESSION['city'] = $ipdat->geoplugin_city;
//   $_SESSION['latitude'] = $ipdat->geoplugin_latitude;
//   $_SESSION['longitude'] = $ipdat->geoplugin_longitude;
//   $_SESSION['currency'] = $ipdat->geoplugin_currencySymbol;
//   $_SESSION['currencycode'] = $ipdat->geoplugin_currencyCode;
//   $_SESSION['timezone'] = $ipdat->geoplugin_timezone;
// }

?>
<?php include("footer.php"); ?>