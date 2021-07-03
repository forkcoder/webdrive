<?php
session_start();
require('../Servlets.php');
$session = new DBProxy();
if($session->validate($_GET['auth_ph'], $_GET['ph'])==false){
  header("Location: /index.php");
  die();
}
else{

  $con=$session->initDBConnection();
  
  $time= date("Y-m-d H:m:s");
  $u_userid=$_SESSION['fcoder_userid'];
  $u_name=$_SESSION['fcoder_name'];
  $u_genid=$_SESSION['fcoder_genid'];
  $sharedby = $_REQUEST['remarks'];
  $base=false;
  if($sharedby!=''){
    $sql="SELECT wds.id id FROM fcoder_webdrive_sharemap wdsm, fcoder_webdrive_share wds
    where wdsm.wdsm_share_id = wds.id and wdsm_iuser_id='$u_genid' and wdsm.wdsm_readonly=1 and wdsm.wdsm_status=1 and wds.wds_status=1 and wds.wds_base='$sharedby'";
    $result = mysqli_query($con, $sql) or die("Fetching Shares from DB is failed ");
    if(mysqli_num_rows($result)==1)
    $base = "../../web_drive/".$sharedby."/";
  }
  else
  $base = "../../web_drive/".$u_userid."/";
  if($base!=false){
    if(chdir($base)){
      $filenames = explode(',',$_REQUEST['filenames']);
      $pwd = $_REQUEST['pwd'];
      $files= array();
      $len = 0;
      $entries = array_diff(scandir($pwd), array('.', '..'));
      foreach($entries as $entry) {
        $file= $pwd.'/'.$entry;
        $key = array_search(fileinode($file),$filenames);
        if($key!==false){
          $filenames[$key]=$file;
          $len++;
        }
      }
      $filename = $filenames[0];
      $sql="INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_datetime, wdl_status,wdl_msg) values('download', '$u_genid', '$filename', '$time',1,200)";
      $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");
      if($len==1 && !is_dir($filename)){
        header('Content-Description: File Download');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filename));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        readfile(realpath($filename));
      }
      else if($len>=1){
        if($len > 1){
          if($pwd!='.')
          $filename = $pwd.'/'.basename($pwd);
          else
          $filename = 'My Drive';
        }
        $zip =new ZipArchive();
        $zip->open($filename.'.zip', ZipArchive::CREATE|ZipArchive::OVERWRITE);
        $zipfile =$filename.'.zip';
        for($i=0;$i<$len;$i++){
          $targetFile = $filename = $filenames[$i];
          if(is_dir($targetFile)==false)
          $zip->addFile($targetFile, basename($targetFile));
          else{
            $targetFile = realpath($targetFile);
            $files = new RecursiveIteratorIterator(
              new RecursiveDirectoryIterator($targetFile),
              RecursiveIteratorIterator::LEAVES_ONLY);
              if($len > 1)
              $targetFile = realpath($tmp);
              foreach($files as $name => $file){
                if(!$file->isDir()){
                  $filepath = $file->getRealPath();
                  $relativePath = substr($filepath, strlen($targetFile)+1);
                  $zip->addFile($filepath, $relativePath);
                }
              }
            }
            $sql="INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_datetime, wdl_status, wdl_msg) values('download', '$u_genid', '$filename', '$time',1,200)";
            $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");
          }
          $zip->close();
          header('Content-Description: File Download');
          header('Content-Type: application/octet-stream');
          header('Content-Disposition: attachment; filename="'.basename($zipfile));
          header('Expires: 0');
          header('Cache-Control: must-revalidate');
          header('Pragma: public');
          header('Content-Length: ' . filesize($zipfile));
          readfile($zipfile);
          unlink($zipfile);
        }
      }
    }
    $session->closeDBConnection($con);
  }
  ?>
