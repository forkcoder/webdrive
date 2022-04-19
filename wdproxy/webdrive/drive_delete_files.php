<?php
session_start();
require('../WDProxy.php');
$data = array();
$data['opts']['status']=false;
$session = new WDProxy();
if ($session->remote_validate($_POST['access_key'])){
  $con=$session->initDBConnection();

  $time= date("Y-m-d H:m:s");
  $userid=$_SESSION['bbank_userid'];
  $u_name=$_SESSION['bbank_name'];
  $u_genid=$_SESSION['bbank_genid'];

  $base = "../../web_drive/".$userid."/";
  $data['opts']['status']=false;
  if(chdir($base)){
    $filenames = explode(',',$_POST['filenames']);
    $pwd = $_POST['pwd'];
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
      $totalSize = 0;
      $filename = $filenames[$i];
      $path = realpath($filename);
      if(is_dir($path)){
        $files = new RecursiveIteratorIterator(
          new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach($files as $fileinfo){
          $func = ($fileinfo->isDir()?'rmdir':'unlink');
          $totalSize += $fileinfo->getSize();
          $func($fileinfo->getRealPath());
        }        
        rmdir($path);
      }
      else{
        $totalSize = filesize($path);
        unlink($path);
      }
      $fname = str_replace ("'","''",$filename);
      if(file_exists($path)==false){
        $sql="INSERT into bbank_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_datetime, wdl_status,wdl_msg)
        values('delete', '$u_genid', '$fname', '$time',1,200)";
        $result = mysqli_query($con, $sql) or die("Adding bbank_webdrive_log to DB is failed");
        $wdrive_projected_size = $_SESSION['bbank_wstorage_data_bytes'] - $totalSize;
        $_SESSION['bbank_wstorage_data_bytes'] = $wdrive_projected_size;
        $sql="UPDATE bbank_users set wstorage_data_bytes=$wdrive_projected_size where genid='$u_genid' and userid='$userid' and wdrive_access=1";
        $result = mysqli_query($con, $sql) or die("Updating data size info to DB is failed");
      }
      else{
        $sql="INSERT into bbank_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_datetime, wdl_status,wdl_msg)
        values('delete', '$u_genid', '$fname', '$time',0,500)";
        $result = mysqli_query($con, $sql) or die("Adding bbank_webdrive_log to DB is failed");
        $delFlag = false;
      }
    }
  }

  if($delFlag==false)
  $data['opts']['msg']='Failed to delete file(s). Contact with site Administrator..';
  else{
    $data['opts']['status']=$delFlag;
    $data['opts']['msg']='Delete operation has been completed successfully.';
  }
  $session->closeDBConnection($con);
}
echo json_encode($data);
?>
