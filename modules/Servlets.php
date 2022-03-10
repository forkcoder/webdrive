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
class DBProxy{
  function __construct(){
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
        return isset($_SESSION['fcoder_userid']);
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
  function initDBConnection(){
    return db_connect(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'));
  }
  function formatSizeUnits($bytes)
  {
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
  function setUserinfo($con){
    $validUser = false;
    $u_userid = $_SESSION['fcoder_userid'];
    $u_email_id = $_SESSION['fcoder_email_id'];
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
        $_SESSION['fcoder_ftransfer_types'] = [".csv", "application/vnd.openxmlformats-gspacedocument.spreadsheetml.sheet", "application/vnd.ms-excel", "application/pdf", "application/msword", "application/vnd.openxmlformats-gspacedocument.wordprocessingml.document", "application/vnd.ms-powerpoint", "application/vnd.openxmlformats-gspacedocument.presentationml.presentation", "application/vnd.ms-xpsdocument"];

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
    unset($_SESSION['geo_gspace']);
    unset($_SESSION['fcoder_category']);
    unset($_SESSION['device_status']);
    unset($_SESSION['fcoder_engineers']);
    unset($_SESSION['fcoder_actypes']);
    unset($_SESSION['gspaces']);
    unset($_SESSION['fcoder_lspaces']);
    unset($_SESSION['fcoder_role']);
    unset($_SESSION['fcoder_genid']);
    unset($_SESSION['fcoder_name']);

    unset($_SESSION['fcoder_uid']);
    unset($_SESSION['fcoder_gspace']);
    unset($_SESSION['fcoder_lspace']);
    unset($_SESSION['fcoder_actype']);
    unset($_SESSION['fcoder_contact_no']);
    unset($_SESSION['fcoder_email_id']);
    unset($_SESSION['fcoder_userid']);
    unset($_SESSION['fcoder_remember_token']);
    unset($_SESSION['fcoder_pwd']);       //not exist in table
    unset($_SESSION['fcoder_accountno']);
    unset($_SESSION['fcoder_role']);

    unset($_SESSION['fcoder_upload_limit']);
    unset($_SESSION['fcoder_total_uploads']);
    unset($_SESSION['fcoder_total_recipients']);
    unset($_SESSION['fcoder_file_livetime']);

    unset($_SESSION['fcoder_wstorage_limit']);
    unset($_SESSION['fcoder_wshare_limit']);
    unset($_SESSION['fcoder_wstorage_limit_bytes']);
    unset($_SESSION['fcoder_wshare_limit_bytes']);
    unset($_SESSION['fcoder_wstorage_data_bytes']);
    unset($_SESSION['fcoder_wshare_data_bytes']);
    unset($_SESSION['fcoder_wdrive_access']);
    unset($_SESSION['fcoder_wshare_access']);
    unset($_SESSION['fcoder_ftransfer_access']);
    unset($_SESSION['fcoder_uassets_access']);
    unset($_SESSION['fcoder_cschedule_access']);
    unset($_SESSION['fcoder_hadmin_access']);
    unset($_SESSION['fcoder_afcoder_access']);
    unset($_SESSION['fcoder_avater_count']);

    unset($_SESSION['uassets_create_asset']);
    unset($_SESSION['uassets_create_workstation']);
    unset($_SESSION['uassets_dist_asset']);
    unset($_SESSION['uassets_dist_user']);
    unset($_SESSION['uassets_report_asset']);

    unset($_SESSION['uassets_live_station']);
    unset($_SESSION['uassets_lock_station']);
    unset($_SESSION['uassets_usage_mode']);
    unset($_SESSION['uassets_workstations']);
    unset($_SESSION['uassets_maxuid']);

    unset($_SESSION['uassets_types']);
    unset($_SESSION['uassets_brands']);
    unset($_SESSION['uassets_models']);
    unset($_SESSION['uassets_vendors']);
  }
}
