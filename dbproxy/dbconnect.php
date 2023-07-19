<?php
function db_connect($SERVER ,$USER,$PASSWORD, $DATABASE){
  $mnt = '<html lang="en">
  <head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Web Drive</title>
  <link rel="icon" href="images/webdrive.png" type="image/png">
  <link href="styles/style.css" rel="stylesheet" type="text/css" media="screen" />
  </head>
  <body>
  <div id="main-page">';
  $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
  if(getenv('APP_ENV')=='production')
  $mnt = $mnt.'<div id="mainContentDiv" style="color:darkred;justify-content:center;"> Currently Web Drive is in Maintenance Mode. Will be back soon...</div>';
  else 
  $mnt = $mnt.'<div id="mainContentDiv" style="color:darkred;justify-content:center;white-space:nowrap"> Development Server is in Maintenance Mode. Please visit <a href="'.$link.'">'.$link.'</a></div>';
  $mnt = $mnt.'<div id="footer-main-fcoder" class="nav navbar-nav">Developed by ForkCoder Inc.. All Rights Reserved.</div>
  </div>
  </body>
  </html>';
  $con = @mysqli_connect($SERVER, $USER, $PASSWORD) or die($mnt);
  if ($con != FALSE && strlen($DATABASE) > 0) {
    if (@mysqli_select_db($con, $DATABASE)) {
      // set the correct charset encodin
      mysqli_query($con, 'SET NAMES \'utf8\'');
      // mysqli_query($con, 'SET CHARACTER_SET \'utf8\'');
      return $con;
    } else
    return FALSE;
  } else
  return FALSE;
}
