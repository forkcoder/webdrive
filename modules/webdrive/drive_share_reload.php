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
  $data['opts']['status']=true;
  $u_userid=$_SESSION['fcoder_userid'];
  $u_name=$_SESSION['fcoder_name'];
  $u_genid=$_SESSION['fcoder_genid'];
  $sender_genid = $_REQUEST['snode'];
  $filelist = array();
  $linkedlist = array();
  $sql="SELECT userid FROM fcoder_users iu where iu.genid= '$sender_genid'";
  $result = mysqli_query($con, $sql) or die("Fetching user from DB is failed " );
  $r=mysqli_fetch_row($result);
  $base = "../../web_drive/".$r[0]."/";

  $filelist = array();
  $linkedlist = array();
  $sharedfiles = array();
  $sql="SELECT wds.id id, wds.wds_path file, wds.wds_name name  FROM fcoder_webdrive_sharemap wdsm, fcoder_webdrive_share wds where wdsm.wdsm_share_id = wds.id and wdsm_iuser_id='$u_genid' and wds_owner='$sender_genid' and wdsm_readonly=1 and wdsm_status=1 and wds_status=1";
  $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed ".$sql);
  $totalshare = mysqli_num_rows($result);
  if(chdir($base)==true && $totalshare>=1){
    $snode =$sender_genid;
    $data['snode']=$snode;
    $filelist[$snode]['inode'] = $snode;
    $filelist[$snode]['ipath'] = "".$snode;
    $filelist[$snode]['name'] = $snode;
    $filelist[$snode]['size'] =0;
    $filelist[$snode]['dir']=true;
    $filelist[$snode]['ext']='';
    $filelist[$snode]['path']='.';
    $filelist[$snode]['parent']=-1;
    while($rows= mysqli_fetch_assoc($result)){
      $sharedfiles['path'][] = $rows['file'];
      $sharedfiles['name'][] = $rows['name'];
    }
    $linkedlist[$snode]=array();
    for($sl=0;$sl<$totalshare;$sl++){
      $name=$sharedfiles['name'][$sl];
      $path= $sharedfiles['path'][$sl];
      $isdir=is_dir($path);
      $knode = fileinode($path);
      $filelist[$knode]['inode'] = $knode;
      $filelist[$knode]['ipath'] = $snode.'-'.$knode;
      $filelist[$knode]['name'] = $name;
      if($isdir)
      $filelist[$knode]['size'] =8192;
      else
      $filelist[$knode]['size'] =filesize($path);
      $filelist[$knode]['mtime'] = date("d F Y H:i:s",filemtime($path));
      $filelist[$knode]['dir']=$isdir;
      $filelist[$knode]['ext']=pathinfo($path,PATHINFO_EXTENSION);
      $filelist[$knode]['realpath']=$path;
      $filelist[$knode]['path']='./'.$name;
      $filelist[$knode]['parent']=$snode;
      array_push($linkedlist[$snode],$knode);
      if($isdir){
        $dfsStack = array();
        array_push($dfsStack, $knode);
        while(!empty($dfsStack)){
          $pnode = array_pop($dfsStack);
          $realbase = $filelist[$pnode]['realpath'];
          $base = $filelist[$pnode]['path'];
          $ipath = $filelist[$pnode]['ipath'];
          if(is_dir($realbase)){
            $linkedlist[$pnode]=array();
            $entries = array_diff(scandir($realbase), array('.', '..'));
            foreach($entries as $entry) {
              $realpath=$realbase.'/'.$entry;
              $path=$base.'/'.$entry;
              $inode = fileinode($realpath);
              $filelist[$inode]['inode'] = $inode;
              $filelist[$inode]['name'] = $entry;
              $filelist[$inode]['mtime'] = date("d F Y H:i:s",filemtime($realpath));
              $filelist[$inode]['parent']=$pnode;
              if(is_dir($realpath)) {
                $fsize= 8192;//filesize($realpath);
                $gpnode=$pnode;
                while($gpnode!=-1){
                  $filelist[$gpnode]['size'] += $fsize;
                  $gpnode = $filelist[$gpnode]['parent'];
                }
                $filelist[$inode]['size'] = $fsize;
                $filelist[$inode]['dir']=true;
                $filelist[$inode]['ext']='';
              }
              else {
                $fsize= filesize($realpath);
                $gpnode=$pnode;
                while($gpnode!=-1){
                  $filelist[$gpnode]['size'] += $fsize;
                  $gpnode = $filelist[$gpnode]['parent'];
                }
                $filelist[$inode]['size']=$fsize;
                $filelist[$inode]['dir']=false;
                $filelist[$inode]['ext']=pathinfo($realpath,PATHINFO_EXTENSION);
              }
              $filelist[$inode]['realpath'] = $realpath;
              $filelist[$inode]['path'] = $path;
              $filelist[$inode]['ipath']= $ipath."-".$inode;
              array_push($dfsStack, $inode);
              array_push($linkedlist[$pnode],$inode);
            }
            $linkedlist[$pnode]['total']= count($linkedlist[$pnode]);
          }
        }
      }
      $filelist[$knode]['path']='./'.$name;
    }
    $linkedlist[$snode]['total']=$totalshare;
    $data['opts']['status']=true;
  }
  else
  $data['opts']['msg']='Shared Documents not found.';
  $data['filelink']=$linkedlist;
  $data['filelist']=$filelist;
 
  echo json_encode($data);
  $session->closeDBConnection($con);
}
?>
