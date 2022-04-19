<?php
session_start();
$data = array();
$data['opts']['status']=false;
require('../WDProxy.php');
$session = new WDProxy();
if ($session->remote_validate($_POST['access_key'])){
  $con=$session->initDBConnection();
  $time= date("Y-m-d H:m:s");
  $userid=$_SESSION['bbank_userid'];
  $u_name=$_SESSION['bbank_name'];
  $u_genid=$_SESSION['bbank_genid'];

  $base = "../../web_drive/".$userid."/";
  $optstatus=false;
  if(chdir($base)){
    $path = $_POST['path'];
    $name = $_POST['filename'];
    $files = array_diff(scandir($path), array('.', '..'));
    if(in_array($name, $files)){
      $mesg='Given name is already exist. Insert different name and try again.';
    }
    else{
      if($_SESSION['bbank_wstorage_data_bytes']<($_SESSION['bbank_wstorage_limit_bytes'])){
        if($path=='.' || ( $path[0]=='.'&& $path[1]=='/')){
          $filename = $path.'/'.$name;

          if(file_exists($filename)==false){
            if(mkdir($filename, 0775)){
              // $oldmask = umask(0);
              // chmod($filename, 0775);
              // umask($oldmask);
              $inode=fileinode($filename);
              $mtime = filemtime($filename);
              $data['inode']=$inode;
              $data['mtime']=$mtime;
              $data['name']=$name;
              $data['path']=$filename;
              $data['size']=filesize($filename);
              $optstatus=true;
              $mesg='Folder ('.$name.') has been created Successfully.';
              $fpath = str_replace ("'","''",$path);
              $fname = str_replace ("'","''",$name);
              $sql="INSERT into bbank_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status,wdl_msg)
               values('mkdir', '$u_genid', '$fname', '$fpath', '$time',1,200)";
              $result = mysqli_query($con, $sql) or die("Adding bbank_webdrive_log to DB is failed");
            }
            else
            $mesg='Failed to create new folder. Contact with site Administrator..';
          }
          else{
            $mesg='Failed to create folder ('.$name.'). File name already exist.';
          }
        }
        else
        $mesg='You are not authorized to folder. Contact with site Administrator..';
      }
      else
      $mesg='You drive has no sufficient free space. Remove unnecessay documents and try again.';
    }
  }
  if($optstatus==false){
    $fname = str_replace ("'","''",$name);
    $fpath = str_replace ("'","''",$path);
    $sql="INSERT into bbank_webdrive_log (wdl_action, wdl_iuser_id, wdl_src, wdl_dest, wdl_datetime, wdl_status, wdl_msg)
    values('mkdir', '$u_genid', '$fname', '$fpath', '$time',0,500)";
    $result = mysqli_query($con, $sql) or die("Adding bbank_webdrive_log to DB is failed");
  }
  $data['opts']['msg']= $mesg;
  $data['opts']['status']= $optstatus;
  $session->closeDBConnection($con);
}
echo json_encode($data);
?>
