<?php
session_start();
include("header.php");
$con = $session->initDBConnection();
$session->reloadSession($con);
$session->closeDBConnection($con);
$tdata = '';
if (isset($_SESSION['fcoder_userid']) == false || $_SESSION['fcoder_userid'] == '') {
  $session->removeSession();
  header("location: index.php");
  die();
}
?>
<div id="mainContentDiv">
  <div id="webDriveDiv" class="notextselect" onClick="event.stopPropagation();webdriveModule.dismissAll();">
    <div id="webDriveLeftDiv">
      <div id="webDriveTree" class="general-scroll-bar-style"><img src="images\\loader.gif" style="width:20px;height:20px;align-self:center"></div>
    </div>

    <div id="webDriveRightDiv">
      <div id="webDriveMenus" onclick="event.stopPropagation()">
        <span class="menuButton" style="min-width:30px;vertical-align:middle" id="wdrive-grid-list-id" onclick="webdriveModule.listView(this)"><img style="height:20px;" src="images\\webdrive\\list.png"></span>
        <span class="menuButton" style="min-width:20px;" id="wdrive-back-btn-id" onclick="webdriveModule.backDir()"><img id="wdrive-back-img-id" style="height:20px;vertical-align:middle" src="images\\webdrive\\backinactive.png"></span>
        <span class="menuButton" style="min-width:20px" id="wdrive-up-btn-id" onclick="webdriveModule.upDir()"><img id="wdrive-up-img-id" style="height:20px;vertical-align:middle" src="images\\webdrive\\upinactive.png"></span>
        <div style="position:relative">
          <div onclick="webdriveModule.createNew()" class="menuButton wdrive-menu-button"><img src="images\\webdrive\\createnew.png"><span> New</span></div>
        </div>
        <div id="wdrive-singlefileordir-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-singlefileordir-rename-id" onclick="webdriveModule.menuActHandler('rename')" class="menuButton wdrive-menu-button"><img src="images\\webdrive\\rename.png"> <span>Rename</span> </div>
        </div>
        <div id="wdrive-common-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-common-copy-id" onclick="webdriveModule.menuActHandler('copy')" class="menuButton  wdrive-menu-button"><img src="images\\webdrive\\copy.png"><span> Copy</span></div>
          <div id="wdrive-common-download-id" onclick="webdriveModule.menuActHandler('download')" class="menuButton wdrive-menu-button"><img src="images\\webdrive\\download.png"><span> Download</span></div>
        </div>
        <div id="wdrive-caution-menu-id" class="wdrive-group-menu-style">';
          <div id="wdrive-caution-move-id" onclick="webdriveModule.menuActHandler('move')" class="menuButton wdrive-menu-button"><img src="images\\webdrive\\move.png"><span> Move<span></div>
          <?php if ($_SESSION['fcoder_wshare_access'] == 1) { ?>
            <div id="wdrive-caution-share-id" onclick="webdriveModule.menuActHandler('share')" class="menuButton  wdrive-menu-button"><img src="images\\webdrive\\share.png"><span> Share</span></div>
          <?php } ?>
          <div id="wdrive-caution-delete-id" onclick="webdriveModule.menuActHandler('delete')" class="menuButton wdrive-menu-button"><img src="images\\webdrive\\delete.png"><span> Delete</span></div>
        </div>
        <div id="wdrive-compress-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-compress-compress-id" onclick="webdriveModule.menuActHandler('compress')" class="menuButton wdrive-menu-button"><img src="images\\webdrive\\compress.png"><span> Compress</span></div>
        </div>
        <div id="wdrive-extract-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-extract-extract-id" onclick="webdriveModule.menuActHandler('extract')" class="menuButton wdrive-menu-button"><img src="images\\webdrive\\extract.png"><span> Extract</span></div>
        </div>
        <div id="wdrive-paste-menu-id" class="wdrive-group-menu-style">
          <div id="wdrive-paste-paste-id" onclick="webdriveModule.menuActHandler('paste')" class="menuButton wdrive-menu-button"><img src="images\\webdrive\\paste.png"><span> Paste</span></div>
        </div>
      </div>
      <div class="hd-rldv">
        <div id="context-menu-id" onclick="event.stopPropagation();"></div>
      </div>
      <div id="uploaderID" style="width:100%;height:100%;overflow-y:auto;overflow-x:hidden" onClick="event.stopPropagation();webdriveModule.dismissAll();" class="general-scroll-bar-style">
        <div id="webDriveDashboard" onclick="event.stopPropagation();webdriveModule.dismissAll()"></div>
        <div class="hd-fccc" style="opacity:0.6;width:150px;margin-left:auto;margin-right:auto" onmouseover="this.style.opacity='1.0';" onmouseout="this.style.opacity='0.6';">
          <span>Drop your Files Here</span>
          <span style="margin:7px auto">Or</span>
          <button style="display:block;width:120px; height:25px;cursor:pointer" onclick="document.getElementById('web-drive-file-upload-id').click()">Upload Files</button>
          <input type="file" style="display:none;" id="web-drive-file-upload-id" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-powerpoint, application/vnd.openxmlformats-officedocument.presentationml.presentation, application/vnd.ms-xpsdocument, application/x-rar, application/x-rar-compressed, application/octet-stream, application/zip, application/x-zip, application/x-zip-compressed, image/x-png, image/jpeg, image/gif" onchange="webdriveModule.fileUpload()" multiple />
        </div>
      </div>
      <div class="hd-rldv" style="width:100%;" id="shareWith"> </div>
    </div>
  </div>
  <div class="hd-rldv">
    <div id="webDrivePWD"><img src="images\\loader.gif" style="width:20px;height:20px"></div>
  </div>
</div>
<div id="previewPane" class="supermodal" onclick="event.stopPropagation();exitSuperModal()">
  <div class="modal" id="wdrive-modal-content">
  </div>
</div>
<script type="text/javascript">
  window.onload = function() {
    webdriveModule.init();
  };
</script>
<?php include("footer.php"); ?>