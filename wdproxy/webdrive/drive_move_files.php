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
  $userid = $_SESSION['fcoder_userid'];
  $u_name = $_SESSION['fcoder_name'];
  $u_genid = $_SESSION['fcoder_genid'];
  $mesg = '';
  $base = '';

  $operation  = $_POST['operation'];
  $base = "../../web_drive/" . $userid . "/";
  $dest = "../../web_drive/" . $userid . "/" . $_POST['dest'];

  $optstatus = false;
  $fileinodes = explode(',', $_POST['filenames']);
  $pwd = $_POST['pwd'];
  if (in_array(fileinode($dest), $fileinodes) != true) {
    if ($base != false) {
      if (chdir($base)) {
        $cfs = 0;
        $len = 0;
        $filenames =  array();
        $entries = array_diff(scandir($pwd), array('.', '..'));
        foreach ($entries as $entry) {
          $file = $pwd . '/' . $entry;
          $key = array_search(fileinode($file), $fileinodes);
          if ($key !== false) {
            $filenames[$len] = $file;
            $len++;
            if (is_dir($file) == true)
              $cfs = $cfs + dirSize($file);
            else
              $cfs = $cfs + filesize($file);
          }
        }
        $wds = $_SESSION['fcoder_wstorage_data_bytes'];
        $msq = $_SESSION['fcoder_wstorage_limit_bytes'];
        $wdrive_projected_size = $wds + $cfs;
        if (((($wdrive_projected_size) < $msq) && $operation == 'copy') || (($wds < $msq) && $operation == 'move')) {
          $accessFailed = array();
          $copyFailed = array();
          if ($operation == 'copy') {
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
              $sql = "INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg)
              values('copy', '$u_genid', '$fsrc', '$fdest', '$time',1,200)";
              $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");
            }
            $_SESSION['fcoder_wstorage_data_bytes'] = $wdrive_projected_size;
            $sql="UPDATE fcoder_users set wstorage_data_bytes=$wdrive_projected_size where genid='$u_genid' and userid='$userid' and wdrive_access=1";
            $result = mysqli_query($con, $sql) or die("Updating data size info to DB is failed");
            $mesg = 'Copy operation has been completed successfully.';
          } else {
            $optstatus = true;
            for ($i = 0; $i < $len; $i++) {
              $src = $filenames[$i];
              if ($dest != pathinfo($src, PATHINFO_DIRNAME)) {
                $name = basename($src);
                if (is_dir($src) == true)
                  recurse_move($src, $dest . '/' . $name);
                else
                  rename($src, $dest . '/' . $name);
              }
              $fsrc = str_replace("'", "''", $src);
              $fdest = str_replace("'", "''", $dest);
              $sql = "INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg)
              values('move', '$u_genid', '$fsrc', '$fdest', '$time',1,200)";
              $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");
            }
            $mesg = 'Move operation has been completed successfully.';
          }
        } else
          $mesg = 'You don\'t have sufficient space. Remove unnecessay documents and try again.';
      }
    } else
      $mesg = 'You are not authorized to download or file(s) has been deleted by Owner. Refresh Web Drive.';
  } else
    $mesg = 'Please Select proper destination to Copy/Move file(s).';

  if ($optstatus == false) {
    $sql = "INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg) values('$operation', '$u_genid', '$pwd', '$dest', '$time',1,500)";
    $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");
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
// Function to Copy folders and files
function recurse_move($src, $dst)
{
  if (is_dir($src)) {
    @mkdir($dst);
    $files = scandir($src);
    foreach ($files as $file)
      if ($file != "." && $file != "..")
        recurse_move("$src/$file", "$dst/$file");
    rmdir($src);
  } else if (file_exists($src)) {
    if (file_exists($dst) == false)
      rename($src, $dst);
  }
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
