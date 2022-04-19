<?php
session_start();
require('../WDProxy.php');
$data = array();
$data['opts']['status'] = false;
$session = new WDProxy();
if ($session->remote_validate($_POST['access_key'])) {
  $con = $session->initDBConnection();
  date_default_timezone_set("Asia/Dhaka");
  $time = date("Y-m-d H:m:s");
  $userid = $_SESSION['bbank_userid'];
  $u_name = $_SESSION['bbank_name'];
  $u_genid = $_SESSION['bbank_genid'];
  $sharedby = $_POST['remarks'];
  $mesg = '';
  $base = false;
  $sql = "SELECT wds.id id FROM bbank_webdrive_sharemap wdsm, bbank_webdrive_share wds
    where wdsm.wdsm_share_id = wds.id and wdsm_iuser_id='$u_genid' and wdsm.wdsm_readonly=1 and wdsm.wdsm_status=1 and wds.wds_status=1 and wds.wds_base='$sharedby'";
  $result = mysqli_query($con, $sql) or die("Fetching Shares from DB is failed ");
  if (mysqli_num_rows($result) > 0)
    $base = "../../web_drive/" . $sharedby . "/";
  $dest = "../../web_drive/" . $userid . "/" . $_POST['dest'];

  $optstatus = false;
  $fileinodes = explode(',', $_POST['filenames']);
  $filenames =  explode(',', $_POST['pwd']);
  if (in_array(fileinode($dest), $fileinodes) != true) {
    if ($base != false) {
      if (chdir($base)) {
        $cfs = 0;
        $len = 0;
        foreach ($filenames as $file) {
          $key = array_search(fileinode($file), $fileinodes);
          if ($key !== false) {
            $len++;
            if (is_dir($file) == true)
              $cfs = $cfs + dirSize($file);
            else
              $cfs = $cfs + filesize($file);
          }
        }
        $wds = $_SESSION['bbank_wstorage_data_bytes'];
        $msq = $_SESSION['bbank_wstorage_limit_bytes'];

        if (((($wds + $cfs) < $msq))) {
          $accessFailed = array();
          $copyFailed = array();
          $optstatus = true;
          for ($i = 0; $i < $len; $i++) {
            $src = $filenames[$i];
            $ext = '';
            $name = pathinfo($src, PATHINFO_FILENAME);
            if (is_dir($src) == false)
              $ext = '.' . pathinfo($src, PATHINFO_EXTENSION);
            $count = 1;
            $temp = $name;
            while (file_exists($dest . '/' . $name . $ext)) {
              $name = $temp . '(' . $count . ')';
              $count = $count + 1;
            }
            if (is_dir($src) == true)
              recurse_copy($src, $dest . '/' . $name);
            else
              copy($src, $dest . '/' . $name . $ext);
            $fsrc = str_replace("'", "''", $src);
            $fdest = str_replace("'", "''", $dest);
            $sql = "INSERT into bbank_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg)
              values('copy', '$u_genid', '$fsrc', '$fdest', '$time',1,200)";
            $result = mysqli_query($con, $sql) or die("Adding bbank_webdrive_log to DB is failed");
          }
          $_SESSION['bbank_wstorage_data_bytes'] = $wds + $cfs;
          $mesg = 'Copy operation has been completed successfully.';
        } else
          $mesg = 'You don\'t have sufficient space. Remove unnecessay documents and try again.';
      }
    } else
      $mesg = 'You are not authorized to download or file(s) has been deleted by Owner. Refresh Web Drive.';
  } else
    $mesg = 'Please Select proper destination to Copy/Move file(s).';

  if ($optstatus == false) {
    $pwd = $_POST['pwd'];
    $sql = "INSERT into bbank_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg) values('copy', '$u_genid', '$pwd', '$dest', '$time',1,500)";
    $result = mysqli_query($con, $sql) or die("Adding bbank_webdrive_log to DB is failed");
  }
  $data['opts']['msg'] = $mesg;
  $data['opts']['status'] = $optstatus;
  $session->closeDBConnection($con);
}
echo json_encode($data);

function dirSize($path)
{
  $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
  $totalSize = 0;
  foreach ($iterator as $file) {
    $totalSize += $file->getSize();
  }
  return $totalSize;
}

function recurse_copy($src, $dst)
{
  $dir = opendir($src);
  @mkdir($dst);
  while (false !== ($file = readdir($dir))) {
    if (($file != '.') && ($file != '..')) {
      if (is_dir($src . '/' . $file)) {
        recurse_copy($src . '/' . $file, $dst . '/' . $file);
      } else {
        copy($src . '/' . $file, $dst . '/' . $file);
      }
    }
  }
  closedir($dir);
}
