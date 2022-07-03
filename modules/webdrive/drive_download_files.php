<?php
session_start();
require('../Servlets.php');
$session = new DBProxy();
if ($session->validate($_POST['auth_ph'], $_POST['ph'])) {
  // Read a file and display its content chunk by chunk
  function downloadFile($file){
    $chunksize = 5 * (1024 * 1024);
    $filename = realpath($file);
    $filesize = intval(sprintf("%u", filesize($filename)));
    error_reporting(0);
    ob_start();
    header('Content-Type: application/octet-stream');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . $filesize);
    header('Content-Disposition: attachment; filename="' . basename($file));

    // header("Pragma: public");
    // header("Cache-Control: must-revalidate");
    // header('Content-Description: File Download');
    // header('Expires: 0');
    // header('X-Accel-Buffering: no');
    if($filesize > $chunksize)
    { 
        $handle = fopen($filename, 'rb'); 
        while (!feof($handle))
        { 
          print(@fread($handle, $chunksize));
          ob_flush();
          flush();
        }
        fclose($handle); 
    }
    else readfile($filename);
  }
  $con = $session->initDBConnection();

  $time = date("Y-m-d H:m:s");
  $userid = $_SESSION['fcoder_userid'];
  $u_name = $_SESSION['fcoder_name'];
  $u_genid = $_SESSION['fcoder_genid'];
  $base = false;
  $base = "../../web_drive/" . $userid . "/";

  if ($base != false) {
    if (chdir($base)) {
      $filenames = explode(',', $_POST['filenames']);
      $pwd = $_POST['pwd'];
      $files = array();
      $len = 0;
      $entries = array_diff(scandir($pwd), array('.', '..'));
      foreach ($entries as $entry) {
        $file = $pwd . '/' . $entry;
        $key = array_search(fileinode($file), $filenames);
        if ($key !== false) {
          $filenames[$key] = $file;
          $len++;
        }
      }
      $filename = $filenames[0];
      if ($len == 1 && !is_dir($filename)) {
        $fname = str_replace("'", "''", $filename);
        $sql = "INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_datetime, wdl_status,wdl_msg)
        values('download', '$u_genid', '$fname', '$time',1,200)";
        $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");
        $session->closeDBConnection($con);
        downloadFile($filename);
        exit;
      } else if ($len >= 1) {
        $zipfile = '';
        if ($len > 1) {
          if ($pwd != '.')
            $zipfile = basename($pwd) . '.zip';
          else
            $zipfile = 'My Drive.zip';
        } else {
          $zipfile = $filename . '.zip';
        }
        $zip = new ZipArchive();
        $zip->open($zipfile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        for ($i = 0; $i < $len; $i++) {
          $targetFile = $filename = $filenames[$i];
          if (is_dir($targetFile) == false)
            $zip->addFile($targetFile, basename($targetFile));
          else {
            $targetFile = realpath($targetFile);
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
