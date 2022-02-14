<?php
session_start();
$server_address = strtok($_POST['access_key'], " ");
$client_ipaddress = strtok(" ");
$userid = strtok(" ");
$cursor_pos = 1048576 * $_POST['seq'];
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
if(isset($_SESSION[$cuid])){
    echo $_SESSION[$cuid];
     $_SESSION[$cuid]++; 
}
else echo 0;

