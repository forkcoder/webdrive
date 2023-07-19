<?php
session_start();
require('../Servlets.php');
$session = new DBProxy();
if (isset($_SESSION['fcoder_remember_token']) == false) {
  header("Location: /index.php");
  die();
} else {
  $rtoken = $_POST['token'];
  $con = $session->initDBConnection();
  $maxUploadSize = 204800; //Maximum size for uploaded Picture 100KB
  $rows = array();
  $u_sapid = $_SESSION['fcoder_genid'];
  $u_domainid = $_SESSION['fcoder_userid'];
  $rows['errors'] = array();
  $errors['key'] = array();
  $rows['opts']['status'] = false;
  $rows['opts']['msg'] = '';
  $content_length = (int) $_SERVER['CONTENT_LENGTH'];
  $name       = $_FILES['imageUpload']['name'];
  $temp_name  = $_FILES['imageUpload']['tmp_name'];
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $type  = $finfo->file($temp_name);
  $image_info = getimagesize($temp_name);
  if ($maxUploadSize - $content_length < 0) {
    $errors['key'][] = 'logged-user-img-id';
    $errors['msg'][] = 'Please upload Image in size less or equal to 200KB.';
  }
  if ($image_info[0] != 300 || $image_info[1] != 300) {
    $errors['key'][] = 'logged-user-img-id';
    $errors['msg'][] = 'Image Dimension (Height x Width) must be equal to (300x300) pixels.';
  }
  if (!isset($name) || empty($name)) {
    $errors['key'][] = 'logged-user-img-id';
    $errors['msg'][] = 'Image Upload Failed due to Empty or Incorrect File Name.';
  }
  if (!is_uploaded_file($temp_name)) {
    $errors['key'][] = 'logged-user-img-id';
    $errors['msg'][] = 'Image has not been uploaded yet.';
  }
  if ($type != "image/jpeg" && $type != "image/jpg" && $type != "image/png") {
    $errors['key'][] = 'logged-user-img-id';
    $errors['msg'][] = 'Kindly upload prefer format (PNG, JPEG or JPG) of Image file.';
  }
  if ($rtoken == $_SESSION['fcoder_remember_token']) {
    if (isset($_SESSION['mgmt_avater_count'])) {
      $u_avater_count = $_SESSION['mgmt_avater_count'] + 1;
    } else $u_avater_count = 1;
    $limit_avater_count = 5;
    if ($u_avater_count > $limit_avater_count) {
      $errors['key'][] = 'logged-user-img-id';
      $errors['msg'][] = 'You have already exceed the limit (' . $limit_avater_count . ') of changing Profile Image.';
    }
  }
  $rows['errors'] = $errors;
  if (count($errors['key']) == 0) {
    $rows['errors']['total'] = 0;
    $name = '../../images/profile/' . $rtoken;
    if (move_uploaded_file($temp_name, $name)) {
      if ($rtoken == $_SESSION['fcoder_remember_token']) {
        $sql = "UPDATE fcoder_users  set avater_count= $u_avater_count where remember_token='$rtoken'";
        $result = mysqli_query($con, $sql) or die("Update user to DB is failed" . $sql);
        $_SESSION['mgmt_avater_count'] = $u_avater_count;
      }
      $rows['opts']['status'] = true;
      $rows['logged-user-img'] = $name;
      $rows['opts']['msg'] = 'Image has been uploaded Successfully.';
    } else
      $rows['opts']['msg'] = 'Image has not been uploaded yet.';
  } else {
    unlink($temp_name);
    $rows['errors']['total'] = count($errors['key']);
  }
  echo json_encode($rows);
  $session->closeDBConnection($con);
}
