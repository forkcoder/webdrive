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
  $path = $_REQUEST['path'];
  $newname = $_REQUEST['newname'];
  $oldname = $_REQUEST['oldname'];
  $inode = $_REQUEST['inode'];
  $optstatus=false;
  if(chdir($base)){
    $files = array_diff(scandir($path), array('.', '..'));
    if(in_array($newname, $files)){
      $mesg='Folder name already exist. Insert different name and try again.';
    }
    else{
      if($_SESSION['fcoder_wstorage_data_bytes']<($_SESSION['fcoder_wstorage_limit_bytes'])){
        if($path=='.' || ( $path[0]=='.' && $path[1]=='/')){
          $oldname=$path.'/'.$oldname;
          $newname=$path.'/'.$newname;
          $finfo = new finfo(FILEINFO_MIME_TYPE);
          $oldtype  = $finfo->file($oldname);
          if(rename($oldname, $newname)){
            if($oldtype == $finfo->file($newname)){
              $optstatus=true;
              $sql="INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg) values('rename', '$u_genid', '$oldname', '$newname', '$time',1,200)";
              $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");
              $mesg='Folder ('.$oldname.') has been changed to ('.$newname.') Successfully.';
            }
            else{
              rename($newname,$oldname);
              $mesg='Please rename file/folder ('.basename($oldname).') without changing format.';
            }
          }
          else
          $mesg='Failed to rename folder ('.basename($oldname).'). Contact with webmaster@forkcoder.com.';
        }
        else
        $mesg='You are not authorized to folder. Contact with webmaster@forkcoder.com.';
      }
      else
      $mesg='Web drive is full. Remove unnecessay documents and try again.';
    }
  }
  if($optstatus==true)
  $sql="INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg) values('rename', '$u_genid', '$oldname', '$newname', '$time',1,200)";
  else
  $sql="INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg) values('rename', '$u_genid', '$oldname', '$newname', '$time',1,500)";
  $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");

  $data['opts']['msg'] = $mesg;
  $data['opts']['status']= $optstatus;
  echo json_encode($data);
  $session->closeDBConnection($con);
}
?>
