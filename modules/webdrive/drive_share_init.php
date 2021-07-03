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
  $data['opts']['status']=false;
  if($_SESSION['fcoder_wshare_access']==1){
    
    
    $time = date("Y-m-d H:m:s");
    $data = array();
    $u_userid=$_SESSION['fcoder_userid'];
    $u_name=$_SESSION['fcoder_name'];
    $u_genid=$_SESSION['fcoder_genid'];

    // Information about shared from other users
    $sharednodes = array();
    $senderlist=array();
    $sql="SELECT wds.wds_owner sender, wds.wds_base base, wds.wds_title title, wds.id id FROM fcoder_webdrive_sharemap wdsm, fcoder_webdrive_share wds where wdsm.wdsm_share_id = wds.id and wdsm_iuser_id='$u_genid' and wdsm_readonly=1 and wdsm_status=1 and wds_status=1";
    $result = mysqli_query($con, $sql) or die("Fetching share list from DB is failed ");
    while($rows= mysqli_fetch_assoc($result)){
      $sender = $rows['sender'];
      if(in_array($sender, $senderlist)==false){
        array_push($senderlist,$sender);
        $sharednodes[$sender]['base']=$rows['base'];
        $sharednodes[$sender]['title']=$rows['title'];
      }
    }
    $data['sharenodes']=$sharednodes;
    $data['shareindex']=$senderlist;
    $data['sharenodes']['total']=count($senderlist);
    $data['opts']['status']=true;
  }
  else
  $data['opts']['msg']='You are not authorized to Access Shared Inbox. Contact with webmaster@forkcoder.com.';
  echo json_encode($data);
  $session->closeDBConnection($con);
}
?>
