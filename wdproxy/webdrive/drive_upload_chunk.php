<?php
session_start();
$server_address = strtok($_POST['access_key'], " ");
$client_ipaddress = strtok(" ");
$userid = strtok(" ");
$part = (int)$_POST['seq'];
$bpc = (int)$_POST['bpc'];
$cursor_pos = $bpc* $part ;
$tmp_name = $_FILES['fileToUpload']['tmp_name'];
$size = $_FILES['fileToUpload']['size'];
$name = $_FILES['fileToUpload']['name'];
$cuid = $_POST['cuid'];

$path = "../../web_drive/" . $userid . "/" . $_POST['filepath'];
$name = $_POST['filename'];
if (chdir($path)) {
    $com = fopen($_POST['filename'], "c+");
    $in = fopen($tmp_name, "rb");
    if ($in) {
        while ($buff = fread($in, $bpc)) {
            fseek($com, $cursor_pos, SEEK_CUR);
            fwrite($com, $buff);
        }
    }
    fclose($in);
    fclose($com);
}
$data = array();
 $data['bpc'] = $bpc;
if(isset($_SESSION[$cuid])){
    $data['part'] = $part;
    $data['cuid_seq'] = $_SESSION[$cuid];
    $data['status'] = true;
    if($_SESSION[$cuid] == $_POST['total_chunks'])
    unset($_SESSION[$cuid]);
    else
    $_SESSION[$cuid]++;
}
else{
$data['status'] = false;
} 
echo json_encode($data);


