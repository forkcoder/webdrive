<?php
function db_connect($SERVER ,$USER,$PASSWORD, $DATABASE){
  $mnt = '<html lang="en">
  <head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>BB ICT Help Desk</title>
  <link rel="icon" href="images/fcoder_bb_logo.png" type="image/png">
  <link href="styles/style.css" rel="stylesheet" type="text/css" media="screen" />
  </head>
  <body>
  <div id="main-page">
  <div id="banner-menu-fcoder">
  <div id="banner-fcoder-top">
  <div class="fcoderbblogo"><img  src="images/fcoder_bb_logo.png" style="width:76px;height:76px" border="0"/></div>
  <div style="padding:0 10px;flex-grow:1">
  <div>
  <span><span style="font-weight:bold;color: white;" >ICT Help Desk </span><span style="color: lightblue;font-size:0.5em" >Version 2.0 </span></span><br>
  <span style="font-weight:bold; color:lightgreen" >Bangladesh Bank</span>
  </div>
  </div>
  </div>
  </div>';
  if(getenv('APP_ENV')=='production')
  $mnt = $mnt.'<div id="mainContentDiv" style="color:darkred;justify-content:center;"> Currently Web Drive is in Maintenance Mode. Will be back soon...</div>';
  else 
  $mnt = $mnt.'<div id="mainContentDiv" style="color:darkred;justify-content:center;white-space:nowrap"> Development Server is in Maintenance Mode. Please visit <a href="https://helpdesk.bb.org.bd/">https://helpdesk.bb.org.bd/</a></div>';
  $mnt = $mnt.'<div id="footer-main-fcoder" class="nav navbar-nav">Developed by ICT CELL, BB Barishal and Powered by ICTIMMD, Head Office. All Rights Reserved.</div>
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
