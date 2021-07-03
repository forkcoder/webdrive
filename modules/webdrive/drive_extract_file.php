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
  
  $time= date("Y-m-d H:m:s");
  $pwd = $_REQUEST['pwd'];
  $wdl_src= $_REQUEST['filename'];
  $u_userid= $_SESSION['fcoder_userid'];
  $u_name= $_SESSION['fcoder_name'];
  $u_genid= $_SESSION['fcoder_genid'];
  $optstatus=false;
  $base = "../../web_drive/".$u_userid."/";
  if(chdir($base)){
    $filename= $wdl_src;
    if($_SESSION['fcoder_wstorage_data_bytes']+filesize($filename)<$_SESSION['fcoder_wstorage_limit_bytes']){
      $zip= new ZipArchive();
      if($zip->open(realpath($filename))==true){
        $filename= realpath($pwd).'/'.pathinfo($filename)['filename'];
        $count= 1;
        $temp= $filename;
        while(file_exists($filename)){
          $filename= $temp.'('.$count.')';
          $count = $count+1;
        }
        $zip->extractTo($filename);
        $zip->close();

        $files = new RecursiveIteratorIterator(
          new RecursiveDirectoryIterator($filename, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
        );
        $totalSize =0;
        foreach($files as $file){
          $totalSize +=$file->getSize();
        }
        $wdrive_projected_size= $_SESSION['fcoder_wstorage_data_bytes'] + $totalSize;
        if($_SESSION['fcoder_wstorage_limit_bytes'] >  $wdrive_projected_size){
          $_SESSION['fcoder_wstorage_data_bytes'] = $wdrive_projected_size;
          $optstatus= true;
          $sql="INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg) values('extract', '$u_genid', '$wdl_src', '$pwd', '$time',1,200)";
          $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");
          $mesg='Extract operation has been completed successfully.';
        }
        else{
          foreach($files as $fileinfo){
            $func = ($fileinfo->isDir()?'rmdir':'unlink');
            $func($fileinfo->getRealPath());
          }
          rmdir($filename);
          $mesg='You don\'t have sufficient space.  Remove unnecessay documents and try again.';
        }

        //delete insecure file
        // $acceptedTypes = [".csv","application/vnd.openxmlformats-gspacedocument.spreadsheetml.sheet","application/vnd.ms-excel","application/pdf","application/msword","application/vnd.openxmlformats-gspacedocument.wordprocessingml.document","application/vnd.ms-powerpoint","application/vnd.openxmlformats-gspacedocument.presentationml.presentation","application/vnd.ms-xpsdocument","application/zip", "application/x-zip", "application/x-zip-compressed", "image/png", "image/jpeg", "image/gif"];
        // $files = new RecursiveIteratorIterator(
        //   new RecursiveDirectoryIterator($filename),
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

      }
      else
      $mesg='Failed to extract ('.basename($filename).'). Contact with webmaster@forkcoder.com.';
    }
    else
    $mesg='Web drive is full. Remove unnecessay documents and try again.';
  }
  if($optstatus==false){
    $sql="INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg) values('extract', '$u_genid', '$wdl_src', '$pwd', '$time',0,500)";
    $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");
  }
  $data['opts']['status']= $optstatus;
  $data['opts']['msg']=$mesg;
  echo json_encode($data);
  $session->closeDBConnection($con);
}
?>
