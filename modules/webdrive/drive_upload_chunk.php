<?php
session_start();
require('../Servlets.php');
$session = new DBProxy();
$userid = $_SESSION['fcoder_userid'];
$u_genid = $_SESSION['fcoder_genid'];
$part = (int)$_POST['seq']; //sequence number of chunk
$bpc = (int)$_POST['bpc']; //bytes per chunk
$cursor_pos = $bpc * $part; //moving position of filepointer
$tmp_name = $_FILES['fileToUpload']['tmp_name'];
$size = $_FILES['fileToUpload']['size'];
$name = $_FILES['fileToUpload']['name'];
$cuid = $_POST['cuid']; //identify the base of of chunk  

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
if (isset($_SESSION[$cuid])) {
    $data['part'] = $part;
    $data['cuid_seq'] = $_SESSION[$cuid];
    $data['status'] = true;
    if ($_SESSION[$cuid] == $_POST['total_chunks']) {
        $con = $session->initDBConnection();
        $wdrive_projected_size = $_SESSION['fcoder_wstorage_data_bytes'] + $size;
        $_SESSION['fcoder_wstorage_data_bytes'] = $wdrive_projected_size;
        $sql = "UPDATE fcoder_users set wstorage_data_bytes=$wdrive_projected_size where genid='$u_genid' and userid='$userid' and wdrive_access=1";
        $result = mysqli_query($con, $sql) or die("Updating data size info to DB is failed");
        $session->closeDBConnection($con);
        unset($_SESSION[$cuid]);
    } else
        $_SESSION[$cuid]++;
} else {
    $data['status'] = false;
}
echo json_encode($data);
