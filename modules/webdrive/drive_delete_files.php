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
  $data = array();
  $u_userid=$_SESSION['fcoder_userid'];
  $u_name=$_SESSION['fcoder_name'];
  $u_genid=$_SESSION['fcoder_genid'];

  $base = "../../web_drive/".$u_userid."/";
  $data['opts']['status']=false;
  if(chdir($base)){
    $filenames = explode(',',$_REQUEST['filenames']);
    $pwd = $_REQUEST['pwd'];
    $len = 0;
    $entries = array_diff(scandir($pwd), array('.', '..'));
    foreach($entries as $entry) {
      $file = $pwd.'/'.$entry;
      $key = array_search(fileinode($file),$filenames);
      if($key!==false){
        $filenames[$key]=$file;
        $len++;
      }
    }
    $delFlag = true;
    for($i=0;$i<$len;$i++){
      $filename = $filenames[$i];
      $path = realpath($filename);
      if(is_dir($path)){
        $files = new RecursiveIteratorIterator(
          new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach($files as $fileinfo){
          $func = ($fileinfo->isDir()?'rmdir':'unlink');
          $func($fileinfo->getRealPath());
        }
        rmdir($path);
      }
      else{
        unlink($path);
      }
      if(file_exists($path)==false){
        $sql="INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_datetime, wdl_status,wdl_msg) values('delete', '$u_genid', '$filename', '$time',1,200)";
        $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");
      }
      else{
        $sql="INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_datetime, wdl_status,wdl_msg) values('delete', '$u_genid', '$filename', '$time',0,500)";
        $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");
        $delFlag = false;
      }
    }
  }

  if($delFlag==false)
  $data['opts']['msg']='Failed to delete file(s). Contact with webmaster@forkcoder.com.';
  else{
    $data['opts']['status']=$delFlag;
    $data['opts']['msg']='Delete operation has been completed successfully.';
  }
  echo json_encode($data);
  $session->closeDBConnection($con);
}
?>
