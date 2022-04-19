<?php
session_start();
$data = array();
$data['opts']['status']=false;
require('../WDProxy.php');
$session = new WDProxy();
if ($session->remote_validate($_POST['access_key'])){
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
  $userid=$_SESSION['bbank_userid'];
  $u_name=$_SESSION['bbank_name'];
  $u_genid=$_SESSION['bbank_genid'];
  $sender_genid = $_POST['snode'];
  $data['opts']['status']=false;
  $sql="SELECT userid FROM bbank_users iu where iu.genid= '$sender_genid'";
  $result = mysqli_query($con, $sql) or die("Fetching user from DB is failed " );
  $r=mysqli_fetch_row($result);
  $base = "../../web_drive/".$r[0]."/";
  $time = date("Y-m-d H:m:s");
  $sql="SELECT wds.id id, wds.wds_path file, wds.wds_name name, wds.wds_count count  FROM bbank_webdrive_sharemap wdsm, bbank_webdrive_share wds where wdsm.wdsm_share_id = wds.id and wdsm_iuser_id='$u_genid' and wds_owner='$sender_genid' and wdsm_readonly=1 and wdsm_status=1 and wds_status=1";
  $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed ");
  $totalshare = mysqli_num_rows($result);
  $data['opts']['status']=false;
  $sharedfiles = array();
  if($totalshare>=1){
    $sharecancelwith = $u_genid;
    while($row= mysqli_fetch_assoc($result)){
      $id=$row['id'];
      $status = 1;
      $sharecount = $row['count']-1;
      if($sharecount==0)
      $status=0;
      //remove share of existing shared file
      $sql="UPDATE bbank_webdrive_share set wds_status=$status, wds_count=$sharecount where id=$id";
      mysqli_query($con, $sql) or die("Updating shareinfo to DB is failed.");
      $sql="UPDATE bbank_webdrive_sharemap set wdsm_status=0, wdsm_removed_at='$time' where wdsm_status=1 and wdsm_share_id=$id";
      mysqli_query($con, $sql) or die("Removing shareinfo from DB is failed.");
      $data['opts']['status']=true;
    }
  }
  $data['sharelinks']=$sharedfiles;
  $session->closeDBConnection($con);
}
echo json_encode($data);
?>
