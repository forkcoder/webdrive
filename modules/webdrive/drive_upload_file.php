<?php
session_start();
require('../Servlets.php');
$session = new DBProxy();
if($session->validate($_POST['auth_ph'], $_POST['ph'])==false){
  header("Location: /index.php");
  die();
}
else{

  $time = date("Y-m-d H:m:s");
  $u_userid=$_SESSION['fcoder_userid'];
  $u_name=$_SESSION['fcoder_name'];
  $u_genid=$_SESSION['fcoder_genid'];
  $base = "../../web_drive/".$u_userid."/";
  $freeSpace= $_SESSION['fcoder_wstorage_limit_bytes'] - $_SESSION['fcoder_wstorage_data_bytes'];
  $response = array();
  $response['opts']['status']=false;
  $total = 0;
  $content_length=(int) $_SERVER['CONTENT_LENGTH'];
  $path = $_POST['path'];
  $name = $_FILES['filesUpload']['name'];
  if($freeSpace-$content_length>0){
    $response['opts']['status']=true;

    if(chdir($base)){
      $count =1;
      $temp = $path.'/'.$name;
      while(file_exists($temp)){
        $name= pathinfo($temp,PATHINFO_FILENAME).'('.$count.').'.pathinfo($temp,PATHINFO_EXTENSION);
        $temp = $path.'/'.$name;
        $count = $count+1;
      }
      $temp_name  = $_FILES['filesUpload']['tmp_name'];
      if(isset($name) && !empty($name)){
        if(is_uploaded_file($temp_name)){
          $creationTime = time();
          if($path=='.' || ( $path[0]=='.'&& $path[1]=='/')){
            $filename =$path.'/'.$name;
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $type  = $finfo->file($temp_name);
            if(in_array($type, $_SESSION['fcoder_wdrive_types'])){
              if(move_uploaded_file($temp_name, $filename)){
                $oldmask = umask(0);
                chmod($filename, 0755);
                umask($oldmask);
                $response['summary']['inode']= fileinode($filename);
                $response['summary']['name']=$name;
                $response['summary']['status']='Uploaded';
                $response['summary']['msg']='File has been Uploaded Successfully.';
              }
              else{
                $response['summary']['name']=$name;
                $response['summary']['status']='Unauthorized';
                $response['summary']['msg']='File has not been uploaded yet. Contact with webmaster@forkcoder.com.';
              }
            }
            else {
              $response['summary']['type']=$type;
              $response['summary']['name']=$name;
              $response['summary']['status']='Mismatch';
              unlink($temp_name);
              $response['summary']['msg']='Kindly upload PDF or MS Office (doc, docx, ppt, etc.) document.';
            }
          }
          else{
            $response['summary']['name']=$name;
            $response['summary']['status']='Denied';
            $response['summary']['msg']='You are not authorized to upload folder. Contact with webmaster@forkcoder.com.';
          }
        }
        else{
          $response['summary']['name']=$name;
          $response['summary']['status']='Failed';
          $response['summary']['msg']='File has not been uploaded yet. Contact with webmaster@forkcoder.com.';
        }
      }
      else{
        $response['summary']['name']=$name;
        $response['summary']['status']='Incorrect';
        $response['summary']['msg']='File Upload Failed due to Empty or Incorrect File Name.';
      }
    }
  }
  else{
    $response['opts']['msg']='Please upload file in size less or equal to '.$session->formatSizeUnits($freeSpace).' Bytes';
  }
  $response['summary']['name']=$name;
  echo json_encode($response);
}
?>
