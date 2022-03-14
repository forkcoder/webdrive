<?php
include($_SERVER['DOCUMENT_ROOT'] . '/dbproxy/dbconnect.php');
class DotEnv{
  protected $path;
  public function __construct(string $path){
    if(!file_exists($path)) {
      throw new \InvalidArgumentException(sprintf('%s does not exist', $path));
    }
    $this->path = $path;
  }
  public function load() :void{
    if (!is_readable($this->path)) {
      throw new \RuntimeException(sprintf('%s file is not readable', $this->path));
    }
    $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
      if (strpos(trim($line), '#') === 0) {
        continue;
      }
      list($name, $value) = explode('=', $line, 2);
      $name = trim($name);
      $value = trim($value);
      if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
      }
    }
  }
}
class WDProxy{
  function __construct(){
    (new DotEnv($_SERVER['DOCUMENT_ROOT'] . '/.env'))->load();
  }
  function remote_validate($access_key){
    $server_address = strtok($access_key," ");
    $client_ipaddress = strtok(" ");
    $_SESSION['fcoder_userid'] = $userid = strtok(" ");
    $login_at = strtok("");
    if($this->getIPAddress() === $client_ipaddress){
      $curl_handle=curl_init();
      curl_setopt($curl_handle, CURLOPT_URL, $server_address.'modules/webdrive/drive_remote_access.php');
      curl_setopt($curl_handle, CURLOPT_POST, 1);
      curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "client_ipaddress=$client_ipaddress&userid=$userid&login_at=$login_at");
      curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
      $res = curl_exec($curl_handle);
      curl_close($curl_handle);
      if ((int)$res==1) {
        $con=$this->initDBConnection();
        if($this->userExist($con)){
          $this->closeDBConnection($con);
          return true;        
        }
        else {
          $this->closeDBConnection($con);
          return false;
        }
        // $this->saveBrowserInfo();
        // $ipx=explode('.', $this->getIPAddress());
        // if($ipx[0]!=127 && $ipx!=null && count($ipx)==4 && (isset($ipx[1]) && $ipx[1]!='168'))
        // $ip2= $ipx[1];
        // else
        // $ip2='168';
        // $_SESSION['vlan_features']= array();
        // $vlanfeature=array();

        // $sql = "SELECT * FROM fcoder_vlans v, gspace o where v.officeid=o.o_id and v.vlanid='$ip2'";
        // $result = mysqli_query($con, $sql);
        // if(mysqli_num_rows($result)==1)
        // $vlanfeature= $_SESSION['vlan_features'] = mysqli_fetch_assoc($result);
        // $_SESSION['geo_office']= $_SESSION['vlan_features']['o_name'];
      }
      else return false;
    }
    else return false;
  }
  function getIPAddress(){
    $ipaddress='';
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(!empty($_SERVER['HTTP_X_FORWARDED']))
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(!empty($_SERVER['HTTP_FORWARDED_FOR']))
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(!empty($_SERVER['HTTP_FORWARDED']))
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(!empty($_SERVER['REMOTE_ADDR']))
    $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
    $ipaddress = $_SERVER['REMOTE_ADDR'];
    return $ipaddress;
  }
  function closeDBConnection($con){
    mysqli_close($con);
  }
  function initDBConnection(){
    return db_connect(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'));
  }
  function formatSizeUnits($bytes){
    if ($bytes >= 1073741824)
    $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    elseif ($bytes >= 1048576)
    $bytes = number_format($bytes / 1048576, 2) . ' MB';
    elseif ($bytes >= 1024)
    $bytes = number_format($bytes / 1024, 2) . ' KB';
    elseif ($bytes > 1)
    $bytes = $bytes . ' bytes';
    elseif ($bytes == 1)
    $bytes = $bytes . ' byte';
    else
    $bytes = '0 bytes';
    return $bytes;
  }
  

  function userExist($con){
    $userid  = $_SESSION['fcoder_userid'];
    if(isset($_SESSION['fcoder_genid'])){
      $u_genid = $_SESSION['fcoder_genid'];
      $sql = "SELECT genid, name FROM fcoder_users where genid='$u_genid' and userid='$userid' and wdrive_access=1";
    }
    else
    $sql = "SELECT genid,name  FROM fcoder_users where userid='$userid' and wdrive_access=1";

    $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed ");
    if(mysqli_num_rows($result) == 1){
        $r  = mysqli_fetch_assoc($result);
      $_SESSION['fcoder_genid']= $r['genid'];
      $_SESSION['fcoder_name'] = $r['name'];
      $this->setUserinfo($con);
      return true;
    }
    else return false;
  }
  function setUserinfo($con){
    $validUser = false;
    $u_genid = $_SESSION['fcoder_genid'];
    $userid  = $_SESSION['fcoder_userid'];

    $sql = "SELECT * FROM fcoder_users where genid='$u_genid' and userid='$userid'";    // $sql="SELECT * FROM fcoder_users where userid='$userid'";
    $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed ");
    $totalusers = mysqli_num_rows($result);
    if ($totalusers == 1) {
      $validUser = true;
      $r = mysqli_fetch_assoc($result);
      $_SESSION['fcoder_uid'] = $r['id'];
      $_SESSION['fcoder_genid']=$r['genid'];
      $_SESSION['fcoder_name'] = $r['name'];
      $_SESSION['fcoder_gspace'] = $r['gspace'];
      $_SESSION['fcoder_department'] = $r['lspace'];
//      $_SESSION['fcoder_designation'] = $r['designation'];
    //   $_SESSION['fcoder_ipphone'] = $r['ipphone'];
      $_SESSION['fcoder_contact_no'] = $r['contact_no'];
      $_SESSION['fcoder_email_id'] = $r['email_id'];
      $_SESSION['fcoder_upload_limit'] =$r['upload_limit'];
      $_SESSION['fcoder_upload_limit_bytes'] = 1048576 * $r['upload_limit'];
      $_SESSION['fcoder_total_uploads'] = $r['total_uploads'];
      $_SESSION['fcoder_total_recipients'] =  $r['total_recipients'];
      $_SESSION['fcoder_file_livetime'] = $r['file_livetime'];

      $_SESSION['fcoder_wdrive_access'] =  $r['wdrive_access'];

      if ($r['wdrive_access'] == 1)
      $_SESSION['fcoder_wdrive_types'] = [".csv", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/vnd.ms-excel", "application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.ms-powerpoint", "application/vnd.openxmlformats-officedocument.presentationml.presentation", "application/vnd.ms-xpsdocument", "application/x-rar", "application/x-rar-compressed", "application/octet-stream", "application/zip", "application/x-zip", "application/x-zip-compressed", "image/png", "image/jpeg", "image/gif"];
      else
      $_SESSION['fcoder_wdrive_types'] = [];

      $_SESSION['fcoder_wstorage_limit'] = $r['wstorage_limit'];
      $_SESSION['fcoder_wstorage_data_bytes'] =  $r['wstorage_data_bytes'];
      $_SESSION['fcoder_wstorage_limit_bytes'] = 1048576 * $r['wstorage_limit'];

      $_SESSION['fcoder_wshare_limit'] = $r['wshare_limit'];
      $_SESSION['fcoder_wshare_access'] = $r['wshare_access'];
      $_SESSION['fcoder_wshare_data_bytes'] = $r['wshare_data_bytes'];
      $_SESSION['fcoder_wshare_limit_bytes'] = 1048576 *$r['wshare_limit'];
      $_SESSION['fcoder_avater_count'] = $r['avater_count'];
      $_SESSION['fcoder_userid'] = $r['userid'];
      $_SESSION['fcoder_role'] = $r['role'];
    }
    return $validUser;
  }
  function removeSession(){
    $helper = array_keys($_SESSION);
    foreach ($helper as $key){
      $_SESSION[$key]="";
      unset($_SESSION[$key]);
    }
    $helper = array_keys($_ENV);
    foreach ($helper as $key){
      $_ENV[$key]="";
      unset($_ENV[$key]);
    }

      session_unset();
      session_destroy();
      session_write_close();
      session_regenerate_id(true);
  }
}
