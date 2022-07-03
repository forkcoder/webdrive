<!--******************* Web Drive ****-->
<!--******************* Version 1.0 *****************************-->
<!--******************* Version Date 01.07.2021 *****************-->
<!--******************* Developed by Sajib Mitra. All Rights Reserved. *****************-->
<?php
if (!isset($_SESSION))
  session_start();
require('modules/Servlets.php');
$session = new DBProxy();
?>
<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>Web Drive</title>
  <link rel="icon" href="/images/webdrive.png" type="image/png">
  <!--*******************Start   OTP *****************************-->
  <script>
    <?php
    date_default_timezone_set('Asia/Dhaka');
    if (!isset($_SESSION['login_key'])) {
      $uxid = uniqid();
      $_SESSION['login_key'] = $uxid;
    } else {
      $uxid = $_SESSION['login_key'];
    }
    ?>
    var auth_ph = "<?php echo $uxid; ?>";
    var ph = "Access denied from all page";
  </script>
  <!--*******************End   OTP *****************************-->
  <!--*******************Start Common Header script and Styles *****************************/-->
  <link rel="stylesheet" href="/styles/bootstrap.min.css">
  <link href="/styles/style.css" rel="stylesheet" type="text/css" media="screen" />
  <script language="javascript" type="text/javascript" src="/scripts/header.js"></script>
  <?php if(isset($_SESSION['fcoder_hadmin_access']) && $_SESSION['fcoder_hadmin_access'] == 1) {?>
    <script language="javascript" type="text/javascript" src="/scripts/admin/admin.js"></script>
    <link href="/styles/admin/admin.css" rel="stylesheet" type="text/css" media="screen" />
  <?php } ?>
  <script language="javascript" type="text/javascript" src="/scripts/base64.js"></script>
  <link href="/styles/webdriveStyle.css" rel="stylesheet" type="text/css" />
  <script language="javaScript" type="text/javascript" src="/scripts/webdrive/webdriveScript.js"></script>
  <!-- <script language="javaScript" type="text/javascript" src="/scripts/webdrive/admin/usradminModule.js"></script> -->
  <!--*******************End Main Header script *****************************/-->
</head>

