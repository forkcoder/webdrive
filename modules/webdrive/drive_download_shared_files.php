<?php
session_start();
require('../Servlets.php');
$session = new DBProxy();
if ($session->validate($_POST['auth_ph'], $_POST['ph'])) {
  // Read a file and display its content chunk by chunk
  function downloadFile($file)
  {
    // error_reporting(0);
    // ob_start();
    header('Content-Type: application/octet-stream');
    header("Pragma: public");
    header("Cache-Control: must-revalidate");
    header('Content-Description: File Download');
    header('Content-Disposition: attachment; filename="' . basename($file));
    header('Expires: 0');
    header('X-Accel-Buffering: no');
    header('Content-Length: ' . filesize($file));
    readfile(realpath($file));
    // ob_clean();
    // ob_end_flush();
  }
  $con = $session->initDBConnection();

  $time = date("Y-m-d H:m:s");
  $userid = $_SESSION['fcoder_userid'];
  $u_name = $_SESSION['fcoder_name'];
  $u_genid = $_SESSION['fcoder_genid'];
  $sharedby = $_POST['remarks'];
  $base = "../../web_drive/" . $sharedby . "/";
  if ($base != false) {
    if (chdir($base)) {
      $fileinodes = explode(',', $_POST['filenames']);
      $filenames =  explode(',', $_POST['pwd']);
      $len = count($filenames);
      $filename = $filenames[0];
      if ($len == 1 && !is_dir($filename) && array_search(fileinode($filename), $fileinodes) !== false) {
        // $sql = "SELECT wds.id id FROM fcoder_webdrive_sharemap wdsm, fcoder_webdrive_share wds where wdsm.wdsm_share_id = wds.id and wdsm_iuser_id='$u_genid' and wdsm.wdsm_readonly=1 and wdsm.wdsm_status=1 and wds.wds_status=1 and wds.wds_base='$sharedby' and wds.wds_path='$filename'";
        // $result = mysqli_query($con, $sql) or die("Fetching Shares from DB is failed ");
        // if (mysqli_num_rows($result) == 1) {
          $fname = str_replace("'", "''", $filename);
          $sql = "INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_datetime, wdl_status,wdl_msg)
        values('download', '$u_genid', '$fname', '$time',1,200)";
          $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");
          $session->closeDBConnection($con);
          downloadFile($filename);
        // }
        exit;
      } else if ($len >= 1) {
        $zipfile = '';
        if ($len > 1) {
          if ($pwd != '.')
            $zipfile = basename($pwd) . '.zip';
          else
            $zipfile = 'My Drive.zip';
        } else
          $zipfile = $filename . '.zip';
        $zip = new ZipArchive();
        $zip->open($zipfile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        for ($i = 0; $i < $len; $i++) {
          $targetFile = $filename = $filenames[$i];
          $sql = "SELECT wds.id id FROM fcoder_webdrive_sharemap wdsm, fcoder_webdrive_share wds where wdsm.wdsm_share_id = wds.id and wdsm_iuser_id='$u_genid' and wdsm.wdsm_readonly=1 and wdsm.wdsm_status=1 and wds.wds_status=1 and wds.wds_base='$sharedby' and wds.wds_path='$filename'";
          $result = mysqli_query($con, $sql) or die("Fetching Shares from DB is failed ");
          // if (mysqli_num_rows($result) == 1 && array_search(fileinode($filename), $fileinodes)!==false) {
            if (is_dir($targetFile) == false)
              $zip->addFile($targetFile, basename($targetFile));
            else {
              $targetFile = realpath($targetFile);
              $files = array();
              $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($targetFile),
                RecursiveIteratorIterator::LEAVES_ONLY
              );
              if ($len > 1)
                $targetFile = realpath($tmp);
              foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                  $filepath = $file->getRealPath();
                  $relativePath = substr($filepath, strlen($targetFile) + 1);
                  $zip->addFile($filepath, $relativePath);
                }
              }
            }
            $fname = str_replace("'", "''", $filename);
            $sql = "INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_datetime, wdl_status, wdl_msg)
            values('download', '$u_genid', '$fname', '$time',1,200)";
            $result = mysqli_query($con, $sql) or die("Adding zip info fcoder_webdrive_log to DB is failed");
          // }
        }
        $session->closeDBConnection($con);
        $zip->close();
        downloadFile($zipfile);
        unlink($zipfile);
        exit;
      }
    }
  }
}
