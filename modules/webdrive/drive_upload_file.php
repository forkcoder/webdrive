<?php
session_start();
$data = array();
$data['opts']['status'] = false;
$data['opts']['msg'] = 'You are not authorized to upload folder. Contact with site Administrator..';
require('../Servlets.php');
$session = new DBProxy();
if ($session->validate($_POST['auth_ph'], $_POST['ph'])) {
  $userid = $_SESSION['fcoder_userid'];
  $u_genid = $_SESSION['fcoder_genid'];
  $base = "../../web_drive/" . $userid . "/";
  $path = $_POST['filepath'];
  $name = $_POST['filename'];
  $size = $_POST['filesize'];
  if (isset($name) && !empty($name)) {
    if (($_SESSION['fcoder_wstorage_data_bytes'] + $size) <  $_SESSION['fcoder_wstorage_limit_bytes']) {
      if (chdir($base)) {
        if (file_exists($path . '/' . $name) == false) {
          if ($path == '.' || ($path[0] == '.' && $path[1] == '/')) {
            // if (in_array($_POST['filetype'], $_SESSION['fcoder_wdrive_types'])){
              $data['opts']['status'] = true;
              $data['chunk_upload_id'] = $cuid = uniqid();
              $_SESSION[$cuid] = 1;
              $data['opts']['msg'] = 'File transfered has been initiated...';
            // }else
            //   $data['opts']['msg'] = 'Kindly upload PDF or MS Office (doc, docx, ppt, etc.) document.';
          } else
            $data['opts']['msg'] = 'You are not authorized to upload folder. Contact with site Administrator..';
        } else
          $data['opts']['msg'] = 'File with identical name already exist.';
      }
    } else
      $data['opts']['msg'] = 'Please upload file in size less or equal to ' . $session->formatSizeUnits($_SESSION['fcoder_wstorage_limit_bytes'] - $_SESSION['fcoder_wstorage_data_bytes'],'') . ' Bytes';
      
  } else
    $data['opts']['msg'] = 'File Upload Failed due to Empty or Incorrect File Name.';
}
echo json_encode($data);
