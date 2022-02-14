<?php
session_start();
$server_address = strtok($_POST['access_key'], " ");
$client_ipaddress = strtok(" ");
$userid = strtok(" ");
$cursor_pos = getenv('BYTES_PER_CHUNK') * $_POST['seq'];
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
        while ($buff = fread($in, 1048576)) {
            fseek($com, $cursor_pos, SEEK_CUR);
            fwrite($com, $buff);
        }
    }
    fclose($in);
    fclose($com);
}
$data = array();
if(isset($_SESSION[$cuid])){
    $data['part'] = $_POST['seq'];
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


