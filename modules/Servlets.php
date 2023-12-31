<?php
include($_SERVER['DOCUMENT_ROOT'] . '/dbproxy/dbconnect.php');
class DotEnv
{
  protected $path;
  public function __construct(string $path)
  {
    if (!file_exists($path)) {
      throw new \InvalidArgumentException(sprintf('%s does not exist', $path));
    }
    $this->path = $path;
  }
  public function load(): void
  {
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
class DBProxy
{
  function __construct()
  {
    (new DotEnv($_SERVER['DOCUMENT_ROOT'] . '/.env'))->load();
  }
  function validate($auth_ph, $ph)
  {
    $lk = isset($_SESSION['login_key']) ? $_SESSION['login_key'] : uniqid();
    if ($auth_ph == "" || $auth_ph != $lk) {
      return false;
    } else if ($auth_ph == $_SESSION['login_key']) {
      $sk = isset($_SESSION['session_key']) ? $_SESSION['session_key'] : uniqid();
      if ($ph == "" || $ph != $sk) {
        return false;
      } else if ($ph == $_SESSION['session_key']) {
        unset($_SESSION['session_key']);
        return isset($_SESSION['fcoder_userid']) ? true : false;
      }
    }
  }
  function uploadCompleted($size,$u_genid,$userid)
  {
    $con = $this->initDBConnection();
    $wdrive_projected_size = $_SESSION['fcoder_wstorage_data_bytes'] + $size;
    $_SESSION['fcoder_wstorage_data_bytes'] = $wdrive_projected_size;
    $sql = "UPDATE fcoder_users set wstorage_data_bytes=$wdrive_projected_size where genid='$u_genid' and userid='$userid' and wdrive_access=1";
    $result = mysqli_query($con, $sql) or die("Updating data size info to DB is failed");
    $this->closeDBConnection($con);
  }
  function login($con, $login_id, $password, $request)
  {
    $sql = "SELECT genid, name, password_hash, email_id, userid, hadmin_access FROM fcoder_users where  (userid = '$login_id' && userid!='' && userid!=1) || (email_id='$login_id'  && email_id!='' && email_id!=1)  limit 1";
    $info = mysqli_query($con, $sql) or die("User info could not be fetched.");
    if (mysqli_num_rows($info) == 1) {
      $user = mysqli_fetch_assoc($info);
      if (password_verify($password, $user['password_hash'])) {
        $_SESSION['fcoder_genid'] = $genid = $user['genid'];
        $_SESSION['fcoder_name'] = $user['name'];
        $_SESSION['fcoder_email_id'] = $user['email_id'];
        $_SESSION['fcoder_userid'] = $userid = $user['userid'];
        $_SESSION['fcoder_hadmin_access'] = $user['hadmin_access'];
        if ($request == 'login') {
          $login_at = date('Y-m-d H:i:s', time());
          $client_browser = $_SESSION['clientInfo']['name'];
          $client_version = $_SESSION['clientInfo']['version'];
          $client_ipaddress = $_SESSION['clientInfo']['ipaddress'];
          $client_hostname = $_SESSION['clientInfo']['hostname'];
          $client_platform = $_SESSION['clientInfo']['platform'];
          $geo_gspace = $geo_country  = $_SESSION['clientInfo']['country'];
          $geo_city = $_SESSION['clientInfo']['city'];
          $geo_latitude = $_SESSION['clientInfo']['latitude'] ?: 0.0;
          $geo_longitude = $_SESSION['clientInfo']['longitude'] ?: 0.0;
          $geo_currency = $_SESSION['clientInfo']['currency'];
          $geo_currencycode = $_SESSION['clientInfo']['currencycode'];
          $geo_timezone = $_SESSION['clientInfo']['timezone'];
          $geo_gspace = $_SESSION['geo_gspace'];
          $login_key = $_SESSION['login_key'];
          $sql = "INSERT INTO `fcoder_access_log` (`u_id`,`genid`,`login_at`,`access_status`, `client_browser`,`client_version`, `client_ipaddress`,`client_hostname`,`client_platform`, `geo_gspace`, `geo_country`, `geo_city`, `geo_latitude`, `geo_longitude`, `geo_currency`, `geo_currencycode`, `geo_timezone`, `login_key` )
			VALUES('$userid','$genid','$login_at','login','$client_browser','$client_version', '$client_ipaddress','$client_hostname','$client_platform', '$geo_gspace', '$geo_country', '$geo_city', $geo_latitude, $geo_longitude, '$geo_currency', '$geo_currencycode', '$geo_timezone', '$login_key')";
          mysqli_query($con, $sql) or die("could not inserted to access log.");
          $_SESSION['access_key'] = $client_ipaddress . ' ' . $userid . ' ' . $login_at;
        }
        $this->reloadSession($con);
        return 0;
      } else return 1;
    } else return 2;
  }
  function reloadSession()
  {
    $con = $this->initDBConnection();
    /********************  start define Global Features ***************************/
    $this->setUserinfo($con);
    $this->closeDBConnection($con);
  }
  function closeDBConnection($con)
  {
    mysqli_close($con);
  }
  function initDBConnection()
  {
    return db_connect(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'));
  }
  function formatSizeUnits($bytes, $unit, $pre = 2)
  {
    if ($unit != '') {
      if ($unit == 'GB')
        $bytes = number_format($bytes / 1073741824, $pre);
      elseif ($unit == 'MB')
        $bytes = number_format($bytes / 1048576, $pre);
      elseif ($unit == 'KB')
        $bytes = number_format($bytes / 1024, $pre);
    } else {
      if ($bytes >= 1073741824)
        $bytes = number_format($bytes / 1073741824, $pre) . ' GB';
      elseif ($bytes >= 1048576)
        $bytes = number_format($bytes / 1048576, $pre) . ' MB';
      elseif ($bytes >= 1024)
        $bytes = number_format($bytes / 1024, $pre) . ' KB';
      elseif ($bytes > 1)
        $bytes = $bytes . ' bytes';
      elseif ($bytes == 1)
        $bytes = $bytes . ' byte';
      else
        $bytes = '0 bytes';
    }
    return $bytes;
  }
  function setUserinfo($con)
  {
    $validUser = false;
    $u_userid = $_SESSION['fcoder_userid'] ?? '';
    $u_email_id = $_SESSION['fcoder_email_id'] ?? '';
    $sql = "SELECT * FROM fcoder_users where userid='$u_userid' || email_id ='$u_email_id'";
    $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed ");
    $totalusers = mysqli_num_rows($result);
    if ($totalusers == 1) {
      $r = mysqli_fetch_assoc($result);
      $_SESSION['fcoder_uid'] = $r['id'];
      $_SESSION['fcoder_name'] = $r['name'];
      $_SESSION['fcoder_gspace'] = $r['gspace'];
      $_SESSION['fcoder_lspace'] = $r['lspace'];
      $_SESSION['fcoder_actype'] = $r['actype'];
      $_SESSION['fcoder_contact_no'] = $r['contact_no'];
      $_SESSION['fcoder_email_id'] = $r['email_id'];
      $_SESSION['fcoder_accountno'] = $r['accountno'];
      $_SESSION['fcoder_upload_limit'] = $r['upload_limit'];

      $_SESSION['fcoder_wstorage_data_bytes'] = $r['wstorage_data_bytes'];
      $_SESSION['fcoder_wstorage_limit'] = $r['wstorage_limit'];
      $_SESSION['fcoder_wstorage_limit_bytes'] = 1048576 * $r['wstorage_limit'];
      $_SESSION['fcoder_wshare_limit'] = $r['wshare_limit'];
      $_SESSION['fcoder_wshare_limit_bytes'] = 1048576 * $r['wshare_limit'];
      $_SESSION['fcoder_wshare_data_bytes'] = $r['wshare_data_bytes'];
      $_SESSION['fcoder_wshare_access'] = $r['wshare_access'];
      if ($r['wdrive_access'] == 1)
        $_SESSION['fcoder_wdrive_types'] = [".csv", "application/vnd.openxmlformats-gspacedocument.spreadsheetml.sheet", "application/vnd.ms-excel", "application/pdf", "application/msword", "application/vnd.openxmlformats-gspacedocument.wordprocessingml.document", "application/vnd.ms-powerpoint", "application/vnd.openxmlformats-gspacedocument.presentationml.presentation", "application/vnd.ms-xpsdocument", "application/zip", "application/x-zip", "application/x-zip-compressed", "image/png", "image/jpeg", "image/gif"];
      else
        $_SESSION['fcoder_wdrive_types'] = [];

      $_SESSION['fcoder_wdrive_access'] = $r['wdrive_access'];
      $_SESSION['fcoder_hadmin_access'] = $r['hadmin_access'];
      $_SESSION['fcoder_avater_count'] = $r['avater_count'];
      $_SESSION['fcoder_total_uploads'] = $r['total_uploads'];
      $_SESSION['fcoder_upload_limit_bytes'] = 1048576 * $r['total_uploads'];
      $_SESSION['fcoder_total_recipients'] = $r['total_recipients'];
      $_SESSION['fcoder_file_livetime'] = $r['file_livetime'];
      if ($r['userid'] != '')
        $_SESSION['fcoder_userid'] = $r['userid'];
      $_SESSION['fcoder_genid'] = $r['genid'];
      $_SESSION['fcoder_role'] = $r['role'];
      $_SESSION['fcoder_remember_token'] = $r['remember_token'];
      $validUser = true;
    }
    return $validUser;
  }
  function get($id)
  {
    return $_SESSION[$id];
  }
  function set($id, $value)
  {
    $_SESSION[$id] = $value;
  }
  function unset($id)
  {
    unset($_SESSION[$id]);
  }
  function isset($id)
  {
    return isset($_SESSION[$id]);
  }
  function removeSession()
  {
    $helper = array_keys($_SESSION);
    foreach ($helper as $key) {
      $_SESSION[$key] = "";
      unset($_SESSION[$key]);
    }
    session_unset();
    session_destroy();
    session_write_close();
    // session_regenerate_id(true);
  }
}
