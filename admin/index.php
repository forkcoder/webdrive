<?php
session_start();
include("../header.php");
if (isset($_SESSION['fcoder_userid']) == false || $_SESSION['fcoder_userid'] == '') {
  $session->removeSession();
  header("Location:/index.php");
  die();
} else if (isset($_SESSION['fcoder_hadmin_access']) && $_SESSION['fcoder_hadmin_access'] == 1) {
  if (isset($_GET['token']) == true && strpos($_SERVER['REQUEST_URI'], 'admin') !== false) {

    $token = $_GET['token'];
    $userid = $_GET['uid'];
    $sql = "SELECT * FROM fcoder_users where remember_token='$token' and userid='$userid'";
    $con = $session->initDBConnection();
    $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed.");

    if (mysqli_num_rows($result) == 1) {
      $data = mysqli_fetch_assoc($result);
      if (isset($_SESSION['fcoder_userid']) && $_SESSION['fcoder_userid'] != '' && isset($_SESSION['fcoder_hadmin_access']) && $_SESSION['fcoder_hadmin_access'] == 1) {
        $email_id = $data['email_id'];

        $log = '';
        $sql = "SELECT wdl_datetime, wdl_action, wdl_src FROM fcoder_webdrive_log where wdl_iuser_id='$userid' order by wdl_datetime desc limit 20";
        $result = mysqli_query($con, $sql) or die("Fetching wdrive log from DB is failed." . $sql);
        $count = 1;
        while ($r = mysqli_fetch_assoc($result)) {
          if ($count % 2)
            $tdata = $tdata . '<tr class="tableRowStyle even"  valign="middle">';
          else
            $tdata = $tdata . '<tr class="tableRowStyle odd"  valign="middle">';

          foreach ($r as $key => $t) {
            if ($key == 'name') {
              if ($r['hadmin_access'] == 1)
                $tdata = $tdata . '<td> <a href="/admin?token=' . $r['remember_token'] . '&uid=' . $r['userid'] . '"> <span class="glyphicon glyphicon-star-empty" style="color:blueviolet"></span><span>' . $t . '</span></a></td>';
              else
                $tdata = $tdata . '<td> <a href="/admin?token=' . $r['remember_token'] . '&uid=' . $r['userid'] . '"> <span class="glyphicon glyphicon-user" style="color:darkcyan"></span><span>' . $t . '</span></a></td>';
            } else if ($key == 'wshare_limit') {
              if ($r['wshare_access'] == 1)
                $tdata = $tdata . '<td> ' . $session->formatSizeUnits($r['wshare_data_bytes'], 'MB') . ' of ' . $t . '</td>';
              else
                $tdata = $tdata . '<td> No </td>';
            } else if ($key == 'wstorage_limit') {
              $tdata = $tdata . '<td> ' . $session->formatSizeUnits($r['wstorage_data_bytes'], 'MB') . ' of ' . $t . '</td>';
            } else if ($key == 'hadmin_access') {
              if ($t == 1)
                $tdata = $tdata . '<td>Yes </td>';
              else
                $tdata = $tdata . '<td>  No </td>';;
            } else if ($key != 'remember_token' && $key != 'wshare_access' && $key != 'hadmin_access' && $key != 'wstorage_data_bytes' && $key != 'wshare_data_bytes')
              $tdata = $tdata . '<td> ' . $t . '</td>';
          }
          $tdata = $tdata . '</tr>';
          $count++;
        }

        print '<div id="mainContentDiv" class="general-main-form">
            <div class="tab">
              <button class="tablinks active" onclick="adminModule.openCity(event, \'wdrive-admin-edit-info\')" >Update Basic Information</button>
              <button class="tablinks" onclick="adminModule.openCity(event, \'wdrive-admin-activity-log\')">Web Drive Activity Log</button>
              <button class="tablinks" onclick="adminModule.openCity(event,\'wdrive-admin-data-stats\')"> Stats of Data Usages</button>
            </div>
            <div id="wdrive-admin-edit-info" class="tabcontent" style="display:flex;" >
              <div>
                <div onmouseover="document.getElementById(\'editLoggedUserImgDiv\').style.display=\'block\'" onmouseout="document.getElementById(\'editLoggedUserImgDiv\').style.display=\'none\'" style="display:flex;flex-direction:row;width:100%;justify-content:center;align-items:flex-start;margin:5px auto">
                  <div class="hd-rldv">
                    <div class="p-image" onclick="event.stopPropagation()" id="editLoggedUserImgDiv" style="display:none">
                      <input onchange="imageUpload(\'' . $token . '\',this);" id="profileImgUpload" type="file" accept="image/x-png,image/jpeg" style="display:none" />
                      <img class="upload-button" title="Dimension: 300x300 pixels, Max Size: 200KB" onclick="document.getElementById(\'profileImgUpload\').click();" src="\\images\\update_img.svg" />
                    </div>
                  </div>';
        $ppic = "/images/profile/" . $token;
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $ppic) == true)
          print '<img id="logged-user-img-id" class="userprofilelogo" src="' . $ppic . '?' . time() . '" style="align-self:center" />';
        else
          print '<img id="logged-user-img-id" title="' . $ppic . '" class="userprofilelogo" src="/images/logged-user.png" style="align-self:center" />';
        print '
                </div>
              </div>
              <div>
              <form id="form-user-update-admin">

              <div class="general-main-form-body  general-scroll-bar-style" style="justify-content:flex-start;">
                <div class="general-main-form-row">
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
                    <option value="" disabled hidden> Select Type </option>
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
                </div>
                <div class="general-main-form-row">
                  <div class="general-main-form-header"><span style="color:red;">*</span><span>Email ID:</span></div>
                  <input class="general-form-input-w200" onfocus="this.classList.remove(\'errorinput\')" type="text" id="userinfo_email_id" name="userinfo_email_id" value="' . $email_id . '" maxlength="50">
                </div>
              </div>
              <input type="hidden" id="unique-token-id" name="unique-token-id" value="' . $token . '">
              </form>
              </div>
              <div class="general-main-form-row" style="justify-content:center;align-items:center;">
                <span onClick="adminModule.updateUserData();" class="menuButton" style="padding:5px 10px; ">  Save User Info </span>
              </div>
              </div>
            <div id="wdrive-admin-activity-log" class="tabcontent">
            <table width="80%" border="0" align="center" valign="top" cellpadding="1" cellspacing="0" >
            <tr  class="tableRowStyle tableRowHeaderStyle"  valign="middle">
            <th> Date Time </th>
            <th> Action </th>
            <th> Source </th>
            <th> Destination </th>
            </tr>  
            ' . $log . '</table> 
          </div>
          <div id="wdrive-admin-data-stats" class="tabcontent">
            <h3>Monthly Reports</h3>
            <p>Monthly Reports</p>
          </div>
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
    $session->closeDBConnection($con);
  } else {
    $con = $session->initDBConnection();
    $sql = "SELECT name, actype,role, hadmin_access, remember_token, wstorage_limit, wshare_access, wstorage_data_bytes, wshare_data_bytes, wshare_limit, avater_count, contact_no, userid, email_id from fcoder_users where userid!='' order by name desc";
    $result = mysqli_query($con, $sql) or die("User info could not be fetched.");
    $tdata = '';
    $count = 1;
    while ($r = mysqli_fetch_assoc($result)) {
      if ($count % 2)
        $tdata = $tdata . '<tr class="tableRowStyle even"  valign="middle">';
      else
        $tdata = $tdata . '<tr class="tableRowStyle odd"  valign="middle">';

      foreach ($r as $key => $t) {
        if ($key == 'name') {
          if ($r['hadmin_access'] == 1)
            $tdata = $tdata . '<td> <a href="/admin?token=' . $r['remember_token'] . '&uid=' . $r['userid'] . '"> <span class="glyphicon glyphicon-star-empty" style="color:blueviolet"></span><span>' . $t . '</span></a></td>';
          else
            $tdata = $tdata . '<td> <a href="/admin?token=' . $r['remember_token'] . '&uid=' . $r['userid'] . '"> <span class="glyphicon glyphicon-user" style="color:darkcyan"></span><span>' . $t . '</span></a></td>';
        } else if ($key == 'wshare_limit') {
          if ($r['wshare_access'] == 1)
            $tdata = $tdata . '<td> ' . $session->formatSizeUnits($r['wshare_data_bytes'], 'MB') . ' of ' . $t . '</td>';
          else
            $tdata = $tdata . '<td> No </td>';
        } else if ($key == 'wstorage_limit') {
          $tdata = $tdata . '<td> ' . $session->formatSizeUnits($r['wstorage_data_bytes'], 'MB') . ' of ' . $t . '</td>';
        } else if ($key == 'hadmin_access') {
          if ($t == 1)
            $tdata = $tdata . '<td>Yes </td>';
          else
            $tdata = $tdata . '<td>  No </td>';;
        } else if ($key != 'remember_token' && $key != 'wshare_access' && $key != 'hadmin_access' && $key != 'wstorage_data_bytes' && $key != 'wshare_data_bytes')
          $tdata = $tdata . '<td> ' . $t . '</td>';
      }
      $tdata = $tdata . '</tr>';
      $count++;
    }
    $session->closeDBConnection($con);

    print '<div id="userMainFrame" ><div id="userControlRow">
  <input type="text" style="padding-right:15px;padding:2px;margin:10px" class="roundedSelectCorner" placeholder="Type Name, Id, email etc." id="r_userinfo" value="" onkeyup="userModule.filterUsers(this.value)">
  <div  onclick="userModule.init();"  class="clearButton" > Clear </div>
  <div class="tsummary-style" id="userSummaryDivID" style="display:none"></div>
  </div>
  <div id="show_user_div" class="hd-fcsc general-scroll-bar-style">
  <table width="80%" border="0" align="center" valign="top" cellpadding="1" cellspacing="0" >
  <tr  class="tableRowStyle tableRowHeaderStyle"  valign="middle">
  <th> Name </th>
  <th> Type </th>
  <th> Role </th>
  <th> Admin </th>
  <th> Storage (MB)</th>
  <th> Shareable (MB) </th>
  <th> Avatar </th>
  <th> Contact </th>
  <th> Domain Id </th>
  <th> Email Id </th>
  </tr>  
  ' . $tdata . '</table></div></div>';
  }
} else {
  header("Location:/index.php");
  die();
}
include("../footer.php");
