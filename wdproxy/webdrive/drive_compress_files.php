<?php
session_start();
$data = array();
$data['opts']['status']=false;
require('../WDProxy.php');
$session = new WDProxy();
if ($session->remote_validate($_POST['access_key'])){
  $con=$session->initDBConnection();
  date_default_timezone_set("Asia/Dhaka");
  $time= date("Y-m-d H:m:s");
  $userid=$_SESSION['bbank_userid'];
  $u_name=$_SESSION['bbank_name'];
  $u_genid=$_SESSION['bbank_genid'];
  $base = "../../web_drive/".$userid."/";
  if(chdir($base)){
    $filenames = explode(',',$_POST['filenames']);
    $pwd = $_POST['pwd'];
    $optstatus=false;
    $wdl_src='';
    if($_SESSION['bbank_wstorage_data_bytes']<($_SESSION['bbank_wstorage_limit_bytes'])){
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
            $wdrive_projected_size=$_SESSION['bbank_wstorage_data_bytes'] + filesize($zipfile);
            $wdl_id = fileinode($zipfile);
            $wdl_src = $pwd.'/'.basename($zipfile);
            if($_SESSION['bbank_wstorage_limit_bytes'] >  $wdrive_projected_size){
              $_SESSION['bbank_wstorage_data_bytes'] = $wdrive_projected_size;
              $optstatus= true;
              $mesg='Compression has been completed successfully.';
              $fpwd =  str_replace ("'","''",$pwd);
              $sql="INSERT into bbank_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status, wdl_msg)
              values('compress', '$u_genid', '$wdl_src', '$fpwd', '$time',1,200)";
              $result = mysqli_query($con, $sql) or die("Adding bbank_webdrive_log to DB is failed");
              $sql="UPDATE bbank_users set wstorage_data_bytes=$wdrive_projected_size where genid='$u_genid' and userid='$userid' and wdrive_access=1";
              $result = mysqli_query($con, $sql) or die("Updating data size info to DB is failed");
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
      $mesg='You don\'t have sufficient space. Remove unnecessay documents and try again.';
    }
    if($optstatus==false){
      $fpwd = str_replace ("'","''",$pwd);
      $fwdl_src = str_replace ("'","''",$fwdl_src);
      $sql="INSERT into bbank_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg)
      values('compress', '$u_genid', '$fwdl_src', '$fpwd', '$time',0,500)";
      $result = mysqli_query($con, $sql) or die("Adding bbank_webdrive_log to DB is failed");
    }
    $data['opts']['status']=$optstatus;
    $data['opts']['msg']= $mesg;
    $session->closeDBConnection($con);
  }
  echo json_encode($data);
  ?>
