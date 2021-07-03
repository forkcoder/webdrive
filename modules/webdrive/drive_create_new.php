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
  $optstatus=false;
  if(chdir($base)){
    $path = $_REQUEST['path'];
    $name = $_REQUEST['filename'];
    $files = array_diff(scandir($path), array('.', '..'));
    if(in_array($name, $files)){
      $mesg='Given name is already exist. Insert different name and try again.';
    }
    else{
      if($_SESSION['fcoder_wstorage_data_bytes']<($_SESSION['fcoder_wstorage_limit_bytes'])){
        if($path=='.' || ( $path[0]=='.'&& $path[1]=='/')){
          $filename = $path.'/'.$name;
          
          if(file_exists($filename)==false){
            if(mkdir($filename, 0740)){
              $oldmask = umask(0);
              chmod($filename, 0740);
              umask($oldmask);
              $inode=fileinode($filename);
              $mtime = filemtime($filename);
              $data['inode']=$inode;
              $data['mtime']=$mtime;
              $data['name']=$name;
              $data['path']=$filename;
              $data['size']=filesize($filename);
              $optstatus=true;
              $mesg='Folder ('.$name.') has been created Successfully.';
              $sql="INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg) values('createnew', '$u_genid', '$name', '$path', '$time',1,200)";
              $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed".$sql);
            }
            else
            $mesg='Failed to create new folder. Contact with webmaster@forkcoder.com.';
          }
          else{
            $mesg='Failed to create folder ('.$name.'). File name already exist.';
          }
        }
        else
        $mesg='You are not authorized to folder. Contact with webmaster@forkcoder.com.';
      }
      else
      $mesg='You drive has no sufficient free space. Remove unnecessay documents and try again.';
    }
  }
  if($optstatus==false){
    $sql="INSERT into fcoder_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status, wdl_msg) values('createnew', '$u_genid', '$name', '$path', '$time',0,500)";
    $result = mysqli_query($con, $sql) or die("Adding fcoder_webdrive_log to DB is failed");
  }
  $data['opts']['msg']= $mesg;
  $data['opts']['status']= $optstatus;
  echo json_encode($data);
  $session->closeDBConnection($con);
}
?>
