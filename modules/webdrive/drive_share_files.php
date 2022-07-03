<?php
session_start();
$time = date("Y-m-d H:m:s");
$data = array();
$data['opts']['status'] = false;
require('../Servlets.php');
$session = new DBProxy();
if ($session->validate($_POST['auth_ph'], $_POST['ph'])) {
  $con = $session->initDBConnection();
  $userid = $_SESSION['fcoder_userid'];
  $u_name = $_SESSION['fcoder_name'];
  $u_genid = $_SESSION['fcoder_genid'];
  $u_gspace = $_SESSION['fcoder_gspace'];
  $sharewith  = $_POST['genid'];
  $shareFailed = array();
  $shareExist = array();
  $shareSucceed = array();
  $base = "../../web_drive/" . $userid . "/";
  $optstatus = false;
  $filenames = explode(',', $_POST['filenames']);
  $mysharesize = $_SESSION['fcoder_wshare_data_bytes'];
  $sql = "SELECT id from fcoder_users where genid='$sharewith' and userid!=''";
  $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed.");
  if (mysqli_num_rows($result) == 1) {
    $optstatus = true;
    if (chdir($base)) {
      $len = count($filenames);
      $pwd = $_POST['pwd'];
      if ($len > 0) {
        $totalSharedSize = 0;
        for ($i = 0; $i < $len; $i++) {
          $path = $filenames[$i];
          $realpath = realpath($path);
          $name = basename($realpath);
          $ctime = date("Y-m-d h:m:s", filectime($realpath));
          $size = 0;
          if (is_dir($realpath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($realpath));
            foreach ($iterator as $file) {
              $size += $file->getSize();
            }
          } else $size = filesize($realpath);
          $inode = fileinode($path);

          $fpath = str_replace("'", "''", $path);
          $sql = "SELECT id, wds_count FROM fcoder_webdrive_share where wds_name='$name' and wds_created_at='$ctime' and wds_owner='$u_genid' and wds_status=1 and wds_path='$fpath'";
          $result = mysqli_query($con, $sql) or die("Fetching shareinfo from DB is failed.");
          if (mysqli_num_rows($result) == 1) {
            //share of existing shared file
            $row = mysqli_fetch_assoc($result);
            $sharecount = $row['wds_count'];
            $id = $row['id'];
            $sql = "SELECT id  from fcoder_webdrive_sharemap where wdsm_share_id=$id and wdsm_iuser_id='$sharewith' and wdsm_status=1";
            $result = mysqli_query($con, $sql) or die("Fetching sharemap from DB is failed ");
            $row = mysqli_fetch_assoc($result);

            if (mysqli_num_rows($result) == 0) {
              $sharecount = $sharecount + 1;
              $sql = "UPDATE fcoder_webdrive_share set wds_count=$sharecount where id=$id";
              $result = mysqli_query($con, $sql) or die("Updating shareinfo to DB is failed.");
              $sql = "INSERT into fcoder_webdrive_sharemap (wdsm_share_id, wdsm_iuser_id, wdsm_shared_at, wdsm_readonly, wdsm_status)
              values('$id', '$sharewith', '$time', 1, 1)";
              $result = mysqli_query($con, $sql) or die("Adding sharemap to DB is failed.");
              $shareSucceed[] = $inode;
              $sql = "INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_share_id, wdl_datetime, wdl_status,wdl_msg)
              values('share', '$u_genid', '$fpath', '$sharewith', $id, '$time',1,200)";
              $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed.");
              $data['opts']['msg'] = 'File(s) have been shared successfully.';
            } else {
              $sql = "INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src,wdl_dest, wdl_share_id, wdl_datetime, wdl_status,wdl_msg)
              values('share', '$u_genid', '$fpath', '$sharewith', $id, '$time',1,500)";
              $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed.");
              $shareExist[] = $inode;
              $data['opts']['msg'] = 'File(s) have been shared already.';
            }
          } else {
            $remote_depo =(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            if ($_SESSION['fcoder_wshare_limit_bytes'] > ($_SESSION['fcoder_wshare_data_bytes'] + $size)) {
              //share new files
              $sharecount = 1;
              $sql = "INSERT into fcoder_webdrive_share (wds_name, wds_created_at, wds_owner, wds_base, wds_title, wds_path, wds_status, wds_count,wds_size, wds_remote_depo)
              values('$name', '$ctime', '$u_genid', '$userid','$u_name', '$fpath', 1, $sharecount,$size, '$remote_depo')";
              $result = mysqli_query($con, $sql) or die("Adding shareinfo to DB is failed");
              $id = mysqli_insert_id($con);
              $sql = "INSERT into fcoder_webdrive_sharemap (wdsm_share_id, wdsm_iuser_id, wdsm_shared_at, wdsm_readonly, wdsm_status)
              values($id, '$sharewith', '$time', 1, 1)";
              $result = mysqli_query($con, $sql) or die("Adding sharemap to DB is failed");
              $shareSucceed[] = $inode;
              $totalSharedSize = $totalSharedSize + $size;
              $sql = "INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src,wdl_dest, wdl_share_id, wdl_datetime, wdl_status,wdl_msg)
              values('share', '$u_genid', '$fpath', '$sharewith', $id, '$time',1,200)";
              $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed.");
              $data['opts']['msg'] = 'File(s) have been shared successfully.';
            } else {
              $optstatus = false;
              $sql = "INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg)
              values('share', '$u_genid', '$fpath', '$sharewith', '$time',1,500)";
              $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed.");
              $shareFailed[] = $inode;
              $data['opts']['msg'] = 'File(s) could not be shared (MAX Share Limit: ' . $_SESSION['fcoder_wshare_limit'] . 'MB). Remove unnecessay shared files and try again.';
            }
          }
        }
        $mysharesize += $totalSharedSize;
      }
    }
  } else {
    $data['opts']['msg'][] = 'Selected User (genid: ' . $sharewith . ') is not belongs to BB ' . $u_gspace;
  }
  $data['sharefailed'] = $shareFailed;
  $data['shareexist'] = $shareExist;
  $data['sharesucceed'] = $shareSucceed;
  $data['mysharesize'] = $_SESSION['fcoder_wshare_data_bytes'] = $mysharesize;
  $sql="UPDATE fcoder_users set wshare_data_bytes=$mysharesize where genid='$u_genid' and userid='$userid' and wdrive_access=1";
  $result = mysqli_query($con, $sql) or die("Updating data size info to DB is failed".$sql);
  $data['opts']['status'] = $optstatus;
  $session->closeDBConnection($con);
}
echo json_encode($data);
