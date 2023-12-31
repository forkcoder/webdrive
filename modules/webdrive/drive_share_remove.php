<?php
session_start();
$data = array();
$data['opts']['status']=false;
require('../Servlets.php');
$session = new DBProxy();
if ($session->validate($_POST['auth_ph'], $_POST['ph'])){
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
  $userid=$_SESSION['fcoder_userid'];
  $u_name=$_SESSION['fcoder_name'];
  $u_genid=$_SESSION['fcoder_genid'];
  $base = "../../web_drive/".$userid."/";
  if(chdir($base)){
    $path = $_POST['file'];
    $sharecancelwith = $_POST['sharecancelwith'];
    $data['opts']['status']=false;
    $name = basename($path);
    $ctime= date("Y-m-d h:m:s",filectime($path));
    $size = dirSize($path);
    $fname = str_replace ("'","''",$name);
    $sql="SELECT id, wds_count FROM fcoder_webdrive_share where wds_name='$fname'  and wds_owner='$u_genid' and wds_path='$path' and wds_status=1 and wds_count>0";
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
        $sql="UPDATE fcoder_users set wshare_data_bytes=$mysharesize where genid='$u_genid' and userid='$userid' and wdrive_access=1";
        $result = mysqli_query($con, $sql) or die("Updating data size info to DB is failed");
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
      $data['opts']['msg']='Failed to remove shared. Please Contact with site Administrator..';
    }
  }
  $session->closeDBConnection($con);
}
echo json_encode($data);
?>
