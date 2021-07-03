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
  $data = array();
  $data['opts']['status']=false;
  if($_SESSION['fcoder_wdrive_access']==1){
    
    $time = date("Y-m-d H:m:s");
    $u_userid=$_SESSION['fcoder_userid'];
    $u_name=$_SESSION['fcoder_name'];
    $u_genid=$_SESSION['fcoder_genid'];

    $filelist = array();
    $linkedlist = array();
    $base = "../../web_drive/".$u_userid."/";

    $u_wstorage_limit = $_SESSION['fcoder_wstorage_limit_bytes'];
    if($_SESSION['fcoder_ftransfer_access']==1)
    $data['opts']['ftransfer']=true;
    else 
    $data['opts']['ftransfer']=false;

    if($_SESSION['fcoder_wshare_access']==1){
      $data['sharelimit']=$_SESSION['fcoder_wshare_limit'];
      // Information about shared from other users
      $sharednodes = array();
      $senderlist=array();
      $sql="SELECT wds.wds_owner sender, wds.wds_base base, wds.wds_title title, wds.id id FROM fcoder_webdrive_sharemap wdsm, fcoder_webdrive_share wds where wdsm.wdsm_share_id = wds.id and wdsm_iuser_id='$u_genid' and wdsm_readonly=1 and wdsm_status=1 and wds_status=1";
      $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed ");
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
      $data['opts']['share']=true;
    }
    else{
      $data['sharenodes']=[];
      $data['shareindex']=[];
      $data['sharenodes']['total']=0;
      $data['opts']['share']=false;
    }

    $sharedbyme=array();
    $sharedfiles=array();
    $mysharesize=0;
    $rnode = -1;
    if(file_exists($base)){
      chdir($base);
      //Information about files shared by me
      $sql="SELECT wds.id id,  wdsm.wdsm_iuser_id sharewith, wds.wds_path file, wds.wds_size size FROM fcoder_webdrive_sharemap wdsm, fcoder_webdrive_share wds where wdsm.wdsm_share_id = wds.id and wds.wds_owner='$u_genid' and wdsm_readonly=1 and wdsm_status=1 and wds_status=1";
      $result = mysqli_query($con, $sql) or die("Fetching my share from DB is failed ");
      while($rows= mysqli_fetch_assoc($result)){
        $file = $rows['file'];
        if(file_exists($file) && $_SESSION['fcoder_wshare_access']==1){
          $file = fileinode($file);
          if($file){
            if(in_array($file, $sharedfiles)==false){
              array_push($sharedfiles,$file);
              $mysharesize +=$rows['size'];
            }
            $sharedbyme[$file][]=$rows['sharewith'];
          }
        }
        else{
          $id = $rows['id'];
          $sql="UPDATE fcoder_webdrive_share set wds_status=0 where id=$id";
          $output = mysqli_query($con, $sql) or die("Updating shareinfo to DB is failed.");
          $sql="UPDATE fcoder_webdrive_sharemap set wdsm_status=0, wdsm_removed_at='$time' where wdsm_status=1 and wdsm_share_id=$id";
          $output = mysqli_query($con, $sql) or die("Removing shareinfo from DB is failed.");
        }
      }
      $data['sharedbyme']=$sharedbyme;  //detail of sharedfiles i.e. which file has been shared with which user
      $data['sharedfiles']=$sharedfiles; //list of shared file
      $data['sharedbyme']['total']=count($sharedfiles);
      $data['mysharesize']= $mysharesize;
      $_SESSION['fcoder_wshare_data_bytes']= $mysharesize;

      $rnode= fileinode('.');
      $data['rnode']=$rnode;
      $filelist[$rnode]['inode'] = $rnode;
      $filelist[$rnode]['ipath'] = "".$rnode;
      $filelist[$rnode]['name'] = $u_genid;
      $filelist[$rnode]['size'] =8192;//filesize($path);
      $filelist[$rnode]['dir']=true;
      $filelist[$rnode]['ext']='';
      $filelist[$rnode]['path']='.';
      $filelist[$rnode]['parent']=-1;
      $dfsStack = array();
      array_push($dfsStack, $rnode);
      while(!empty($dfsStack)){
        $pnode = array_pop($dfsStack);
        $base = $filelist[$pnode]['path'];
        $ipath = $filelist[$pnode]['ipath'];
        if(is_dir($base)){
          $linkedlist[$pnode]=array();
          $entries = array_diff(scandir($base), array('.', '..'));
          foreach($entries as $entry) {
            $path=$base.'/'.$entry;
            $inode = fileinode($path);
            $filelist[$inode]['inode'] = $inode;
            $filelist[$inode]['name'] = $entry;
            $filelist[$inode]['mtime'] = date("d F Y H:i:s",filemtime($path));
            $filelist[$inode]['parent']=$pnode;
            if(is_dir($path)) {
              $fsize= 8192;//filesize($path);
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
              $fsize= filesize($path);
              $gpnode=$pnode;
              while($gpnode!=-1){
                $filelist[$gpnode]['size'] += $fsize;
                $gpnode = $filelist[$gpnode]['parent'];
              }
              $filelist[$inode]['size']=$fsize;
              $filelist[$inode]['dir']=false;
              $filelist[$inode]['ext']=pathinfo($path,PATHINFO_EXTENSION);
            }
            $filelist[$inode]['path'] = $path;
            $filelist[$inode]['ipath']= $ipath."-".$inode;
            array_push($dfsStack, $inode);
            array_push($linkedlist[$pnode],$inode);
          }
          $linkedlist[$pnode]['total']= count($linkedlist[$pnode]);
        }
      }
      $wdrivedatasize= $filelist[$rnode]['size'];
      $_SESSION['fcoder_wstorage_data_bytes']=$wdrivedatasize;
      $data['wdrivefreesize']=$u_wstorage_limit-$wdrivedatasize;
      $data['wdrivefsfactor']=$wdrivedatasize/$u_wstorage_limit*100;
      $data['opts']['status']=true;
    }
    else{
      if(mkdir($base, 0755)){
        $oldmask = umask(0);
        chmod($base, 0755);
        umask($oldmask);
        if(chdir($base)){
          $data['sharedbyme']=$sharedbyme;  //detail of sharedfiles i.e. which file has been shared with which user
          $data['sharedfiles']=$sharedfiles; //list of shared file
          $data['sharedbyme']['total']=count($sharedfiles);
          $data['mysharesize']= $mysharesize;
          $_SESSION['fcoder_wshare_data_bytes']= $mysharesize;

          $wdrivedatasize= 0;
          $_SESSION['fcoder_wstorage_data_bytes']=$wdrivedatasize;
          $data['wdrivefreesize']=$u_wstorage_limit-$wdrivedatasize;
          $data['wdrivefsfactor']=$wdrivedatasize/$u_wstorage_limit*100;
          $rnode = fileinode('.');
          $data['rnode']=$rnode;
          $filelist[$rnode]['inode'] = $rnode;
          $filelist[$rnode]['ipath'] = "".$rnode;
          $filelist[$rnode]['name'] = $u_genid;
          $filelist[$rnode]['size'] = $_SESSION['fcoder_wstorage_data_bytes']= $wdrivedatasize;
          $filelist[$rnode]['dir']=true;
          $filelist[$rnode]['ext']='';
          $filelist[$rnode]['path']='.';
          $filelist[$rnode]['parent']=-1;
          $linkedlist[$rnode]['total']=0;
          $data['opts']['msg']='Folder ('.$u_genid.') has been created Successfully.';
          $data['opts']['status']=true;
        }
      }
    }
    $data['filelink']=$linkedlist;
    $data['filelist']=$filelist;
    $data['basetree']='<div id="tnode-'.$rnode.'" class="tnodeStyle" onClick="webdriveModule.renderWebDrive('.$rnode.',false);webdriveModule.driveReload();" ><img id="tnode-img" src="images\\webdrive\\mydrive.png" height="20"/><span>My Drive</span></div><div class="innertnodeStyle" id="innertnode-'.$rnode.'"></div>';
    if($_SESSION['fcoder_wshare_access']==1)
    $data['basetree'].='<div id="share-div" class="tnodeStyle" onClick="webdriveModule.toggleShareInbox()"><img id="snode-img" src="images\\webdrive\\sharedinbox.png" width="20" height="20"/><span>Shared Inbox (<span id="share-links-count-id">0</span>)</span></div><div id="share-links-id"></div>';
  }
  else
  $data['opts']['msg']='You are not authorized. Contact with webmaster@forkcoder.com.';
  echo json_encode($data);
  $session->closeDBConnection($con);
}
?>
