<?php
session_start();
include("header.php");
if (isset($_SESSION['fcoder_userid']) && $_SESSION['fcoder_userid'] != '' && isset($_GET['token']) == false && isset($_SESSION['fcoder_remember_token'])) { ?>
  <div id="mainContentDiv" class="notextselect" onClick="event.stopPropagation();webdriveModule.dismissAll();">
    <div id="webDriveRightDiv">
      <div id="webDriveMenus" onclick="event.stopPropagation()">
        <span class="menuButton" style="min-width:30px;vertical-align:middle" id="wdrive-grid-list-id" onclick="webdriveModule.listView(this)"><span class="glyphicon glyphicon-th-list"> </span></span>
        <span class="menuButton" style="min-width:20px;" id="wdrive-back-btn-id" onclick="webdriveModule.backDir()"><span id="wdrive-back-img-id" class="glyphicon glyphicon-chevron-left"> </span></span>
        <span class="menuButton" style="min-width:20px" id="wdrive-up-btn-id" onclick="webdriveModule.upDir()"><span id="wdrive-up-img-id" class="glyphicon glyphicon-chevron-up"> </span></span>
        <div style="position:relative">
          <div onclick="webdriveModule.createNew()" class="menuButton wdrive-menu-button"><span class="glyphicon glyphicon-plus-sign"></span> New </div>
        </div>
        <div id="wdrive-singlefileordir-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-singlefileordir-rename-id" onclick="webdriveModule.menuActHandler('rename')" class="menuButton wdrive-menu-button"> <span class="glyphicon glyphicon-edit"></span> Rename </div>
        </div>
        <div id="wdrive-common-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-common-copy-id" onclick="webdriveModule.menuActHandler('copy')" class="menuButton  wdrive-menu-button"> <span class="glyphicon glyphicon-copy"></span> Copy </div>
          <div id="wdrive-common-download-id" onclick="webdriveModule.menuActHandler('download')" class="menuButton wdrive-menu-button"><span class="glyphicon glyphicon-cloud-download"></span> Download </div>
        </div>
        <div id="wdrive-caution-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-caution-move-id" onclick="webdriveModule.menuActHandler('move')" class="menuButton wdrive-menu-button"><span class="glyphicon glyphicon-move"></span> Move </div>
          <?php if ($_SESSION['fcoder_wshare_access'] == 1) { ?>
            <div id="wdrive-caution-share-id" onclick="webdriveModule.menuActHandler('share')" class="menuButton  wdrive-menu-button"><span class="glyphicon glyphicon-share"></span> Share</div>
          <?php } ?>
          <div id="wdrive-caution-delete-id" onclick="webdriveModule.menuActHandler('delete')" class="menuButton wdrive-menu-button"><span class="glyphicon glyphicon-remove-circle"></span>Delete</div>
        </div>
        <div id="wdrive-compress-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-compress-compress-id" onclick="webdriveModule.menuActHandler('compress')" class="menuButton wdrive-menu-button"><span class="glyphicon glyphicon-compressed"></span> Compress</div>
        </div>
        <div id="wdrive-extract-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-extract-extract-id" onclick="webdriveModule.menuActHandler('extract')" class="menuButton wdrive-menu-button"><span class="glyphicon glyphicon-wrench"></span> Extract </div>
        </div>
        <div id="wdrive-paste-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-paste-paste-id" onclick="webdriveModule.menuActHandler('paste')" class="menuButton wdrive-menu-button"><span class="glyphicon glyphicon-paste"></span>Paste</div>
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
      </div>
      <div class="hd-rldv" style="align-self:center">
        <div class="hd-fccc" style="position:absolute;opacity:0.6;width:150px;margin-right:auto;bottom:60px;z-index:1" onmouseover="this.style.opacity='1.0';" onmouseout="this.style.opacity='0.6';">
          <span>Drop your Files Here</span>
          <span style="margin:7px auto">Or</span>
          <button style="display:block;width:120px; height:25px;cursor:pointer" onclick="document.getElementById('web-drive-file-upload-id').click()"><span class="glyphicon glyphicon-cloud-upload"></span> Upload Files </button>
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
<?php
} else if (isset($_GET['token']) == true) {
  $token = $_GET['token'];
  
  if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
    $sql = "SELECT * FROM fcoder_users where remember_token='$token' and userid='$uid'";
  } else
    $sql = "SELECT * FROM fcoder_users where remember_token='$token'";
  $con = $session->initDBConnection();
  $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed.");
  $session->closeDBConnection($con);
  if (mysqli_num_rows($result) == 1) {
    $data = mysqli_fetch_assoc($result);
    if($token == $_SESSION['fcoder_remember_token'] && isset($_SESSION['fcoder_userid']) && $data['userid'] == $_SESSION['fcoder_userid']){
      unset($_GET['Acs']);
    }
    else {
      $_SESSION['fcoder_access_id'] = '';
    }
    if (($token == $_SESSION['fcoder_remember_token'] && isset($_SESSION['fcoder_userid']) && $data['userid'] == $_SESSION['fcoder_userid']) || (isset($_SESSION['fcoder_remember_token']) == false && ((isset($_SESSION['fcoder_userid']) == false && $data['userid'] == '') || (isset($_GET['Acs']) && $_GET['Acs'] == $data['password_hash'])))) {
      $currentPassword = '';
      $mandatory = '';
      $headline = 'Update Basic Information';
      $submitButton = '<span class="login-button-style" onclick="profileUpdateUserinfo();"> Save and Continue </span>';
      $email_id = $data['email_id'];
      if ((isset($_SESSION['fcoder_remember_token']) == false && $data['userid'] == '') || (isset($_GET['Acs']) && $_GET['Acs'] == $data['password_hash'])) {
        $mandatory = '<span style="color:red;">*</span>';

        if (isset($_GET['Acs']) && $_GET['Acs'] == $data['password_hash']) {
          $_SESSION['fcoder_access_id'] = $data['password_hash'];
          $headline = 'Reset Password';
          $submitButton = '<span class="login-button-style" onclick="profileResetPassword();"> Reset and Continue </span>';
          $_SESSION['fcoder_email_id'] = $data['email_id'];
        } else {
          $_SESSION['fcoder_email_id'] = $email_id;
          $_SESSION['fcoder_remember_token'] = $token;
        }
      } else {
        $currentPassword = '<div class="general-main-form-row">
      <div class="general-main-form-header"> <span style="color:red;">*</span><span>Current Password:</span></div>
      <input class="general-form-input-w200" onfocus="this.classList.remove(\'errorinput\')" name="userinfo_password_current" id="userinfo_password_current" type="password" vaule=""/>
    </div>';
      }

      print '<div id="mainContentDiv" class="general-main-form">
        <div id="menu-index-page">
          <div class="cardModuleStyle">
            <div class="headline">' . $headline . '</div>';
      if (isset($_GET['Acs']) == false) {
        print '<div class="general-main-form-row">
              <div onmouseover="document.getElementById(\'editLoggedUserImgDiv\').style.display=\'block\'" onmouseout="document.getElementById(\'editLoggedUserImgDiv\').style.display=\'none\'" style="display:flex;flex-direction:row;width:100%;justify-content:center;align-items:flex-start;margin:5px auto">
                <div class="hd-rldv">
                  <div class="p-image" onclick="event.stopPropagation()" id="editLoggedUserImgDiv" style="display:none">
                    <input onchange="imageUpload(\'' . $token . '\',this);" id="profileImgUpload" type="file" accept="image/x-png,image/jpeg" style="display:none" />
                    <img class="upload-button" title="Dimension: 300x300 pixels, Max Size: 200KB" onclick="document.getElementById(\'profileImgUpload\').click();" src="\\images\\update_img.svg" />
                  </div>
                </div>';
        $ppic = "images/profile/" . $token;
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $ppic) == true)
          print '<img id="logged-user-img-id" class="userprofilelogo" src="' . $ppic . '?' . time() . '" style="align-self:center" />';
        else
          print '<img id="logged-user-img-id" title="' . $ppic . '" class="userprofilelogo" src="images\\logged-user.png" style="align-self:center" />';
        print '</div>
            </div>';
      }
      print '<div class="general-main-form-body  general-scroll-bar-style" style="justify-content:flex-start;">
              <form id="form-user-update-self">';
      if (isset($_GET['Acs']) == false) {
        print '<div class="general-main-form-row">
                <div class="general-main-form-header"><span style="color:red;">*</span><span>Name:</span></div>
                <input class="general-form-input-w200" onfocus="this.classList.remove(\'errorinput\')" type="text" placeholder="Name" id="userinfo_name" name="userinfo_name" value="' . $data['name'] . '" maxlength="100">
              </div>
              <div class="general-main-form-row">
                <div class="general-main-form-header"><span style="color:red;">*</span><span>User ID:</span></div>
                <input class="general-form-input-w200" onfocus="this.classList.remove(\'errorinput\')" type="text" placeholder="User ID" id="userinfo_uderid" name="userinfo_userid" value="' . $data['userid'] . '" maxlength="50">
              </div>
              <div class="general-main-form-row">
                <div class="general-main-form-header"><span>Account Type:</span></div>
                <select class="general-form-input-w200" onfocus="this.classList.remove(\'errorinput\')" style="width:230px" id="userinfo_actype" name="userinfo_actype" value="' . $data['actype'] . '">
                  <option value="" disabled> Select Type </option>
                  <option value="Education"> Education </option>
                  <option value="Business"> Business </option>
                </select>
              </div>
              <div class="general-main-form-row">
                 <div class="general-main-form-header"> <span> Employer Name:</span></div>
                  <input class="general-form-input-w200" onfocus="this.classList.remove(\'errorinput\')" type="text" placeholder="Name of Employer" id="userinfo_gspace" name="userinfo_gspace" value="' . $data['gspace'] . '" maxlength="15">
              </div>
              <div class="general-main-form-row">
                <div class="general-main-form-header"> <span> Department:</span></div>
                <input class="general-form-input-w200" onfocus="this.classList.remove(\'errorinput\')" type="text" placeholder="Name of Department" id="userinfo_lspace" name="userinfo_lspace" value="' . $data['lspace'] . '" maxlength="15">
              </div>
              <div class="general-main-form-row">
                <div class="general-main-form-header"> <span style="color:red;">*</span><span>Contact No:</span></div>
                <input class="general-form-input-w200" onfocus="this.classList.remove(\'errorinput\')" type="text" placeholder="Contact No." id="userinfo_contact_no" name="userinfo_contact_no" value="' . $data['contact_no'] . '" maxlength="15">
              </div>';
      }
      print '<div class="general-main-form-row">
                <div class="general-main-form-header"><span>Email ID:</span></div>
                <input class="general-form-input-w200" onfocus="this.classList.remove(\'errorinput\')" type="text" disabled id="userinfo_email_id" name="userinfo_email_id" value="' . $email_id . '" maxlength="50">
              </div>' . $currentPassword . '<div class="general-main-form-row">
                <div class="general-main-form-header">' . $mandatory . '<span>New Password:</span></div>
                <input class="general-form-input-w200" onfocus="this.classList.remove(\'errorinput\')" name="userinfo_password" id="userinfo_password" type="password" autocomplete="false" onkeyup="checkpasswd();" vaule=""/>
              </div>
              <div class="general-main-form-row">
                <div class="general-main-form-header">' . $mandatory . '<span>Confirm Password:</span></div>
                <input type="password" class="general-form-input-w200" onfocus="this.classList.remove(\'errorinput\')" name="userinfo_confirm_password" id="userinfo_confirm_password" autocomplete="false" onkeyup="checkpasswd();" value="" />
                <div class="hd-rldv"><span id="userinfo_message"></span></div>
              </div>
              </div>
              <input type="hidden" id="unique-token-id" name="unique-token-id" value="' . $token . '">';

      print '</form>
              <div class="general-main-form-row">
              ' . $submitButton . '          
              </div>
            </div>';

      print '</div>
      </div>';
    } else {
      $session->removeSession();
      header("Location:index.php");
      die();
    }
  } else {
    $session->removeSession();
    header("Location:index.php");
    die();
  }
} else {
  print '<div id="mainContentDiv">
    <div id="menu-index-page">
      <form method="post" name="generalCardForm" action="index.php">
        <div class="cardModuleStyle">
          <div class="headline">Sign In</div>
          <hr style="background-color:darkgray;width:90%;margin:5px">
          <div class="loginInputFieldStyle">
            <div>User ID:</div>
            <div><input class="roundCornerInput" onfocus="this.classList.remove(\'errorinput\')" placeholder="Email or User ID" type="text" id="fcoder-login-uid" name="fcoder-login-uid" onKeyup="check_submit(event,\'generalCardForm\',\'loginButton\')" /></div>
          </div>
          <div class="loginInputFieldStyle">
            <div>Password:</div>
            <div><input class="roundCornerInput" onfocus="this.classList.remove(\'errorinput\')" placeholder="Type Password"  type="password" id="fcoder-login-psw" name="fcoder-login-psw" onKeyup="check_submit(event,\'generalCardForm\',\'loginButton\')"></div>
          </div>
          <div class="loginInputFieldStyle" >
          <span class="backlinks" onClick="passwordRecovery()" >Forget Password</span>
          <input class="login-button-style"  type="button" value="Login" name="loginButton" onclick="LoginRequest(this.value);">
          </div>
        </div>
      </form>
    </div>';
}

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

?>
<?php include("footer.php"); ?>