<!--******************* Fork Drive ****-->
<!--******************* Version 1.0 *****************************-->
<!--******************* Version Date 01.07.2021 *****************-->
<!--******************* Developed and Maintained By: Fork Coder Inc. *****************-->
<?php
if (!isset($_SESSION))
  session_start();
?>
<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" name="viewport" content="width=device-width, initial-scale=1">
  <title>Fork Drive</title>
  <link rel="icon" href="images/forkdrive.png" type="image/png">
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
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link href="styles/style.css" rel="stylesheet" type="text/css" media="screen" />
  <script language="javascript" type="text/javascript" src="scripts/header.js"></script>
  <script language="javascript" type="text/javascript" src="scripts/base64.js"></script>
  <link href="styles/webdriveStyle.css" rel="stylesheet" type="text/css" />
  <script language="javaScript" type="text/javascript" src="scripts/webdrive/webdriveScript.js"></script>
  <!--*******************End Main Header script *****************************/-->
</head>

<body>
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="#">Fork Drive</a>
      </div>
      <!-- <ul class="nav navbar-nav">
      <li class="active"><a href="#">Home</a></li>
      <li><a href="#">Page 1</a></li>
      <li><a href="#">Page 2</a></li>
    </ul> -->
      <ul class="nav navbar-nav navbar-right">
        <?php if (isset($_SESSION['fcoder_remember_token'])==false && isset($_SESSION['fcoder_userid'])==true) { ?>
          <li><a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION['fcoder_name']; ?></a></li>
            <li><a href="#"  onclick="logout()"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
        <?php } else { ?>
          <?php if (isset($_SESSION['fcoder_remember_token'])) { ?>
           <li><a href="#" onclick="logout()"><span class="glyphicon glyphicon-log-out" ></span> Logout</a></li>
          <?php } else { ?>
            <li><a href="#" onclick="event.stopPropagation();registerNow();"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
            <li><a href="#"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
          <?php } ?>
        <?php } ?>
      </ul>
    </div>
  </nav>