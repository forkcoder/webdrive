<?php
session_start();
require('../Servlets.php');
$session = new DBProxy();
$userid = $_SESSION['fcoder_userid'];
$u_genid = $_SESSION['fcoder_genid'];
$part = (int)$_POST['seq']; //sequence number of chunk
$bpc = (int)getenv('BYTES_PER_CHUNK'); //bytes per chunk
$cursor_pos = $bpc * $part; //moving position of filepointer
$tmp_name = $_FILES['fileToUpload']['tmp_name'];
$size = $_FILES['fileToUpload']['size'];
$name = $_FILES['fileToUpload']['name'];
$cuid = $_POST['cuid']; //identify the base of of chunk  

$path = "../../web_drive/" . $userid . "/" . $_POST['filepath'];
$name = $_POST['filename'];
sleep(1);
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
$data[$cuid]['status'] = false;
if (isset($_SESSION[$cuid])) {
    $data[$cuid]['chunk'] = $_SESSION[$cuid];
    $data[$cuid]['status'] = true;
    $data[$cuid]['succeed'] = false;
    if ($_SESSION[$cuid] == $_POST['total_chunks']) {
        $session->uploadCompleted($size,$u_genid,$userid);
        unset($_SESSION[$cuid]);
        $data[$cuid]['succeed'] = true;
    } else
        $_SESSION[$cuid]++;
}
echo json_encode($data);