<body>
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="/">
          <img src="/images/webdrive.png" width="24" height="24" class="d-inline-block align-top" style="margin-right:5px" alt="">
          <span> Web Drive </span>
        </a>
      </div>
      <!-- <ul class="nav navbar-nav">
      <li class="active"><a href="#">Home</a></li>
      <li><a href="#">Page 1</a></li>
      <li><a href="#">Page 2</a></li>
    </ul> -->
      <ul class="nav navbar-nav navbar-right hd-frss">
        <?php
        if (isset($_GET['token']) == false && isset($_SESSION['fcoder_remember_token']) == true && isset($_SESSION['fcoder_userid']) == true && strpos($_SERVER['REQUEST_URI'], 'admin') === false) { ?>
          <li>
            <?php
            $ps = 0;
            $wstore = 0;
            $wshare = 0;
            $wstore_use = 0;
            $wshare_use = 0;
            if (isset($_SESSION['fcoder_wshare_limit']))
              $wshare = $_SESSION['fcoder_wshare_limit'];
            if (isset($_SESSION['wshare_data_bytes']) && isset($_SESSION['fcoder_wshare_limit_bytes']) && $_SESSION['fcoder_wshare_limit_bytes'] != 0) {
              $wshare_use = $_SESSION['wshare_data_bytes'];
              $ps = floor($wshare_use / $_SESSION['fcoder_wshare_limit_bytes'] * 100);
              $wshare_use =  $session->formatSizeUnits($wshare_use, 'MB');
            }
            if (isset($_SESSION['fcoder_wstorage_limit']))
              $wstore = $_SESSION['fcoder_wstorage_limit'];
            if (isset($_SESSION['fcoder_wstorage_data_bytes']) && isset($_SESSION['fcoder_wstorage_limit_bytes']) && $_SESSION['fcoder_wstorage_limit_bytes'] != 0) {
              $wstore_use = $_SESSION['fcoder_wstorage_data_bytes'];
              $ps = floor($wstore_use / $_SESSION['fcoder_wstorage_limit_bytes'] * 100);
              $wstore_use =  $session->formatSizeUnits($wstore_use, 'MB');
            }
            print '<div class="hd-fcss">
            <div id="webDriveStatus"><div id="wdrive-fs-percentage">' . $ps . '%</div></div>
            <div id="wrapper">
            <svg id="meter">
            <circle id="border" r="20" cx="50%" cy="50%" stroke="#e8e8e8" stroke-width="4" stroke-dasharray="100, 200" fill="none"/>
            <circle  id="avg" r="20" cx="50%" cy="50%" stroke="#7CCCE5" stroke-width="4" stroke-dasharray="100, 200" fill="none"/>
            <circle id="wdrive-fs-factor"  r="20" cx="50%" cy="50%" stroke="#e8e8e8" stroke-width="4" stroke-dasharray="100, 200" fill="none"/>
            </svg>
            </div>            
            <div class="hd-rldv" >
            <div class="hd-frsc" id="wdrive-fs-detail" >
            <span style="white-space:nowrap;"> <span id="wdrive-used-space-id" title="Total Used">' . $wstore_use . '</span> of <span id="wdrive-total-storage-id" title="Total Size of Web Drive">' . $wstore . '</span> MB [<span id="wdrive-myshare-size-id" title="The size of Shared Files">' . $wshare_use . '</span> of <span title="Limit of Shared">' . $wshare . '</span> MB]</span></div>
            </div>
            </div>';
            ?>
          </li>
          <li>
            <?php if (isset($_SESSION['fcoder_hadmin_access']) && $_SESSION['fcoder_hadmin_access'] == 1) { ?>
              <a href="/admin/" style="color:blueviolet"> <span class="glyphicon glyphicon-star-empty"></span><span style="color:darkcyan"> <?php echo $_SESSION['fcoder_name']; ?></span></a>
            <?php } else { ?>
              <a href="<?php echo "/?token=" . $_SESSION['fcoder_remember_token'] . "&uid=" . $_SESSION['fcoder_userid']; ?>" style="color:darkcyan"><span class="glyphicon glyphicon-user"></span><span> <?php echo $_SESSION['fcoder_name']; ?></span></a>
            <?php } ?>
          </li>
          <li><a href="#" onclick="logout()" style="color:orangered"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
          <?php } else if ((isset($_SESSION['fcoder_userid']) != false && $_SESSION['fcoder_userid'] != '') && (strpos($_SERVER['REQUEST_URI'], 'admin') !== false || (isset($_GET['token']) == true && isset($_SESSION['fcoder_remember_token']) == true && isset($_SESSION['fcoder_userid']) == true))) {
          if (strpos($_SERVER['REQUEST_URI'], 'admin') !== false && isset($_GET['token']) == true && isset($_SESSION['fcoder_remember_token']) == true && isset($_SESSION['fcoder_userid']) == true) { ?>
            <li><a href="/admin/" style="color:darkblue;"><span class="glyphicon glyphicon-step-backward"></span> Back </a></li>
          <?php } else { ?>
            <li><a href="/" style="color:darkblue;"><span class="glyphicon glyphicon-step-backward"></span> Back </a></li>
          <?php } ?>
          <li><a href="#" onclick="logout()" style="color:orangered"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
        <?php

        } else if (isset($_GET['token']) == true && (isset($_SESSION['fcoder_remember_token']) == false || isset($_SESSION['fcoder_userid']) == false)) { ?>
          <li><a href="#" onclick="event.stopPropagation();logintoHelpdesk();"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
        <?php } else { ?>
          <li><a href="#" onclick="event.stopPropagation();registerNow();"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
          <li><a href="#" onclick="event.stopPropagation();logintoHelpdesk();"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
        <?php } ?>
      </ul>
    </div>
  </nav>