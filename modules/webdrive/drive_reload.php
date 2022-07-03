<?php
session_start();
require('../Servlets.php');
$session = new DBProxy();
$data = array();
$data['opts']['status'] = false;
$data['opts']['msg'] = 'You are not authorized. Contact with site Administrator.';
if ($session->validate($_POST['auth_ph'], $_POST['ph']) === true) {
  if ($_SESSION['fcoder_wdrive_access'] == 1) {
    $time = date("Y-m-d H:m:s");
    $userid = $_SESSION['fcoder_userid'];
    $u_name = $_SESSION['fcoder_name'];
    $u_genid = $_SESSION['fcoder_genid'];

    $filelist = array();
    $linkedlist = array();
    $base = "../../web_drive/" . $userid . "/";

    $u_wstorage_limit = $_SESSION['fcoder_wstorage_limit_bytes'];
    $wdrivedatasize = 0;
    $sharednodes = array();
    $senderlist = array();
    if ($_SESSION['fcoder_wshare_access'] == 1) {
      $data['sharelimit'] = $_SESSION['fcoder_wshare_limit'];
      // Information about shared from other users
      $con = $session->initDBConnection();
      $sql = "SELECT wds.wds_owner sender, wds.wds_base base, wds.wds_title title, wds.id id, wds.wds_size size FROM fcoder_webdrive_sharemap wdsm, fcoder_webdrive_share wds where wdsm.wdsm_share_id = wds.id and wdsm_iuser_id='$u_genid' and wdsm_readonly=1 and wdsm_status=1 and wds_status=1";
      $result = mysqli_query($con, $sql) or die("Fetching users from DB is failed ");
      while ($rows = mysqli_fetch_assoc($result)) {
        $sender = $rows['sender'];
        if (in_array($sender, $senderlist) == false) {
          array_push($senderlist, $sender);
          $sharednodes[$sender]['base'] = $rows['base'];
          $sharednodes[$sender]['title'] = $rows['title'];
          $sharednodes[$sender]['size'] = 0;
        }
        $sharednodes[$sender]['size'] += (int)$rows['size'];
      }
      $data['sharenodes'] = $sharednodes;
      $data['shareindex'] = $senderlist;
      $data['sharenodes']['total'] = count($senderlist);
      $data['opts']['share'] = true;
    } else {
      $data['sharenodes'] = [];
      $data['shareindex'] = [];
      $data['sharenodes']['total'] = 0;
      $data['opts']['share'] = false;
    }

    $sharedbyme = array();
    $sharedfiles = array();
    $mysharesize = 0;
    $rnode = -1;
    if (file_exists($base)) {
      chdir($base);
      //Information about files shared by me
      if ($_SESSION['fcoder_wshare_access'] == 1) {
        $sql = "SELECT wds.id id,  wdsm.wdsm_iuser_id sharewith, wds.wds_path file, wds.wds_size size FROM fcoder_webdrive_sharemap wdsm, fcoder_webdrive_share wds where wdsm.wdsm_share_id = wds.id and wds.wds_owner='$u_genid' and wdsm_readonly=1 and wdsm_status=1 and wds_status=1";
        $result = mysqli_query($con, $sql) or die("Fetching my share from DB is failed ");
        while ($rows = mysqli_fetch_assoc($result)) {
          $file = $rows['file'];
          if (file_exists($file) && $_SESSION['fcoder_wshare_access'] == 1) {
            $file = fileinode($file);
            if ($file) {
              if (in_array($file, $sharedfiles) == false) {
                array_push($sharedfiles, $file);
                $mysharesize += $rows['size'];
              }
              $sharedbyme[$file][] = $rows['sharewith'];
            }
          } else {
            $id = $rows['id'];
            $sql = "UPDATE fcoder_webdrive_share set wds_status=0 where id=$id";
            $output = mysqli_query($con, $sql) or die("Updating shareinfo to DB is failed.");
            $sql = "UPDATE fcoder_webdrive_sharemap set wdsm_status=0, wdsm_removed_at='$time' where wdsm_status=1 and wdsm_share_id=$id";
            $output = mysqli_query($con, $sql) or die("Removing shareinfo from DB is failed.");
          }
        }
      }
      $data['sharedbyme'] = $sharedbyme;  //detail of sharedfiles i.e. which file has been shared with which user
      $data['sharedfiles'] = $sharedfiles; //list of shared file
      $data['sharedbyme']['total'] = count($sharedfiles);
      $data['mysharesize'] = $mysharesize;
      $_SESSION['fcoder_wshare_data_bytes'] = $mysharesize;

      $rnode = fileinode('.');
      $data['rnode'] = $rnode;
      $filelist[$rnode]['dir'] = true;
      $filelist[$rnode]['ext'] = '';
      $filelist[$rnode]['inode'] = $rnode;
      $filelist[$rnode]['ipath'] = "" . $rnode;
      $filelist[$rnode]['mtime'] = date("d F Y H:i:s", filemtime('.'));
      $filelist[$rnode]['name'] = $u_genid;
      $filelist[$rnode]['parent'] = -1;
      $filelist[$rnode]['path'] = '.';
      $filelist[$rnode]['size'] = 8192; //filesize($path);
      $dfsStack = array();
      array_push($dfsStack, $rnode);
      while (!empty($dfsStack)) {
        $pnode = array_pop($dfsStack);
        $base = $filelist[$pnode]['path'];
        $ipath = $filelist[$pnode]['ipath'];
        if (is_dir($base)) {
          $linkedlist[$pnode] = array();
          $entries = array_diff(scandir($base), array('.', '..'));
          foreach ($entries as $entry) {
            $path = $base . '/' . $entry;
            $inode = fileinode($path);
            $filelist[$inode]['inode'] = $inode;
            $filelist[$inode]['name'] = $entry;
            $filelist[$inode]['mtime'] = date("d F Y H:i:s", filemtime($path));
            $filelist[$inode]['parent'] = $pnode;
            if (is_dir($path)) {
              $fsize = 8192; //filesize($path);
              $gpnode = $pnode;
              while ($gpnode != -1) {
                $filelist[$gpnode]['size'] += $fsize;
                $gpnode = $filelist[$gpnode]['parent'];
              }
              $filelist[$inode]['size'] = $fsize;
              $filelist[$inode]['dir'] = true;
              $filelist[$inode]['ext'] = '';
            } else {
              $fsize = filesize($path);
              $gpnode = $pnode;
              while ($gpnode != -1) {
                $filelist[$gpnode]['size'] += $fsize;
                $gpnode = $filelist[$gpnode]['parent'];
              }
              $filelist[$inode]['size'] = $fsize;
              $filelist[$inode]['dir'] = false;
              $filelist[$inode]['ext'] = pathinfo($path, PATHINFO_EXTENSION);
            }
            $filelist[$inode]['path'] = $path;
            $filelist[$inode]['ipath'] = $ipath . "-" . $inode;
            array_push($dfsStack, $inode);
            array_push($linkedlist[$pnode], $inode);
          }
          $linkedlist[$pnode]['total'] = count($linkedlist[$pnode]);
        }
      }
      $wdrivedatasize = $filelist[$rnode]['size'];
      
      $_SESSION['fcoder_wstorage_data_bytes'] = $wdrivedatasize;
      $data['wdriveusedsize'] = $wdrivedatasize;
      $data['wdrivefsfactor'] = $wdrivedatasize / $u_wstorage_limit * 100;
      $data['opts']['status'] = true;
    } else {
      if (mkdir($base, 0775)) {
        // $oldmask = umask(0);
        // chmod($base, 0775);
        // umask($oldmask);
        if (chdir($base)) {
          $data['sharedbyme'] = $sharedbyme;  //detail of sharedfiles i.e. which file has been shared with which user
          $data['sharedfiles'] = $sharedfiles; //list of shared file
          $data['sharedbyme']['total'] = count($sharedfiles);
          $data['mysharesize'] = $mysharesize;
          $_SESSION['fcoder_wshare_data_bytes'] = $mysharesize;


          $_SESSION['fcoder_wstorage_data_bytes'] = $wdrivedatasize;
          $data['wdriveusedsize'] = $wdrivedatasize;
          $data['wdrivefsfactor'] = $wdrivedatasize / $u_wstorage_limit * 100;
          $rnode = fileinode('.');
          $data['rnode'] = $rnode;
          $filelist[$rnode]['inode'] = $rnode;
          $filelist[$rnode]['ipath'] = "" . $rnode;
          $filelist[$rnode]['name'] = $u_genid;
          $filelist[$rnode]['size'] = $_SESSION['fcoder_wstorage_data_bytes'] = $wdrivedatasize;
          $filelist[$rnode]['dir'] = true;
          $filelist[$rnode]['ext'] = '';
          $filelist[$rnode]['path'] = '.';
          $filelist[$rnode]['mtime'] = date("d F Y H:i:s", filemtime('.'));
          $filelist[$rnode]['parent'] = -1;
          $linkedlist[$rnode]['total'] = 0;
          // $data['opts']['msg']='Folder ('.$u_genid.') has been created Successfully.';
          $data['opts']['status'] = true;
        }
      }
    }
    foreach ($senderlist as $sender) {
      $filelist[$sender]['dir'] = false;
      $filelist[$sender]['ext'] = 'shared';
      $filelist[$sender]['inode'] = $sender;
      $filelist[$sender]['ipath'] = $rnode . "-" . $sender;
      $filelist[$sender]['mtime'] = date("d F Y H:i:s", filemtime('.'));
      $filelist[$sender]['name'] = $sharednodes[$sender]['title'];
      $filelist[$sender]['parent'] = $rnode;
      $filelist[$sender]['path'] = $sharednodes[$sender]['base'];
      $filelist[$sender]['size'] = $sharednodes[$sender]['size'];
      array_push($linkedlist[$rnode], (int)$sender);
      $linkedlist[$rnode]['total']++;
    }
    $data['filelink'] = $linkedlist;
    $data['filelist'] = $filelist;
    $data['basetree'] = '<div id="tnode-' . $rnode . '" class="tnodeStyle" onClick="webdriveModule.renderWebDrive(' . $rnode . ',false);webdriveModule.driveReload();" ><img id="tnode-img" src="images\\webdrive\\mydrive.png" height="20"/><span>My Drive</span></div><div class="innertnodeStyle" id="innertnode-' . $rnode . '"></div>';
    if ($_SESSION['fcoder_wshare_access'] == 1)
    $data['basetree'] .= '<div id="share-links-id"></div>';
    // $data['basetree'] .= '<div id="share-div" class="tnodeStyle" onClick="webdriveModule.toggleShareInbox()"><img id="snode-img" src="images\\webdrive\\shared.png" width="20" height="20"/><span>Shared by Others (<span id="share-links-count-id">0</span>)</span></div><div id="share-links-id"></div>';
    // $data['basetree'] .= '<div id="public-div" class="tnodeStyle" onClick=""><img id="snode-img" src="images\\webdrive\\publicfolder.png" width="20" height="20"/><span> Public Folder </span></div><div id="public-links-id"></div>';
  }
  $session->closeDBConnection($con);
} else
  $session->removeSession();
echo json_encode($data);
