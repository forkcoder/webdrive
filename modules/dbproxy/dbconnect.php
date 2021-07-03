<?php
function db_connect()
{
  $SERVER = "localhost";
  $USER = "forkrswl_admin";
  $PASSWORD = "CHx{BV-t,WEe";
  $DATABASE = "forkrswl_drive";
  $con_status = true;
  $mnt = '<html lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Fork Drive</title>
<link rel="icon" href="images/forkdrive.png" type="image/png">
<link href="styles/style.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body>
<div id="main-page">
<div id="mainContentDiv" style="font-size:18px;color:darkred;justify-content:center;"> Currently Fork Drive is in Maintenance Mode. It will be back soon...</div>
<div id="footer-main-hdesk">
Developed and Maintained By: Fork Coder Inc. All Rights Reserved.
</div>
</div>
</body>
</html>';
  $con = @mysqli_connect($SERVER, $USER, $PASSWORD) or die($mnt);
  if ($con != FALSE && strlen($DATABASE) > 0) {
    if (@mysqli_select_db($con, $DATABASE)) {
      // set the correct charset encodin
      mysqli_query($con, 'SET NAMES \'utf8\'');
      mysqli_query($con, 'SET CHARACTER_SET \'utf8\'');
      return $con;
    } else
      return FALSE;
  } else
    return FALSE;
}
