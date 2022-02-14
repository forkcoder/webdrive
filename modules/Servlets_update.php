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
  function validate($auth_ph, $ph){
    $lk = isset($_SESSION['login_key']) ? $_SESSION['login_key'] : uniqid();
    if ($auth_ph == "" || $auth_ph != $lk) {
      return false;
    } else if ($auth_ph == $_SESSION['login_key']) {
      $sk = isset($_SESSION['session_key']) ? $_SESSION['session_key'] : uniqid();
      if ($ph == "" || $ph != $sk) {
        return false;
      } else if ($ph == $_SESSION['session_key']) {
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > getenv('APP_SESSION_EXP'))) {
        $this->removeSession();
        }
        $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
        // unset($_SESSION['session_key']); //stop genreating session key for every request
        // if(isset($_SESSION['fcoder_userid'])){
        //     return $this->userExist($con);
        // }
        // else return false;
        return isset($_SESSION['fcoder_userid']);
      }
    }
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
  function saveBrowserInfo(){
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";
    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
      $platform = 'linux';
    }elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
      $platform = 'mac';
    }elseif (preg_match('/windows|win32/i', $u_agent)) {
      $platform = 'windows';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
      $bname = 'Internet Explorer';
      $ub = "MSIE";
    }elseif(preg_match('/Firefox/i',$u_agent)){
      $bname = 'Mozilla Firefox';
      $ub = "Firefox";
    }elseif(preg_match('/OPR/i',$u_agent)){
      $bname = 'Opera';
      $ub = "Opera";
    }elseif(preg_match('/Chrome/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
      $bname = 'Google Chrome';
      $ub = "Chrome";
    }elseif(preg_match('/Safari/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
      $bname = 'Apple Safari';
      $ub = "Safari";
    }elseif(preg_match('/Netscape/i',$u_agent)){
      $bname = 'Netscape';
      $ub = "Netscape";
    }elseif(preg_match('/Edge/i',$u_agent)){
      $bname = 'Edge';
      $ub = "Edge";
    }elseif(preg_match('/Trident/i',$u_agent)){
      $bname = 'Internet Explorer';
      $ub = "MSIE";
    }

    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
      // we have no matching number just continue
    }
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
      //we will have two since we are not using 'other' argument yet
      //see if version is before or after the name
      if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
        $version= $matches['version'][0];
      }else {
        $version= $matches['version'][1];
      }
    }else {
      $version= $matches['version'][0];
    }
    $_SESSION['bug_fixing']=1;

    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    $ipaddress = $this->getIPAddress();

    $hostname = $ipaddress;
    if($hostname==false || $hostname==$ipaddress)
    $hostname= '--';

    $_SESSION['clientInfo']= array(
      'userAgent' => $u_agent,
      'name'      => $bname,
      'version'   => $version,
      'platform'  => $platform,
      'pattern'    => $pattern,
      'ipaddress' => $ipaddress,
      'hostname' => $hostname,
    );
  }
  function reloadSession($con){
    /********************  start define Global Features ***************************/
    $this->setCategory($con);
    $this->setDeviceStatus($con);
    if ($this->setUserinfo($con) == true) {
      $this->reloadRequests($con);
      $this->setWSParms($con);
      if($_SESSION['fcoder_hadmin_access']  == 1 || $_SESSION['fcoder_afcoder_access']  == 1 || $_SESSION['fcoder_uassets_access']  == 1 || $_SESSION['fcoder_mdesk_access']  == 1 )
      $this->setUAParms($con);
    }
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
  function setCategory($con){
    /*Start select category*/
    $categories = array();
    $cgroups = array();
    $sql = "SELECT ic.category, mg.name FROM fcoder_categories ic, fcoder_category_group mg where ic.group=mg.id and ic.isactive=1 order by mg.name, ic.category asc ";
    $result = mysqli_query($con, $sql) or die("Incident Category could not fetched");
    while ($row = mysqli_fetch_row($result)) {
      $name = $row[0];
      $group = $row[1];
      if (in_array($group, array_keys($categories)) === false)
      $categories[$group] = array();
      $categories[$group][] = $name;
      $cgroups[$name]=$group;

    }
    $_SESSION['fcoder_category'] = $categories;
    $_SESSION['fcoder_cgroup'] = $cgroups;
    /*end select category*/
  }
  /* Start Device Status*/
  function setDeviceStatus($con){
    $deviceStatus[] = array();
    $sql = "SELECT `device_status` FROM `fcoder_incident_device_status`";
    $result = mysqli_query($con, $sql) or die("Device Status could not be fetched");
    $total_device_status = mysqli_num_rows($result);
    $i = 0;
    while ($row = mysqli_fetch_row($result)) {
      $deviceStatus[$i] = $row[0];
      $i++;
    }
    $_SESSION['device_status'] = $deviceStatus;
  }
  /* End Device Status*/
  /*Start IT Staff*/
  function setEngineer($con){
    $sql = "SELECT u_id, name, gspace, dept, desg, s_desg, phone, mobile, u_role, email_id, isactive FROM fcoder_engineers order by IF(FIELD(s_desg, 'ED','CME','PME','SME', 'OM','ME', 'COS','AME','AD', 'SDECO', 'DECO')=0,'AD',s_desg) desc, substring(genid,4) asc";
    $result = mysqli_query($con, $sql) or die("IT Staff Info could not be fetched");
    $itstaff = array();
    $itsInfo = array();
    while ($r = mysqli_fetch_assoc($result)) {
      $id = $r['u_id'];
      $itstaff[$id] = array();
      $itstaff[$id] = $r;
      $itstaff[$id]['ackcount'] = 0;
      $itstaff[$id]['actcount'] = 0;
      $itstaff[$id]['lockedby'] = '';
      $itstaff[$id]['open'] = 0;
      $itstaff[$id]['acknowledge'] = 0;
      $itstaff[$id]['assign'] = 0;
      $itstaff[$id]['claim'] = 0;
      $itsOffice = $r['office'] ?? 'Head Office';
      $itsDepartment = $r['dept'] ?? 'ICTIMMD';
      $itsRole = $r['u_role'] ?? 'Engineer';

      if (!isset($itsInfo[$itsOffice][$itsDepartment][$itsRole]))
      $itsInfo[$itsOffice][$itsDepartment][$itsRole] = array();
      $itsInfo[$itsOffice][$itsDepartment][$itsRole][$id] = $r['name'];
    }
    $_SESSION['fcoder_engineers'] = $itstaff;
    $_SESSION['fcoder_engineers_deptwise'] = $itsInfo;
  }
  /*end IT Staff*/
  function setOrgunit($con){
    /*start select Office*/
    $offices = array();
    $sql = "SELECT `o_name`, `o_id`,`o_shortname` FROM `office` order by `o_id`=1 desc, `o_name` asc";
    $result = mysqli_query($con, $sql) or die("Office could not be fetched");
    while ($row = mysqli_fetch_row($result)) {
      $offices[$row[0]] = $row[2];
    }
    $_SESSION['offices'] = $offices;

    $sql = "SELECT b.d_name, b.d_shortname, c.o_name, b.isactive from fcoder_departments b, gspace c where b.office_index=c.o_id order by CASE WHEN b.d_name = 'Not Applicable' THEN 1 ELSE 0 END, b.d_name ASC";
    $result = mysqli_query($con, $sql) or die("Department could not be fetched");
    $officewisedept = array();
    $alldepartments = array();
    $count = 0;
    while ($r = mysqli_fetch_row($result)) {
      $officename = $r[2];
      $dname = $r[0];
      $sname = $r[1];
      $dup = array(
        'name' => $dname,
        'sname' => $sname
      );
      if ($r[3] == 1)
      $officewisedept[$officename][]= $dup;
      $alldepartments[$dname] = $sname;
      $count++;
    }
    $_SESSION['officewisedept'] = $officewisedept;
    $_SESSION['fcoder_departments'] = $alldepartments;

    $sql = "SELECT a.name, b.d_name, c.o_name from fcoder_sections a, fcoder_departments b, gspace c where a.d_id = b.d_id and b.office_index=c.o_id order by a.name ASC";
    $result = mysqli_query($con, $sql) or die("Section could not be fetched");
    $sections = array();
    while ($r = mysqli_fetch_row($result)) {
      $officename = $r[2];
      $deptname = $r[1];
      $sections[$officename][$deptname][] = $r[0];
    }
    $_SESSION['officewisedeptwisesec'] = $sections;
  }
  /*start select Designation*/
  function setDesignation($con){
    $designations = array();
    $sql = "SELECT * FROM `fcoder_designations`";
    $result = mysqli_query($con, $sql) or die("Designation could not be fetched");
    $desg_order = "";
    while ($row = mysqli_fetch_assoc($result)) {
      $desg =$row['designation'];
      if($desg_order!="")
      $desg_order = ", ". $desg_order;
      $desg_order="'$desg'".$desg_order;
      $designations[$desg] = $row ;
    }
    $_SESSION['fcoder_designations'] = $designations;
    $_SESSION['fcoder_desg_order'] = $desg_order;
  }
  /*End select Designation*/
  function setUAParms($con){
    $_SESSION['uassets_maxuid'] = array(
      'Head Office' => 'hodmaxuid',
      'Motijheel' => 'motmaxuid',
      'Chattogram' => 'ctgmaxuid',
      'Khulna' => 'khlmaxuid',
      'Rajshahi' => 'rajmaxuid',
      'Sylhet' => 'sylmaxuid',
      'Bogura' => 'bogmaxuid',
      'Rangpur' => 'rngmaxuid',
      'Barishal' => 'barmaxuid',
      'Sadarghat' => 'sdgmaxuid',
      'Mymensingh' => 'mynmaxuid'
    );
    $rank = $_SESSION['fcoder_designations'][$_SESSION['fcoder_designation']]['dsg_depth'];
    $uassets_types = array();
    $result = mysqli_query($con, "SELECT *  from fcoder_asset_types order by id ASC") or die("Asset Type could not be fetched");
    while ($r = mysqli_fetch_assoc($result)) {
      $type = $r['name'];
      if ($type == 'Router' || $type == 'Switch' || $type == 'Server' || $type == 'Other') {
        if( $_SESSION['fcoder_hadmin_access']== 1){
          $uassets_types[$type]['maxuid'] = $r['maxuid'];
          $uassets_types[$type]['code'] = $r['typecode'];
          $uassets_types[$type]['sname'] = $r['sname'];
          foreach ($_SESSION['uassets_maxuid'] as $office => $key)
          $uassets_types[$type][$key] = $r[$key];
        }
      } else if ($type == 'TabletPC') {
        if ($rank <= 3 || ( $_SESSION['fcoder_hadmin_access']== 1)) {
          $uassets_types[$type]['maxuid'] = $r['maxuid'];
          $uassets_types[$type]['code'] = $r['typecode'];
          $uassets_types[$type]['sname'] = $r['sname'];
          foreach ($_SESSION['uassets_maxuid'] as $office => $key)
          $uassets_types[$type][$key] = $r[$key];
        }
      } else if ($type == 'PAD') {
        if ($rank <= 2 || ( $_SESSION['fcoder_hadmin_access']== 1)) {
          $uassets_types[$type]['maxuid'] = $r['maxuid'];
          $uassets_types[$type]['code'] = $r['typecode'];
          $uassets_types[$type]['sname'] = $r['sname'];
          foreach ($_SESSION['uassets_maxuid'] as $office => $key)
          $uassets_types[$type][$key] = $r[$key];
        }
      } else if ($type == 'IPPhone') {
        if ($rank <= 9 || ( $_SESSION['fcoder_hadmin_access']== 1)) {
          $uassets_types[$type]['maxuid'] = $r['maxuid'];
          $uassets_types[$type]['code'] = $r['typecode'];
          $uassets_types[$type]['sname'] = $r['sname'];
          foreach ($_SESSION['uassets_maxuid'] as $office => $key)
          $uassets_types[$type][$key] = $r[$key];
        }
      } else {
        $uassets_types[$type]['maxuid'] = $r['maxuid'];
        $uassets_types[$type]['code'] = $r['typecode'];
        $uassets_types[$type]['sname'] = $r['sname'];
        foreach ($_SESSION['uassets_maxuid'] as $office => $key)
        $uassets_types[$type][$key] = $r[$key];
      }
    }
    $_SESSION['uassets_types'] = $uassets_types;
    $uassets_brands = array();
    $sql = "SELECT * from fcoder_asset_brands order by id ASC";
    $result = mysqli_query($con, $sql) or die("Brand could not be fetched");
    while ($r = mysqli_fetch_assoc($result)) {
      foreach ($uassets_types as $key => $value) {
        if ($r[$key] != -1) {
          $uassets_brands[$key][$r['name']] = $r[$key];
          $uassets_brands['map'][$key][] = $r['name'];
          $uassets_brands['code'][$r['name']] = $r['shortname'];
        }
      }
    }
    $_SESSION['uassets_brands'] = $uassets_brands;

    $uassets_models = array();
    $sql = "SELECT a.name, b.name, c.name, a.shortname from fcoder_asset_models a, fcoder_asset_brands b, fcoder_asset_types c where a.brand_id = b.id and a.type_id = c.id order by a.id ASC";
    $result = mysqli_query($con, $sql) or die("Model could not be fetched");
    while ($r = mysqli_fetch_row($result)) {
      $uassets_models[$r[2]][$r[1]][] = $r[0];
    }
    $_SESSION['uassets_models'] = $uassets_models;

    $uassets_vendors = array();
    $sql = "SELECT id, name from fcoder_asset_vendors order by id ASC";
    $result = mysqli_query($con, $sql) or die("Vendor could not be fetched");
    while ($r = mysqli_fetch_row($result)) {
      $uassets_vendors[$r[0]] = $r[1];
    }
    $_SESSION['uassets_vendors'] = $uassets_vendors;
  }
  function getRemarks($category, $option, $oldval, $newval, $department, $office){
    $remarks = '';
    if ($category == 'Workstation') {
      if ($option == 'New')  $remarks = '<a class="ws-optionNew">New Workstation </a> for ' . $department . ', BB ' . $office;
      else if ($option == 'Leave') $remarks = '<a class="ws-optionLeave">Leave Workstation </a> (ID: ' . $oldval . ') at ' . $department . ', BB ' . $office;
      else if ($option == 'Change') {
        if ($newval !== "")
        $remarks = '<a class="ws-optionChange"> Change Workstation </a> from (ID:' . $oldval . ') to (ID:' . $newval . ') at ' . $department . ', BB ' . $office;
        else {
          if ($oldval != "")
          $remarks = '<a class="ws-optionChange"> Change Workstation </a> from (ID:' . $oldval . ') to Any at ' . $department . ', BB ' . $office;
          else
          $remarks = '<a class="ws-optionChange"> Change Workstation </a> to Any at ' . $department . ', BB ' . $office;
        }
      }
    } else if ($category == 'Asset') {
      if ($option == 'New')  $remarks = '<a class="ws-optionNew">New ' . $newval . ' </a> for Wrokstation (ID:' . $oldval . ') at ' . $department . ', BB ' . $office;
      else if ($option == 'Repair') $remarks = '<a class="ws-optionRepair">Repair ' . $newval . '</a> for Wrokstation (ID:' . $oldval . ') at ' . $department . ', BB ' . $office;
      else if ($option == 'Change') $remarks = '<a class="ws-optionChange">Change ' . $newval . '</a> for Wrokstation (ID:' . $oldval . ') at ' . $department . ', BB ' . $office;
      else if ($option == 'Return') $remarks = '<a class="ws-optionReturn">Return ' . $newval . '</a> for Wrokstation (ID:' . $oldval . ') at ' . $department . ', BB ' . $office;
    }
    return $remarks;
  }
  function autoRequestEntry($category, $option, $oldval, $newval, $changeId){
    $ux = '<div class="ws-user-auto-request-style">';
    if ($category == 'Workstation') {
      $changeId = "ws-request-" . $changeId;
      $ux .= '<div id="' . $changeId . '-option"  class="ws-workstation-request-option" onclick="event.stopPropagation();this.classList.remove(\'errorinput\');mydeskModule.selectWorkstationRequest(\'' . $changeId . '\',\'' . $option . '\',\'' . $oldval . '\',\'' . $newval . '\')">';
      $ux .= '<input type="checkbox" id="' . $changeId . '"  name="ws-workstation-request-check" value="' . $option . '"/>
      <label id="' . $changeId . '-label-id"  for="ws-workstation-request-check">' . $option . '</label>';
      if ($option != 'New') {
        if ($option == 'Change') {
          if ($oldval != '' && $newval != '')
          $ux .= 'ID:<span style="color:darkred;font-weight:bold">' . $oldval . ' </span> to ID:<span style="color:darkred;font-weight:bold">' . $newval . ' </span>';
          else if ($oldval != '' && $newval == '')
          $ux .= 'ID:<span style="color:darkred;font-weight:bold">' . $oldval . ' </span> to
          <input type="text" id="ws-change-value" class="roundCornerInput" style="margin:auto 3px;width:135px;" placeholder="Type Workstation ID" value="" maxlength="20">';
          else if ($oldval == '' && $newval != '')
          $ux .= 'ID:<span style="color:darkred;font-weight:bold">' . $newval . ' </span>';
          else if ($oldval == '' && $newval == '')
          $ux .= ' to <input type="text" id="ws-change-value" class="roundCornerInput" style="margin:auto 3px;width:135px;" placeholder="Type Workstation ID" value="" maxlength="20">';
        } else
        $ux .= 'ID:<span style="color:darkred;font-weight:bold"> ' . $oldval . ' </span>';
      }
      $ux .= '</div>';
    }
    $ux .= '</div>';
    return $ux;
  }
  function getBackButton(){
    return '<span class="backtoButton" onclick="mydeskModule.selectOption(event, \'ws-mydesk-id\', \'ws-mydesk-btn-id\')"> My Desk </span>';
  }
  function requestHandler($category, $option){
    $handler = '';
    if ($_SESSION['geo_office'] == 'Head Office') {
      $handler = 'GS Admin';
    } else {
      if ($category == 'Office') {
        $handler = 'HR Admin';
      } else if ($category == 'Workstation') {
        $handler = 'HR Admin';
      } else if ($category == 'Asset') {
        $handler = 'DS Admin';
      }
    }
    return $handler;
  }
  function reloadRequests($con){
    $requests = array();
    $uid = $_SESSION['fcoder_uid'];
    $genid = $_SESSION['fcoder_genid'];
    $geo_office = $_SESSION['geo_office'];
    $sql = "SELECT * FROM fcoder_user_requests where iuser_id=$uid and iuser_genid='$genid' and status=1 and gspace = '$geo_office'";
    $result = mysqli_query($con, $sql) or die("Requests could not be fetched");
    while ($r = mysqli_fetch_assoc($result)) {
      $category = $r['request_category'];
      $option = $r['request_option'];
      $requests[$category][$option] = $r;
    }
    $requests['total'] = mysqli_num_rows($result);
    $_SESSION['fcoder_user_requests'] = $requests;
  }
  function userExist($con){
    $u_genid = $_SESSION['fcoder_genid'];
    $userid  = $_SESSION['fcoder_userid'];
    $sql = "SELECT * FROM fcoder_users where genid='$u_genid' and userid='$userid' and role!='Inactive'";
    $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed ");
    if(mysqli_num_rows($result) == 1) return true;
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
      $_SESSION['fcoder_gspace'] = $r['office'];
      $_SESSION['fcoder_department'] = $r['department'];
      $_SESSION['fcoder_designation'] = $r['designation'];
      $_SESSION['fcoder_designation_official'] = $r['designation_official'];
      $_SESSION['fcoder_ipphone'] = $r['ipphone'];
      $_SESSION['fcoder_contact_no'] = $r['contact_no'];
      $_SESSION['fcoder_email_id'] = $r['email_id'];
      $_SESSION['fcoder_upload_limit'] =$r['upload_limit'];
      $_SESSION['fcoder_upload_limit_bytes'] = 1048576 * $r['upload_limit'];
      $_SESSION['fcoder_total_uploads'] = $r['total_uploads'];
      $_SESSION['fcoder_total_recipients'] =  $r['total_recipients'];
      $_SESSION['fcoder_file_livetime'] = $r['file_livetime'];
      
     if($r['office']==$_SESSION['geo_office']){ 
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
    }
    else{
      $_SESSION['fcoder_wdrive_access'] =  0;
      $_SESSION['fcoder_wdrive_types'] = [];

      $_SESSION['fcoder_wstorage_limit'] = 0;
      $_SESSION['fcoder_wstorage_data_bytes'] =  0;
      $_SESSION['fcoder_wstorage_limit_bytes'] = 0;

      $_SESSION['fcoder_wshare_limit'] = 0;
      $_SESSION['fcoder_wshare_access'] = 0;
      $_SESSION['fcoder_wshare_data_bytes'] = 0;
      $_SESSION['fcoder_wshare_limit_bytes'] = 0;
    }
    
      $_SESSION['fcoder_ftransfer_access'] =$r['ftransfer_access'];
      if ($r['ftransfer_access'] == 1)
      $_SESSION['fcoder_ftransfer_types'] = [".csv", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/vnd.ms-excel", "application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.ms-powerpoint", "application/vnd.openxmlformats-officedocument.presentationml.presentation", "application/vnd.ms-xpsdocument"];
      else
      $_SESSION['fcoder_ftransfer_types'] = [];

      $_SESSION['fcoder_uassets_access'] = $r['uassets_access'];
      $_SESSION['fcoder_cschedule_access'] =$r['cschedule_access'];
      $_SESSION['fcoder_hadmin_access'] =  $r['hadmin_access'];
      $_SESSION['fcoder_mdesk_access'] = $r['mdesk_access'];
      $_SESSION['fcoder_imanage_access'] = $r['imanage_access'];
      $_SESSION['fcoder_afcoder_access'] =$r['afcoder_access'];
      $_SESSION['fcoder_avater_count'] = $r['avater_count'];
      $_SESSION['fcoder_userid'] = $r['userid'];
      $_SESSION['fcoder_role'] = $r['role'];
      if (isset($_SESSION['active_tab'])) {
        $update = false;
        if (($_SESSION['fcoder_imanage_access'] == 1 && ($_SESSION['active_tab'] == 'incidentModule' || $_SESSION['active_tab'] == 'manageModule')) ||
        ($_SESSION['fcoder_ftransfer_access'] == 1 && $_SESSION['active_tab'] == 'filetransferModule') ||
        ($_SESSION['fcoder_uassets_access'] == 1 && $_SESSION['active_tab'] == 'userassetsModule') ||
        ($_SESSION['fcoder_wdrive_access'] == 1 && $_SESSION['active_tab'] == 'webdriveModule') ||
        ($_SESSION['fcoder_mdesk_access'] == 1 && $_SESSION['active_tab'] == 'mydeskModule') ||
        ($_SESSION['fcoder_afcoder_access'] == 1 && $_SESSION['active_tab'] == 'assetmgmtModule') ||
        ($_SESSION['fcoder_hadmin_access'] == 1 && $_SESSION['active_tab'] == 'fcoderadminModule') ||
        ($_SESSION['fcoder_cschedule_access'] == 1 && $_SESSION['active_tab'] == 'conferenceModule')) $update = true;

        if ($update == false)
        $_SESSION['active_tab'] = 'homeModule';
      } else $_SESSION['active_tab'] = 'homeModule';
    }
    return $validUser;
  }
  function setWSParms($con){
    $u_id = $_SESSION['fcoder_uid'];
    $uassets_workstations = array();
    $uassets_status = array();
    $uassets_live_station = array();
    $uassets_lock_station = array();
    $uassets_usage_mode = array();
    $uassets_create_asset = $uassets_create_workstation = $uassets_dist_asset = $uassets_dist_user = $uassets_report_asset = 0;
    $geo_office = $_SESSION['geo_office'];
    if ($_SESSION['fcoder_role'] == 'Analyst')
    $sql = "SELECT id, status, create_asset, create_workstation, dist_asset, dist_user, report_asset, live_station, lock_station, usage_mode, iworkstation_id, net_faceplate_id,  gspace, department FROM fcoder_workstations where (iuser_id=$u_id or usage_mode=1) and gspace='$geo_office'";
    else
    $sql = "SELECT id, status, create_asset, create_workstation, dist_asset, dist_user, report_asset, live_station, lock_station, usage_mode, iworkstation_id, net_faceplate_id, gspace, department FROM fcoder_workstations where iuser_id=$u_id and gspace='$geo_office'";
    $result = mysqli_query($con, $sql) or die("Fetching workstation from DB is failed.");
    while ($r = mysqli_fetch_assoc($result)) {
      $wid = $r['id'];
      if ($r['live_station'] == 1 && $r['lock_station'] == 0) {
        if ($uassets_create_asset < $r['create_asset']) $uassets_create_asset = $wid;
        if ($uassets_create_workstation < $r['create_workstation']) $uassets_create_workstation = $wid;
        if ($uassets_dist_asset < $r['dist_asset']) $uassets_dist_asset = $wid;
        if ($uassets_dist_user < $r['dist_user']) $uassets_dist_user = $wid;
        if ($uassets_report_asset < $r['report_asset']) $uassets_report_asset = $wid;
      }
      $uassets_live_station[$wid] = $r['live_station'];
      $uassets_status[$wid] = $r['status'];
      $uassets_lock_station[$wid] = $r['lock_station'];
      $uassets_usage_mode[$wid] = $r['usage_mode'];
      $uassets_workstations[$wid]['name'] = $r['iworkstation_id'];
      $uassets_workstations[$wid]['office'] = $r['office'];
      $uassets_workstations[$wid]['department'] = $r['department'];
      $uassets_workstations[$wid]['faceplate'] = $r['net_faceplate_id'];
    }
    $_SESSION['uassets_create_asset'] = $uassets_create_asset;
    $_SESSION['uassets_create_workstation'] = $uassets_create_workstation;
    $_SESSION['uassets_dist_asset'] = $uassets_dist_asset;
    $_SESSION['uassets_dist_user'] = $uassets_dist_user;
    $_SESSION['uassets_report_asset'] = $uassets_report_asset;
    $_SESSION['uassets_status'] = $uassets_status;
    $_SESSION['uassets_live_station'] = $uassets_live_station;
    $_SESSION['uassets_lock_station'] = $uassets_lock_station;
    $_SESSION['uassets_usage_mode'] = $uassets_usage_mode;
    $_SESSION['uassets_workstations'] = $uassets_workstations;
  }
  function removeSession(){
    // backup login_key for total 5 times wrong attempt
    $loginattempt = $_SESSION['lattempt'];
    $login_key = $_SESSION['login_key'];
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
    // restore login_key before 6 times wrong attempt
    if($loginattempt< getenv('APP_LOGIN_FAIL')){
      $_SESSION['lattempt'] = $loginattempt + 1;
      $_SESSION['login_key']= $login_key;
    }
    else{
      session_unset();
      session_destroy();
      session_write_close();
      session_regenerate_id(true);
    }
  }
}
