<?php
session_start();
$data = array();
require('../Servlets.php');
$session = new DBProxy();
if ($session->validate($_GET['auth_ph'], $_GET['ph']) == false) {
  header("Location: /index.php");
  die();
} else {
  date_default_timezone_set("Asia/Dhaka");
  $time = date("Y-m-d H:m:s");
  $con = $session->initDBConnection();
  $u_userid = $_SESSION['fcoder_userid'];
  $u_name = $_SESSION['fcoder_name'];
  $u_genid = $_SESSION['fcoder_genid'];
  $u_gspace = $_SESSION['fcoder_gspace'];
  $optstatus = false;
  $data['opts']['msg'] = 'You don\'t have permission to access to File Transfer';
  if ( $_SESSION['fcoder_ftransfer_access'] == 1) {
    $selectedUsers  = explode(',', $_REQUEST['selectedUsers']);
    $base = "../../web_drive/" . $u_userid . "/";
    $filenames = explode(',', $_REQUEST['filenames']);
    if ($_SESSION['fcoder_total_uploads'] < count($filenames))
      $data['opts']['msg'] = 'You can send <span style="font-weight:bold">' . $_SESSION['fcoder_total_uploads'] . ' files</span> at once.';
    else if ($_SESSION['fcoder_total_recipients'] < count($selectedUsers))
      $data['opts']['msg'] = 'You can send file(s) to  maximum  <span style="font-weight:bold">' . $_SESSION['fcoder_total_recipients'] . ' users</span> at once.';
    else {

      if (chdir($base)) {
        $len = count($filenames);
        $pwd = $_REQUEST['pwd'];
        if ($len > 0) {
          $location = '../../file_transfer/';
          $sendStatus = array();
          $sendStatus['invalidfile'] = array();

          for ($i = 0; $i < $len; $i++) {
            $path = $filenames[$i];
            $realpath = realpath($path);
            $file_name = basename($realpath);
            $ctime = date("Y-m-d h:m:s", filectime($realpath));
            $random_sid = $u_genid . '-' . $i . time();

            $file_size = 0;
            $iterator = array();
            if (is_dir($realpath)) {
              $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($realpath),
                RecursiveIteratorIterator::LEAVES_ONLY
              );
              foreach ($iterator as $file) {
                $file_size += $file->getSize();
              }
            } else $file_size = filesize($realpath);

            $inode = fileinode($path);

            $sendStatus[$inode] = array();
            $sendStatus[$inode] = array();

            if ($_SESSION['fcoder_upload_limit_bytes'] >= $file_size) {


              $finfo = new finfo(FILEINFO_MIME_TYPE);
              $type  = $finfo->file($realpath);
              if (in_array($type, $_SESSION['fcoder_ftransfer_types']) || is_dir($realpath) ) {
                if (is_dir($realpath) == true) {
                  $filename = $location . '/' . $random_sid;
                  $zipfile = $filename;
                  $zip = new ZipArchive();
                  $zip->open($filename, ZipArchive::CREATE);
                  foreach ($iterator as $key=> $file) {
                    if (!$file->isDir()) {
                      $filepath = $file->getRealPath();
                      $relativePath = substr($filepath, strlen($realpath) + 1);
                      $zip->addFile($filepath, $relativePath);
                    }
                  }
                  $zip->close();
                  $file_name .= ".zip";
                } else
                  copy($realpath, $location . '/' . $random_sid);
                $hostname = $_SESSION['clientInfo']['hostname'];
                $ipaddress = $_SESSION['clientInfo']['ipaddress'];

                $sendStatus[$inode]['succeeduser'] = array();
                $sendStatus[$inode]['faileduser'] = array();
                foreach ($selectedUsers as $sendto) {
                  // $sendStatus[$inode][$sendto]=array();
                  $sql = "SELECT id from fcoder_users where genid='$sendto' and gspace='$u_gspace'";
                  $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed ");
                  if (mysqli_num_rows($result) == 1) {
                    $sqls = "INSERT INTO fcoder_file_transfer (f_sender_genid, f_sender_name,f_receiver_genid, f_random_sid, f_send_date_time, f_status, f_send_comments, f_send_hostname, f_send_ipaddress,f_file_name,f_file_size) 
                       VALUES('$u_genid','$u_name','$sendto','$random_sid','$time','Pending','File from Web Drive','$hostname','$ipaddress','$file_name','$file_size');";
                    $result = mysqli_query($con, $sqls) or die("File Transfer info could not be inserted");

                    $sql = "INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg) values('send', '$u_genid', '$realpath', '$location', '$time',1,200)";
                    $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");

                    $sendStatus[$inode]['succeeduser'][] = $sendto;
                  } else {
                    $sendStatus[$inode]['faileduser'][] = $sendto;
                  }
                }
              } else {
                $sendStatus['invalidfile'][] = $inode;
                $sendStatus[$inode]['msg'] = 'Unsupported Type.';
              }
            } else {
              $sendStatus['invalidfile'][] = $inode;
              $sendStatus[$inode]['msg'] = 'Exceeds the limit: <span style="font-weight:bold">' . $_SESSION['fcoder_upload_limit'] . 'MB</span>.';
            }
          }
          $data['sendStatus'] = $sendStatus;
          $data['opts']['msg'] = 'File Transfer has been completed Successfully.';
          $optstatus = true;
        }
      } 
    }
  }
    
  $data['opts']['status'] = $optstatus;

  echo json_encode($data);
  $session->closeDBConnection($con);
}
