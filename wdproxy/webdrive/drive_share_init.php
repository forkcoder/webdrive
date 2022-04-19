<?php
session_start();
$data = array();
$data['opts']['status']=false;
require('../WDProxy.php');
$session = new WDProxy();
if ($session->remote_validate($_POST['access_key'])){
  $con=$session->initDBConnection();
  $data['opts']['status']=false;
  if($_SESSION['bbank_wshare_access']==1){
    $time = date("Y-m-d H:m:s");
    $userid=$_SESSION['bbank_userid'];
    $u_name=$_SESSION['bbank_name'];
    $u_genid=$_SESSION['bbank_genid'];

    // Information about shared from other users
    $sharednodes = array();
    $senderlist=array();
    $sql="SELECT wds.wds_owner sender, wds.wds_base base, wds.wds_title title, wds.id id FROM bbank_webdrive_sharemap wdsm, bbank_webdrive_share wds where wdsm.wdsm_share_id = wds.id and wdsm_iuser_id='$u_genid' and wdsm_readonly=1 and wdsm_status=1 and wds_status=1";
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
  $data['opts']['msg']='You are not authorized to Access Shared Inbox. Contact with site Administrator..';
  $session->closeDBConnection($con);
}
echo json_encode($data);
?>
