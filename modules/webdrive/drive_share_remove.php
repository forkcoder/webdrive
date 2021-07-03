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
  function dirSize($path){
    $totalSize =0;
    if(is_dir($path)){
      $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
      foreach($iterator as $file){
        $totalSize +=$file->getSize();
      }
    }
    else $totalSize = filesize($path);
    return $totalSize;
  }

  $con=$session->initDBConnection();
  
  $time = date("Y-m-d H:m:s");
  $u_userid=$_SESSION['fcoder_userid'];
  $u_name=$_SESSION['fcoder_name'];
  $u_genid=$_SESSION['fcoder_genid'];
  $base = "../../web_drive/".$u_userid."/";
  if(chdir($base)){
    $path = $_REQUEST['file'];
    $sharecancelwith = $_REQUEST['sharecancelwith'];
    $data['opts']['status']=false;
    $name = basename($path);
    $ctime= date("Y-m-d h:m:s",filectime($path));
    $size = dirSize($path);

    $sql="SELECT id, wds_count FROM fcoder_webdrive_share where wds_name='$name'  and wds_owner='$u_genid' and wds_path='$path' and wds_status=1 and wds_count>0";
    $result = mysqli_query($con, $sql) or die("Fetching shareinfo from DB is failed.");
    if(mysqli_num_rows($result)==1){
      $row=mysqli_fetch_row($result);
      $id=$row[0];
      $status = 1;
      if(strlen($sharecancelwith)==0) $sharecount =0;
      else $sharecount = $row[1]-1;

      if($sharecount==0){
        $status=0;
        $mysharesize= $_SESSION['fcoder_wshare_data_bytes']-$size;
        $data['mysharesize']= $mysharesize;
        $_SESSION['fcoder_wshare_data_bytes']=$mysharesize;
      }
      $data['sharecount']=$sharecount;
      //remove share of existing shared file
      $sql="UPDATE fcoder_webdrive_share set wds_status=$status, wds_count=$sharecount where id=$id";
      $result = mysqli_query($con, $sql) or die("Updating shareinfo to DB is failed.");
      if($sharecancelwith!='')
      $sql="UPDATE fcoder_webdrive_sharemap set wdsm_status=0, wdsm_removed_at='$time' where wdsm_status=1 and wdsm_share_id=$id and wdsm_iuser_id='$sharecancelwith'";
      else
      $sql="UPDATE fcoder_webdrive_sharemap set wdsm_status=0, wdsm_removed_at='$time' where wdsm_status=1 and wdsm_share_id=$id";
      $result = mysqli_query($con, $sql) or die("Removing shareinfo from DB is failed.");
      $data['opts']['status']=true;
    }
    else {
      $data['opts']['msg']='Failed to remove shared. Please Contact with webmaster@forkcoder.com.'.$sql;
    }
  }
  echo json_encode($data);
  $session->closeDBConnection($con);
}
?>
