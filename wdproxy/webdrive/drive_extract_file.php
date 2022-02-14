<?php
session_start();
require('../WDProxy.php');
$data = array();
$data['opts']['status']=false;
$session = new WDProxy();
if ($session->remote_validate($_POST['access_key'])){
  $con = $session->initDBConnection();
  $time = date("Y-m-d H:m:s");
  $pwd = $_POST['pwd'];
  $wdl_src = $_POST['filename'];
  $userid = $_SESSION['fcoder_userid'];
  $u_name = $_SESSION['fcoder_name'];
  $u_genid = $_SESSION['fcoder_genid'];
  $optstatus = false;
  $base = "../../web_drive/" . $userid . "/";
  if (chdir($base)) {
    $filename = $wdl_src;
    if ($_SESSION['fcoder_wstorage_data_bytes'] + filesize($filename) < $_SESSION['fcoder_wstorage_limit_bytes']) {
      $ext = pathinfo($filename, PATHINFO_EXTENSION);
      $fname = realpath($pwd) . '/' . pathinfo($filename)['filename'];
      $count = 1;
      $temp = $fname;
      while (file_exists($fname)) {
        $fname = $temp . '(' . $count . ')';
        $count = $count + 1;
      }

      if ($ext == 'zip') {
        $zip = new ZipArchive();
        if ($zip->open(realpath($filename)) == true) {
          $zip->extractTo($fname);
          $zip->close();
          $optstatus = true;
        }
      } else if ($ext == 'rar') {
        //Add rar.so extenstion by installing pecl -v install rar
        $zip = RarArchive::open(realpath($filename));
        $entries = $zip->getEntries();
        foreach ($entries as $entry)
          $entry->extract($fname);
        $zip->close();
        $optstatus = true;
      }



      if ($optstatus == true) {
        $files = new RecursiveIteratorIterator(
          new RecursiveDirectoryIterator($fname, RecursiveDirectoryIterator::SKIP_DOTS),
          RecursiveIteratorIterator::CHILD_FIRST
        );
        $totalSize = 0;
        foreach ($files as $file) {
          $totalSize += $file->getSize();
        }
        $wdrive_projected_size = $_SESSION['fcoder_wstorage_data_bytes'] + $totalSize;
        if ($_SESSION['fcoder_wstorage_limit_bytes'] >  $wdrive_projected_size) {
          $_SESSION['fcoder_wstorage_data_bytes'] = $wdrive_projected_size;

          $fwdl_src = str_replace("'", "''", $wdl_src);
          $fpwd = str_replace("'", "''", $pwd);
          $sql = "INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg)
          values('extract', '$u_genid', '$fwdl_src', '$fpwd', '$time',1,200)";
          $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");
          $sql="UPDATE fcoder_users set wstorage_data_bytes=$wdrive_projected_size where genid='$u_genid' and userid='$userid' and wdrive_access=1";
          $result = mysqli_query($con, $sql) or die("Updating data size info to DB is failed");
          $mesg = 'Extraction operation has been completed successfully.';
        } else {
          foreach ($files as $fileinfo) {
            $func = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $func($fileinfo->getRealPath());
          }
          rmdir($fname);
          $mesg = 'You don\'t have sufficient space. Remove unnecessay documents and try again.';
        }
      } else
        $mesg = 'Failed to extract (' . basename($fname) . '). Contact with site Administrator..';



      //delete insecure file
      // $acceptedTypes = [".csv","application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application/vnd.ms-excel","application/pdf","application/msword","application/vnd.openxmlformats-officedocument.wordprocessingml.document","application/vnd.ms-powerpoint","application/vnd.openxmlformats-officedocument.presentationml.presentation","application/vnd.ms-xpsdocument","application/zip", "application/x-zip", "application/x-zip-compressed", "image/png", "image/jpeg", "image/gif"];
      // $files = new RecursiveIteratorIterator(
      //   new RecursiveDirectoryIterator($fname),
      //   RecursiveIteratorIterator::LEAVES_ONLY);
      //   $finfo = new finfo(FILEINFO_MIME_TYPE);
      //   foreach($files as $name => $file){
      //     if(!$file->isDir()){
      //       $type  = $finfo->file($file->getRealPath());
      //       if(!in_array($type, $acceptedTypes)) {
      //         unlink($file->getRealPath());
      //       }
      //     }
      //   }


    } else
      $mesg = 'You don\'t have sufficient space. Remove unnecessay documents and try again.';
  }
  if ($optstatus == false) {
    $fwdl_src = str_replace("'", "''", $wdl_src);
    $fpwd = str_replace("'", "''", $pwd);
    $sql = "INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg)
    values('extract', '$u_genid', '$fwdl_src', '$fpwd', '$time',0,500)";
    $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed".$sql);
  }
  $data['opts']['status'] = $optstatus;
  $data['opts']['msg'] = $mesg;
  $session->closeDBConnection($con);
}
echo json_encode($data);
