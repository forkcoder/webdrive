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
        return isset($_SESSION['bbank_userid']);
      }
    }
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
  function formatSizeUnits($bytes, $unit)
  {
    if($unit!=''){
      if ($unit == 'GB')
        $bytes = number_format($bytes / 1073741824, 2);
      elseif ($unit == 'MB')
        $bytes = number_format($bytes / 1048576, 2);
      elseif ($unit == 'KB')
        $bytes = number_format($bytes / 1024, 2);
    }
    else {
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
    }
    return $bytes;
  }
  function setUserinfo($con)
  {
    $validUser = false;
    $u_userid = $_SESSION['bbank_userid']??'';
    $u_email_id = $_SESSION['bbank_email_id']??'';
    $sql = "SELECT * FROM bbank_users where userid='$u_userid' || email_id ='$u_email_id'";
    $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed ");
    $totalusers = mysqli_num_rows($result);
    if ($totalusers == 1) {
      $r = mysqli_fetch_assoc($result);
      $_SESSION['bbank_uid'] = $r['id'];
      $_SESSION['bbank_name'] = $r['name'];
      $_SESSION['bbank_gspace'] = $r['gspace'];
      $_SESSION['bbank_lspace'] = $r['lspace'];
      $_SESSION['bbank_actype'] = $r['actype'];
      $_SESSION['bbank_contact_no'] = $r['contact_no'];
      $_SESSION['bbank_email_id'] = $r['email_id'];
      $_SESSION['bbank_accountno'] = $r['accountno'];
      $_SESSION['bbank_upload_limit'] = $r['upload_limit'];

      $_SESSION['bbank_wstorage_data_bytes'] = $r['wstorage_data_bytes'];
      $_SESSION['bbank_wstorage_limit'] = $r['wstorage_limit'];
      $_SESSION['bbank_wstorage_limit_bytes'] = 1048576 * $r['wstorage_limit'];
      $_SESSION['bbank_wshare_limit'] = $r['wshare_limit'];
      $_SESSION['bbank_wshare_limit_bytes'] = 1048576 * $r['wshare_limit'];
      $_SESSION['bbank_wshare_data_bytes'] = $r['wshare_data_bytes'];
      $_SESSION['bbank_wshare_access'] = $r['wshare_access'];
      if ($r['wdrive_access'] == 1)
        $_SESSION['bbank_wdrive_types'] = [".csv", "application/vnd.openxmlformats-gspacedocument.spreadsheetml.sheet", "application/vnd.ms-excel", "application/pdf", "application/msword", "application/vnd.openxmlformats-gspacedocument.wordprocessingml.document", "application/vnd.ms-powerpoint", "application/vnd.openxmlformats-gspacedocument.presentationml.presentation", "application/vnd.ms-xpsdocument", "application/zip", "application/x-zip", "application/x-zip-compressed", "image/png", "image/jpeg", "image/gif"];
      else
        $_SESSION['bbank_wdrive_types'] = [];
      
      $_SESSION['bbank_wdrive_access'] = $r['wdrive_access'];
      $_SESSION['bbank_hadmin_access'] = $r['hadmin_access'];
      $_SESSION['bbank_avater_count'] = $r['avater_count'];
      $_SESSION['bbank_total_uploads'] = $r['total_uploads'];
      $_SESSION['bbank_upload_limit_bytes'] = 1048576 * $r['total_uploads'];
      $_SESSION['bbank_total_recipients'] = $r['total_recipients'];
      $_SESSION['bbank_file_livetime'] = $r['file_livetime'];
      if ($r['userid'] != '')
        $_SESSION['bbank_userid'] = $r['userid'];
      $_SESSION['bbank_genid'] = $r['genid'];
      $_SESSION['bbank_role'] = $r['role'];

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
