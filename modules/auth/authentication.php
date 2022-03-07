<?php
session_start();
require('../Servlets.php');
$session = new DBProxy();
$nid_reg = $_GET['nid_reg'];
$password = $_GET['password'];

/*****************************************
$data = json_decode(stripslashes($_POST['data']));

// here i would like use foreach:

foreach($data as $d){
echo $d;
}
 *****************************************/

$lk = isset($_SESSION['login_key']) ? $_SESSION['login_key'] : uniqid();
if ($_GET['auth_ph'] == "" || $_GET['auth_ph'] != $lk) {
  header("Location: /index.php");
  die();
} else if ($_GET['auth_ph'] == $_SESSION['login_key']) {
  $_SESSION['login_key'] = $_GET['auth_ph'];

  $res = 2;
  if (!isset($_SESSION['fcoder_userid'])) {
    $con = $session->initDBConnection();
    $login_key = $_SESSION['login_key'];
    $password = str_ireplace("replace_with_and", "&", $password);
    $password = str_ireplace("replace_with_hash", "#", $password);
    $password = str_ireplace("replace_with_add", "+", $password);
    

    $sql = "SELECT genid, name, password_hash, email_id, userid FROM fcoder_users where  userid = '$nid_reg' || email_id='$nid_reg'";
    $info = mysqli_query($con, $sql) or die("User info could not be fetched.");
    if (mysqli_num_rows($info)==1) {
      
      $user = mysqli_fetch_row($info);
      if(password_verify($password, $user[2])){
        $_SESSION['fcoder_genid'] = $genid = $user[0];
          $_SESSION['fcoder_name'] = $user[1];
          $_SESSION['fcoder_email_id']=$user[3];
          $_SESSION['fcoder_userid'] = $userid= $user[4];  //will be removed
          $res=1;
          $login_at = date('Y-m-d H:i:s', time());
    
          $client_browser = $_SESSION['clientInfo']['name'];
          $client_version = $_SESSION['clientInfo']['version'];
          $client_ipaddress = $_SESSION['clientInfo']['ipaddress'];
          $client_hostname = $_SESSION['clientInfo']['hostname'];
          $client_platform = $_SESSION['clientInfo']['platform'];
          $geo_gspace = $geo_country  = $_SESSION['clientInfo']['country'];
          $geo_city = $_SESSION['clientInfo']['city'];
          $geo_latitude = $_SESSION['clientInfo']['latitude']?:0.0;
          $geo_longitude = $_SESSION['clientInfo']['longitude']?:0.0;
          $geo_currency = $_SESSION['clientInfo']['currency'];
          $geo_currencycode = $_SESSION['clientInfo']['currencycode'];
          $geo_timezone = $_SESSION['clientInfo']['timezone'];
    
          $geo_gspace = $_SESSION['geo_gspace'];
    
          $sql = "INSERT INTO `fcoder_access_log` (`u_id`,`genid`,`login_at`,`access_status`, `client_browser`,`client_version`, `client_ipaddress`,`client_hostname`,`client_platform`, `geo_gspace`, `geo_country`, `geo_city`, `geo_latitude`, `geo_longitude`, `geo_currency`, `geo_currencycode`, `geo_timezone`, `login_key` )
			VALUES('$userid','$genid','$login_at','login','$client_browser','$client_version', '$client_ipaddress','$client_hostname','$client_platform', '$geo_gspace', '$geo_country', '$geo_city', $geo_latitude, $geo_longitude, '$geo_currency', '$geo_currencycode', '$geo_timezone', '$login_key')";
			    mysqli_query($con, $sql) or die("could not inserted to access log.");
          $_SESSION['access_key'] = $client_ipaddress.' '.$userid.' '.$login_at;
          echo $res;
      }
    }
    $session->closeDBConnection($con);
    /*************************************************************/
  } else {
    if ($_SESSION['fcoder_userid'] == $nid_reg)
      echo 2;
    else echo 3;
  }
} else echo 4;
