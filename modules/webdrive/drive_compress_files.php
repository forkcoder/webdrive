<?php
session_start();
$data = array();
require('../Servlets.php');
$session = new DBProxy();
if($session->validate($_GET['auth_ph'], $_GET['ph'])==false){
  header("Location: /index.php");
  die();
}
else{
  
  $con=$session->initDBConnection();
  date_default_timezone_set("Asia/Dhaka");
  $time= date("Y-m-d H:m:s");
  $u_userid=$_SESSION['fcoder_userid'];
  $u_name=$_SESSION['fcoder_name'];
  $u_genid=$_SESSION['fcoder_genid'];
  $base = "../../web_drive/".$u_userid."/";
  if(chdir($base)){
    $filenames = explode(',',$_REQUEST['filenames']);
    $pwd = $_REQUEST['pwd'];
    $optstatus=false;
    $wdl_src='';
    if($_SESSION['fcoder_wstorage_data_bytes']<($_SESSION['fcoder_wstorage_limit_bytes'])){
      $compressFailed=array();
      $extractFailed=array();
      $accessFailed = array();
      $files= array();
      $len = 0;
      $entries = array_diff(scandir($pwd), array('.', '..'));
      foreach($entries as $entry) {
        $file= $pwd.'/'.$entry;
        if(in_array(fileinode($file),$filenames)!==false)
          $filenames[$len++]=$file;
      }
      if($len>0){
        $dirisempty = false;
        if($len==1&&is_dir($filenames[0]) && count(scandir($filenames[0]))==2) $dirisempty= true;

    
        if($dirisempty== false){
          

          if($len == 1) $filename = $filenames[0];
          else if($pwd!='.')
          $filename = basename($pwd);
          else $filename = 'My Drive';

          $count =1;
          $temp = $filename;
          $filename=$filename.'.zip';
          while(file_exists($filename)){
            $filename= $temp.'('.$count.').zip';
            $count = $count+1;
          }
          $zipfile = $filename;
          $zip =new ZipArchive();
          $zip->open($filename, ZipArchive::CREATE);
          for($i=0;$i<$len;$i++){
            $targetFile =$filenames[$i];
            if(is_dir($targetFile)==false)
            $zip->addFile($targetFile, basename($targetFile));
            else{
              $targetFile = realpath($targetFile);
              $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($targetFile),
                RecursiveIteratorIterator::LEAVES_ONLY);
                if($len > 1)
                $targetFile = realpath($pwd);
                foreach($files as $name => $file){
                  if(!$file->isDir()){
                    $filepath = $file->getRealPath();
                    $relativePath = substr($filepath, strlen($targetFile)+1);
                    $zip->addFile($filepath, $relativePath);
                  }
                }
              }
            }
            $zip->close();
            $wdrive_projected_size=$_SESSION['fcoder_wstorage_data_bytes'] + filesize($zipfile);
            $wdl_id = fileinode($zipfile);
            $wdl_src = $pwd.'/'.basename($zipfile);
            if($_SESSION['fcoder_wstorage_limit_bytes'] >  $wdrive_projected_size){
              $_SESSION['fcoder_wstorage_data_bytes'] = $wdrive_projected_size;
              $optstatus= true;
              $mesg='Compress has been completed successfully.';
              $sql="INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status, wdl_msg) values('compress', '$u_genid', '$wdl_src', '$pwd', '$time',1,200)";
              $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");
              $sql="UPDATE fcoder_users set wstorage_data_bytes=$wdrive_projected_size where genid='$u_genid' and userid='$u_userid' and wdrive_access=1";
              $result = mysqli_query($con, $sql) or die("Adding data size info to DB is failed".$sql);
            }
            else{
              unlink($zipfile);
              $mesg='You don\'t have sufficient space.  Remove unnecessay documents and try again.';
            }
          }
          else
          $mesg='Empty folder could not be compressed. Try Files or directory which is not empty.';
        }
        else
        $mesg='You have not selected any files or directory to compress. Try again.';
      }
      else
      $mesg='Your drive is full. Remove unnecessay documents and try again.';
    }
    if($optstatus==false){
      $sql="INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg) values('compress', '$u_genid', '$wdl_src', '$pwd', '$time',0,500)";
      $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");
    }
    $data['opts']['status']=$optstatus;
    $data['opts']['msg']= $mesg;
    echo json_encode($data);
    $session->closeDBConnection($con);
  }
  ?>
