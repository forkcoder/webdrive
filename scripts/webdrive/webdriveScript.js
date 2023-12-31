var webdriveModule = {
  active: false,
  previewPath: '',
  opcode: '',
  divholder: '',
  sharedbyme: [],
  sharedfiles: [],
  filelist: [],
  filelink: [],
  share_access: false,
  sharelist: [],
  sharelinks: [],
  shareindex: [],
  sharenodes: [],
  mysharesize: 0,
  mysharelimit: 0,
  dnodesLayout: 'icon',
  previousUpStat: '',
  rnode: '',
  snode: '',
  app_user:'',
  actionFor: [],
  actionForCopy: [],
  backStack: [],
  sharedFlag: false,
  copyFromShare: false,
  actionForMoveFlag: false,
  moveStatus: false,
  copiedFrom: -1,
  preRenameID: '',
  treePWD: '',
  dboardPWD: '',
  fileNames: '',
  commonMenu: '',
  cautionmenu: '',
  singleFDMenu: '',
  compressMenu: '',
  extractMenu: '',
  pastemenu: '',
  mousePosition: [],
  isDown: false,
  targetDiv: '',
  users: [],
  upload_limit: 0,
  total_uploads: 0,
  total_receipients: 0,
  chunk_upload_queue: [],
  bytes_per_chunk: 1048576,
  getBytesPerChunk: function () {
    return this.bytes_per_chunk;
  },
  setBytesPerChunk: function (val) {
    this.bytes_per_chunk = val;
  },
  getPreviewPath: function (inode) {
    if (this.getSharedflag()){
      if(this.getShareInfo(inode)['parent']!='')
        return this.previewPath +  this.getSharebase(this.getShareInfo(inode)['parent']) + "\\" + this.getShareInfo(inode)['realpath'];
    }
    else {
        return this.previewPath + this.getAppUser() +"\\" + this.getFileInfo(inode)['path'];
    }
  },
  setPreviewPath: function (val) {
    this.previewPath = val;
  },

  getUploadLimit: function () {
    return this.upload_limit;
  },
  setUploadLimit: function (value) {
    this.upload_limit = value;
  },
  getTotalUploads: function () {
    return this.total_uploads;
  },
  setTotalUploads: function (value) {
    this.total_uploads = value;
  },
  getTotalRecipients: function () {
    return this.total_receipients;
  },
  setTotalRecipients: function (value) {
    this.total_receipients = value;
  },
  getUsers: function () {
    return this.users;
  },
  setUsers: function (users) {
    this.users = users;
  },
  getAppUser: function(){
    return this.app_user;
  },
  setAppUser: function(user){
    this.app_user = user;
  },
  getUser: function (id) {
    return this.users[id];
  },
  setUser: function (id, key, val) {
    this.users[id][key] = val;
  },
  pushSelectedUsers: function (genid) {
    this.selectedUsers.push(genid);
  },
  popSelectedUsers: function (genid) {
    let index = this.selectedUsers.indexOf(genid);
    if (index != -1) this.selectedUsers.splice(index, 1);
  },
  setShareaccess: function (value) {
    this.share_access = value;
  },
  getShareaccess: function () {
    return this.share_access;
  },
  setOpcode: function (value) {
    this.opcode = value;
  },
  getOpcode: function () {
    return this.opcode;
  },
  resetOpcode: function () {
    this.opcode = '';
    this.disableTopMenus();
  },
  setMysharesize: function (value) {
    this.mysharesize = value;
  },
  getMysharesize: function () {
    return this.mysharesize;
  },
  setMysharelimit: function (value) {
    this.mysharelimit = value;
  },
  getMysharelimit: function () {
    return this.mysharelimit;
  },
  refreshBackStack: function () {
    let size = this.backStack.length;
    let pattern = 's-', val;
    for (let i = 0; i < size; i++) {
      val = this.backStack[i].split('-');
      if (pattern.match(val[0])) {
        this.backStack.splice(i, 1);
        --size;
      }
    }
  },
  setSharedflag: function (value) {
    this.sharedFlag = value;
  },
  getSharedflag: function () {
    return this.sharedFlag;
  },
  setCopyFromShare: function (value) {
    this.copyFromShare = value;
  },
  getCopyFromShare: function () {
    return this.copyFromShare;
  },
  getMoveStatus: function () {
    return this.moveStatus;
  },
  setMoveStatus: function (status) {
    this.moveStatus = status;
  },
  getPreRenameID: function () {
    return this.preRenameID;
  },
  setPreRenameID: function (value) {
    this.preRenameID = value;
  },
  getFileNames: function () {
    return this.fileNames;
  },
  setFileNames: function (value) {
    this.fileNames = value;
  },
  setCopiedFrom: function (value) {
    this.copiedFrom = value;
  },
  getCopiedFrom: function () {
    return this.copiedFrom;
  },
  setDnodesLayout: function (value) {
    this.dnodesLayout = value;
  },
  getDnodesLayout: function () {
    return this.dnodesLayout;
  },
  updateActionMenuList: function (el) {
    let paamenu = document.getElementsByClassName('actionForMenu');
    let len = paamenu.length;
    for (let i = 0; i < len; i++) {
      paamenu[i].classList.remove('actionForMenu');
    }
    if (el != '' && el != null) el.classList.add('actionForMenu');
  },
  getActionFor: function () {
    return this.actionFor;
  },
  setActionForCopy: function (value) {
    this.actionForCopy = value;
  },
  getActionForCopy: function () {
    return this.actionForCopy;
  },
  setActionForMoveFlag: function (value) {
    this.actionForMoveFlag = value;
  },
  getActionForMoveFlag: function () {
    return this.actionForMoveFlag;
  },
  getActiveActionMenu: function () {
    return this.activeActionMenu;
  },
  setTreePWD: function (value) {
    this.treePWD = value;
  },
  getTreePWD: function () {
    return this.treePWD;
  },
  setRnode: function (value) {
    this.rnode = value;
  },
  getRnode: function () {
    return this.rnode;
  },
  setSnode: function (value) {
    this.snode = value;
  },
  getSnode: function () {
    return this.snode;
  },
  setDboardPWD: function (value) {
    this.dboardPWD = value;
  },
  getDboardPWD: function () {
    return this.dboardPWD;
  },
  setSharedfiles: function (list) {
    this.sharedfiles = list;
  },
  getSharedfiles: function () {
    return this.sharedfiles;
  },
  setSharedbyme: function (list) {
    this.sharedbyme = list;
  },
  getSharedbyme: function (key) {
    return this.sharedbyme[key];
  },
  setFilelist: function (list) {
    this.filelist = list;
  },
  updateFilelist: function (opcode, inode, pnode, data) {
    if (opcode == 'rename') {
      this.filelist[inode]['name'] = data;
    }
    else if (opcode == 'mkdir') {
      this.filelist[inode] = data;
      let total = this.filelink[pnode]['total'];
      this.filelink[pnode][total] = inode;
      this.filelink[pnode]['total'] = total + 1;
      this.filelink[inode] = [];
      this.filelink[inode]['total'] = 0;
    }
    // this.renderWebDrive(pnode, this.getSharedflag());
    this.renderTree(pnode, this.getSharedflag());
    this.renderDashboard(pnode, this.getSharedflag());
  },
  setShareindex: function (list) {
    this.shareindex = list;
  },
  getShareindex: function () {
    return this.shareindex;
  },
  setSharenodes: function (list) {
    this.sharenodes = list;
  },
  getSharenodes: function () {
    return this.sharenodes;
  },
  getSharebase: function (node) {
    return this.sharenodes[node]['base'];
  },
  getSharetitle: function (node) {
    return this.sharenodes[node]['title'];
  },
  getFilelist: function () {
    return this.filelist;
  },
  getSharelist: function () {
    return this.sharelist;
  },
  setSharelist: function (value) {
    this.sharelist = value;
  },
  getSharelinks: function () {
    return this.sharelinks;
  },
  setSharelinks: function (value) {
    this.sharelinks = value;
  },
  getFileInfo: function (inode) {
    return this.filelist[inode];
  },
  getShareInfo: function (inode) {
    return this.sharelist[inode];
  },
  setFilelink: function (link) {
    this.filelink = link;
  },
  getFilelink: function (id) {
    return this.filelink[id];
  },
  getSharelink: function (id) {
    return this.sharelinks[id];
  },
  adjustHeight: function () {
    let height = document.getElementById('mainContentDiv').clientHeight;
    document.getElementById("webDriveLeftDiv").style.height = height- 30;
    document.getElementById("webDriveRightDiv").style.height = height-30;
    // document.getElementById("webDriveTree").style.height = height;
  },

  init: function () {
    chk_session();
    if (window.XMLHttpRequest)
      xmlhttp = new XMLHttpRequest();
    else
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    xmlhttp.onreadystatechange = function () {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        try {
          var res = JSON.parse(xmlhttp.responseText);
          if (res['opts']['status'] == true) {
            if (webdriveModule.active != true) {
              document.getElementById('shareWithID').innerHTML = res['shareWithID'];
              webdriveModule.commonMenu = document.getElementById('wdrive-common-menu-id');
              webdriveModule.cautionMenu = document.getElementById('wdrive-caution-menu-id');
              webdriveModule.singleFDMenu = document.getElementById('wdrive-singlefileordir-menu-id');
              webdriveModule.compressMenu = document.getElementById('wdrive-compress-menu-id');
              webdriveModule.extractMenu = document.getElementById('wdrive-extract-menu-id');
              webdriveModule.pasteMenu = document.getElementById('wdrive-paste-menu-id');

              webdriveModule.setUsers(res['users']);
              webdriveModule.setAppUser(res['app_user']);
              webdriveModule.setUploadLimit(res['opts']['ul']);
              webdriveModule.setTotalRecipients(res['opts']['tr']);
              webdriveModule.setTotalUploads(res['opts']['tu']);
              webdriveModule.setBytesPerChunk(res['bytes_per_chunk']);
              webdriveModule.setPreviewPath(res['opts']['previewpath']);
              webdriveModule.adjustHeight();
              webdriveModule.driveReload();
              webdriveModule.active = true;
              webdriveModule.enableDragNDrop();
            }
            else {
              webdriveModule.renderWebDrive(webdriveModule.getRnode(), webdriveModule.getSharedflag());
              webdriveModule.driveReload();
            }
          }
          else
            showNotificationMsg('failed', res['opts']['msg']);
        } catch (e) {
          console.log(e); //If any runtime error
        }
      }
    }
    xmlhttp.open("GET", "/modules/webdrive/drive_init.php?auth_ph=" + auth_ph + "&ph=" + ph, true);
    xmlhttp.send();
  },
  enableDragNDrop: function () {
    // IF DRAG-DROP UPLOAD SUPPORTED
    if (window.File && window.FileReader && window.FileList && window.Blob) {
      /* [THE ELEMENTS] */
      var uploaderID = document.getElementById('uploaderID');
      if (uploaderID != null) {
        /* [VISUAL - HIGHLIGHT DROP ZONE ON HOVER] */
        uploaderID.addEventListener("dragenter", function (e) {
          e.preventDefault();
          e.stopPropagation();
          uploaderID.classList.add('highlight');
        });
        uploaderID.addEventListener("dragleave", function (e) {
          e.preventDefault();
          e.stopPropagation();
          uploaderID.classList.remove('highlight');
        });

        /* [UPLOAD MECHANICS] */
        // STOP THE DEFAULT BROWSER ACTION FROM OPENING THE FILE
        uploaderID.addEventListener("dragover", function (e) {
          e.preventDefault();
          e.stopPropagation();
        });

        // ADD OUR OWN UPLOAD ACTION
        uploaderID.addEventListener("drop", function (e) {
          e.preventDefault();
          e.stopPropagation();
          uploaderID.classList.remove('highlight');
          webdriveModule.setOpcode('upload');
          webdriveModule.start(e.dataTransfer.files);
        });
      }
    }

  },
  driveReload: function () {
    chk_session();
    var url = "/modules/webdrive/drive_reload.php";
    var xmlhttp = "";
    if (window.XMLHttpRequest)
      xmlhttp = new XMLHttpRequest();
    else
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    xmlhttp.open('POST', url, true);

    xmlhttp.timeout = 300000;
    xmlhttp.ontimeout = function (e) {
      showNotificationMsg('failed', "Drive Reload Timed-out. Kindly try to reload webdrive again.");
    };
    var formData = new FormData();
    formData.append('auth_ph', auth_ph);
    formData.append('ph', ph);
    xmlhttp.send(formData);
    xmlhttp.onreadystatechange = function () {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        // try {
          var res = JSON.parse(xmlhttp.responseText);
          if (res['opts']['status'] == true) {
            document.getElementById('wdrive-used-space-id').innerHTML = webdriveModule.sizeInMegaBytes(res['wdriveusedsize'], false);
            let wdfs = Math.floor(res['wdrivefsfactor'])
            document.getElementById('wdrive-fs-factor').style.strokeDasharray = "" + (100 - wdfs) + ", 200";
            document.getElementById('wdrive-fs-percentage').innerHTML = wdfs + '%';
            webdriveModule.setMysharesize(webdriveModule.sizeInMegaBytes(res['mysharesize'], false));
            webdriveModule.setMysharelimit(res['sharelimit']);
            webdriveModule.setSharedbyme(res['sharedbyme']);
            webdriveModule.setSharedfiles(res['sharedfiles']);
            webdriveModule.setFilelist(res['filelist']);
            webdriveModule.setFilelink(res['filelink']);
            webdriveModule.setSharenodes(res['sharenodes']);
            webdriveModule.setShareindex(res['shareindex']);
            webdriveModule.setShareaccess(res['opts']['share']);
            if (webdriveModule.getShareaccess())
              webdriveModule.prepareUserPopups();
            let el = document.getElementById("uploaderID");
            if (el.addEventListener) {
              el.addEventListener('contextmenu', function (e) {
                e.preventDefault();
                webdriveModule.displayContextMenu(this, e);
              }, false);
            }
            else {
              document.attachEvent('oncontextmenu', function () {
                windows.event.returnValue = false;
              });
            }
            document.getElementById('context-menu-id').style.display = 'none';
            if (webdriveModule.getRnode() == '') {
              let rnode = res['rnode'];
              webdriveModule.setRnode(rnode);
              webdriveModule.setTreePWD(rnode);
              webdriveModule.setDboardPWD(rnode);
              webdriveModule.backStack.push('r-' + rnode);
              document.getElementById("webDriveTree").innerHTML = res['basetree'];
              webdriveModule.setSharedflag(false);
              webdriveModule.renderWebDrive(rnode, false);
            }
            else
              webdriveModule.renderWebDrive(webdriveModule.getDboardPWD(), webdriveModule.getSharedflag());
            if (webdriveModule.getShareaccess() == true)
              webdriveModule.renderShareInbox();
          }
          else {
            showNotificationMsg('alert', res['opts']['msg']);
            logout();
          }
        // } catch (e) {
        //   //miss me
        //   logout();
        // }
      }
    }
  },
  driveShareReload: function (snode) {
    chk_session();
    if (window.XMLHttpRequest)
      xmlhttp = new XMLHttpRequest();
    else
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    var url = "/modules/webdrive/drive_share_reload.php";
    xmlhttp.open('POST', url, true);
    var formData = new FormData();
    formData.append('snode', snode);
    formData.append('auth_ph', auth_ph);
    formData.append('ph', ph);
    xmlhttp.onreadystatechange = function () {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        try {
          var res = JSON.parse(xmlhttp.responseText);
          if (res['opts']['status'] == true) {
            let snode = res['snode'];
            webdriveModule.setSnode(snode);
            webdriveModule.setSharedflag(true);
            webdriveModule.renderShareInbox();
            res['filelist'][snode]['parent'] = webdriveModule.getRnode();
            webdriveModule.setSharelist(res['filelist']);
            webdriveModule.setSharelinks(res['filelink']);
            document.getElementById('context-menu-id').style.display = 'none';
            webdriveModule.resetOpcode();
            webdriveModule.refreshBackStack();
            webdriveModule.renderWebDrive(snode, true);
          }
          else
            showNotificationMsg('failed', res['opts']['msg']);
        } catch (e) {
          console.log(e); //If any runtime error
        }
      }
    }
    xmlhttp.send(formData);
  },
  renderWebDrive: function (inode, sharedFlag) {
    document.getElementById('context-menu-id').style.display = 'none';
    let copyFlag = false;
    let copiedFiles = this.getActionForCopy();
    for (let i = 0; i < copiedFiles.length; i++) if (copiedFiles[i] == inode) copyFlag = true;
    if (copyFlag == true) this.discardActionForCopy();
    this.renderTree(inode, sharedFlag);
    this.renderDashboard(inode, sharedFlag);
    this.pushDir(inode, sharedFlag);
    this.setSharedflag(sharedFlag);
  },
  renderShareInbox: function () {
    let sharenodes = this.getSharenodes();
    let shareindex = this.getShareindex();
    let totalFiles = sharenodes['total'], fileTree = '';
    for (let i = 0; i < totalFiles; i++) {
      let node = shareindex[i];
      fileTree = fileTree + '<div id="snode-' + node + '" class="tnodeStyle" onClick="webdriveModule.driveShareReload(' + node + ');" ><img id="simg-' + node + '" src="images\\webdrive\\regularsnode.png" width="20" height="20"/><span>' + this.getSharetitle(node) + '</span><div class="wd-remove-shared-link-button" onClick="event.stopPropagation();webdriveModule.removesharelink(' + node + ');"></div></div><div class="innertnodeStyle" id="innersnode-' + node + '"></div>';
    }
    // document.getElementById('share-links-count-id').innerHTML = sharenodes['total'];
    document.getElementById('share-links-id').innerHTML = fileTree;
  },
  resetTnode: function (inode) {
    let tbas = this.backStack.pop();
    let value = tbas.split('-'), node, img;
    if (value[0] == 's') {
      node = document.getElementById('snode-' + inode);
      if (node != null) node.style.color = 'navy';
      img = document.getElementById('simg-' + inode);
      if (img != null) img.src = 'images\\webdrive\\regularsnode.png';
    }
    else {
      node = document.getElementById('tnode-' + inode);
      if (node != null) node.style.color = 'navy';
      img = document.getElementById('timg-' + inode)
      if (img != null) img.src = 'images\\webdrive\\regularnode.png';
    }
    this.backStack.push(tbas);
  },
  renderTree: function (pnode, sharedFlag) {
    let totalFiles, fileTree;
    let npf = 'tnode-', ipf = 'timg-', inpf = 'innertnode-', nimg = 'regularnode.png', animg = 'activenode.png';
    let files = this.getFilelink(pnode);
    if (sharedFlag == true) {
      npf = 'snode-'; ipf = 'simg-', inpf = 'innersnode-', nimg = 'regularsnode.png', animg = 'activesnode.png';
      files = this.getSharelink(pnode);
      totalFiles = files['total'];
    }
    this.resetTnode(this.getTreePWD());
    document.getElementById(npf + pnode).style.color = 'darkred';
    if (pnode != this.getRnode())
      document.getElementById(ipf + pnode).src = 'images\\webdrive\\' + animg;

    this.setTreePWD(pnode);
    let el = document.getElementById(inpf + pnode);
    totalFiles = files['total'];
    let name, inode;
    fileTree = '';
    for (let i = 0; i < totalFiles; i++) {
      if (sharedFlag)
        file = this.getShareInfo(files[i]);
      else
        file = this.getFileInfo(files[i]);
      inode = file['inode'];
      name = file['name'];
      if (name.length > 13) {
        name = name.substr(0, 13);
        let partlen = Math.min(name.length, name.lastIndexOf(" "));
        if (partlen > 0)
          name = name.substr(0, partlen);
        name = '<div class="tnodetip" title="' + file['name'] + '" >' + name + "..." + '</div>';
      }
      if (file['dir'] == true && file['ext'] != 'shared') {
        fileTree = fileTree + '<div id="' + npf + inode + '" class="tnodeStyle" onClick="webdriveModule.renderWebDrive(' + inode + ',' + sharedFlag + ');" ><img id="' + ipf + inode + '" src="images\\webdrive\\' + nimg + '" width="20" height="20"/><span>' + name + '</span></div>';
        fileTree = fileTree + '<div class="innertnodeStyle"  id="' + inpf + inode + '"></div>';
      }
    }
    el.innerHTML = fileTree;
  },

  openImg: function (inode) {
    document.getElementById('context-menu-id').style.display = 'none';
    if (this.getPreviewPath(inode) != false) {
      let wdmc = document.getElementById("wdrive-modal-content");
      const img = new Image();
      // img.onload = function () {
      //   // webdriveModule.renderImgHolder(wdmc, this.width, this.height);
      // }
      img.style.maxWidth = '80%';
      img.style.maxHeight = '90%';
      img.src = this.getPreviewPath(inode);
      img.classList.add('modal-body-content');
      wdmc.setAttribute('style', '');
      wdmc.innerHTML = "";
      wdmc.appendChild(img);
      wdmc.style.flexGrow = "0";
      wdmc.style.backgroundColor = "none";
      displaySuperModal('wdrive-modal-content');
    }
    else showNotificationMsg('alert', 'Failed to display image. Please download the image to view it.');
  },
  openPdf: function (inode) {
    document.getElementById('context-menu-id').style.display = 'none';
    if (this.getPreviewPath(inode) != false) {
      let ppath = this.getPreviewPath(inode);
      let wdmc = document.getElementById("wdrive-modal-content");
      wdmc.setAttribute('style', '');
      wdmc.innerHTML = '<object data="' + ppath + '" type="application/pdf"  width="90%" height="100%" class="modal-body-content">\
      <embed src="' + ppath + '">\
          This browser does not support PDFs. Please download the PDF to view it: <a href="' + ppath + '">Download PDF</a>.</p>\
      </embed>\
  </object>';
      wdmc.style.width = '90%';
      wdmc.style.flexGrow = "1";

      displaySuperModal('wdrive-modal-content');
    }
    else showNotificationMsg('alert', 'Failed to display image. Please download the image to view it.');
  },
  renderDashboard: function (pnode, sharedFlag) {
    document.getElementById('context-menu-id').style.display = 'none';
    webdriveModule.disableTopMenus();
    document.getElementById("webDrivePWD").innerHTML = this.renderLinks(pnode, sharedFlag);
    if (this.getShareaccess() == true)
      document.getElementById('wdrive-myshare-size-id').innerHTML = this.getMysharesize();
    if (this.getDboardPWD() != pnode)
      this.discardShare();
    // this.resetOpcode(); Remove share does not work properly
    this.setDboardPWD(pnode);

    let files;
    if (sharedFlag) {
      files = this.getSharelink(pnode);
    }
    else {
      this.setPreRenameID('');
      files = this.getFilelink(pnode);
    }
    var fileDispUx = [], headLine = [];
    let file, filename, inode, type, size, time, hline, clickOnAction, uXdata, tstyle, dstyle;
    this.setFileNames([]);
    let totalFiles = files['total'];
    let sharedFiles = this.getSharedfiles();

    for (let i = 0; i < totalFiles; i++) {
      if (sharedFlag)
        file = this.getShareInfo(files[i]);
      else
        file = this.getFileInfo(files[i]);
      filename = file['name'];
      let tcut, shortname = filename;
      if (filename.length > 20) {
        tcut = Math.min(20, filename.lastIndexOf(" "));
        shortname = filename.substr(0, tcut == -1 ? 8 : tcut) + "...";
      }
      clickOnAction = '';

      this.fileNames.push(filename);
      time = file['mtime'];
      inode = file['inode'];
      type = file['ext'];
      hline = 'Other Files';
      size = '<span>' + showFileSizeInBytes(file['size']) + '</span>';
      tstyle = '';
      dstyle = ''
      clickOnAction = 'onclick="webdriveModule.selectFileFor(this,event)" ';
      if (type == 'docx') { type = 'doc'; hline = 'Word Documents'; }
      else if (type == 'xlsx') { type = 'xls'; hline = 'Excels/Spreadsheets'; }
      else if (type == 'pptx') { type = 'ppt'; hline = 'Powerpoint Files/Presentations'; }
      else if (type == 'pdf') { type = 'pdf'; hline = 'Portable Documents (PDF)'; clickOnAction = clickOnAction + ' ondblclick="webdriveModule.openPdf(' + inode + ');"'; }
      else if (type == 'png' || type == 'jpeg' || type == 'gif' || type == 'jpg') { type = 'image'; hline = 'Images'; clickOnAction = clickOnAction + 'ondblclick="webdriveModule.openImg(' + inode + ');"'; }
      else if (type == 'zip' || type == 'rar') { type = 'zip'; hline = 'Compressed Files'; }
      else if (type == 'shared') { hline = 'Shared by Others'; clickOnAction = 'onclick="webdriveModule.driveShareReload(' + inode + ');" ondblclick="webdriveModule.driveShareReload(' + inode + ');"'; tstyle = ' style="color:darkblue;text-decoration:underline;cursor:pointer;min-height:100%" '; dstyle = ' style="" ' }
      else if (file['dir'] == true) { type = 'folder'; hline = 'Folder(s)'; clickOnAction = clickOnAction + 'ondblclick="webdriveModule.renderWebDrive(' + inode + ',' + sharedFlag + ');"'; }
      else type = 'file';
      if (fileDispUx[type] == null || typeof fileDispUx[type] === 'undefined') {
        fileDispUx[type] = '';
        headLine[type] = hline;
      }
      uXdata = '';
      if (this.getDnodesLayout() != 'list')
        uXdata = uXdata + '<div id="dnode-' + inode + '" class="diconStyle"  ' + dstyle + clickOnAction + ' >';
      else
        uXdata = uXdata + '<div id="dnode-' + inode + '"   class="dlistStyle"  ' + clickOnAction + ' >';

      if (sharedFlag)
        uXdata = uXdata + '<img src="images\\webdrive\\' + type + '.png"/><div class="hd-rldv"><img style="position:absolute;right:-2px;bottom:-2px;z-index:1;width:20px;height:20px" src="images\\webdrive\\shared.png"/></div>';
      else if (sharedFiles.indexOf(inode) > -1)
        uXdata = uXdata + '<div class="hd-rldv"><img style="position:absolute;left:-5px;bottom:-2px;z-index:1;width:24px;height:24px" src="images\\webdrive\\sharedfile.png"/></div><img src="images\\webdrive\\' + type + '.png"/>';
      else
        uXdata = uXdata + '<img src="images\\webdrive\\' + type + '.png"/>';

      if (this.getDnodesLayout() != 'list')
        uXdata = uXdata + '<div class="hd-fcss" style="flex-grow:1;"><span ' + tstyle + ' title="' + filename + '" id="dnode-name-' + inode + '" >' + shortname + '</span>' + size + '</div></div>';
      else
        uXdata = uXdata + '<span class="dlistNameStyle" ' + tstyle + ' value="' + filename + '" id="dnode-name-' + inode + '" >' + filename + '</span>' + size + '<span>' + time + '</span></div>';

      fileDispUx[type] = fileDispUx[type] + uXdata;

    }
    var fileOnDesk = "";
    let keys = Object.keys(fileDispUx).sort(function (a, b) {
      if (a.key == b.key) return 0;
      if (a.key == 'shared') return -1;
      if (b.key == 'shared') return 1;
      if (a.key < b.key)
        return -1;
      if (a.key > b.key)
        return 1;
      return 0;
    });;
    if (this.getDnodesLayout() != 'list') {
      for (let i = 0; i < keys.length; i++) {
        if (keys[i] == 'shared')
          fileOnDesk = fileOnDesk + '<div class="hd-fcss" style="margin-left:10px;margin-bottom:10px;flex:1 1 100%"><span class="headLine">' + headLine[keys[i]] + '</span><div class="hd-frss" style="flex-wrap:wrap;">' + fileDispUx[keys[i]] + '</div></div>';
        else
          fileOnDesk = fileOnDesk + '<div class="hd-fcss" style="margin-left:10px;margin-bottom:10px;"><span class="headLine">' + headLine[keys[i]] + '</span><div class="hd-frss" style="flex-wrap:wrap">' + fileDispUx[keys[i]] + '</div></div>';
      }
    }
    else {
      for (let i = 0; i < keys.length; i++) {
        fileOnDesk = fileOnDesk + '<div class="hd-fcss" style="margin-left:10px;margin-bottom:10px;"><span class="headLine">' + headLine[keys[i]] + '</span><div class="hd-fcss">' + fileDispUx[keys[i]] + '</div></div>';
      }
    }
    document.getElementById("webDriveDashboard").innerHTML = fileOnDesk;
    let item = '', id;
    for (let i = 0; i < totalFiles; i++) {
      if (sharedFlag)
        file = this.getShareInfo(files[i]);
      else
        file = this.getFileInfo(files[i]);
      inode = file['inode'];
      id = "dnode-" + inode.toString();
      if ((item = document.getElementById(id)) != null) {
        if (item.addEventListener) {
          item.addEventListener('contextmenu', function (e) {
            e.preventDefault();
            e.stopPropagation();
            webdriveModule.displayContextMenu(this, e);
          }, false);
        }
        else {
          document.attachEvent('oncontextmenu', function () {
            windows.event.returnValue = false;
          });
        }
      }
    }
    if (this.getActionFor().length > 0)
      this.renderActionFor();
    if (this.getActionForMoveFlag() && this.getActionForCopy().length > 0)
      this.enableActionForMove();
  },
  gridView: function (el) {
    document.getElementById('webDriveDashboard').style.flexDirection = 'row';
    this.setDnodesLayout('grid');
    this.renderDashboard(this.getDboardPWD(), this.getSharedflag());
    el.outerHTML = '<span class="menuButton" id="wdrive-grid-list-id" style="min-width:30px" onclick="webdriveModule.listView(this)"><span class="glyphicon glyphicon-th-list"> </span></span>';
  },
  listView: function (el) {
    document.getElementById('webDriveDashboard').style.flexDirection = 'column';
    this.setDnodesLayout('list');
    this.renderDashboard(this.getDboardPWD(), this.getSharedflag());
    el.outerHTML = '<span class="menuButton" id="wdrive-grid-list-id" style="min-width:30px" onclick="webdriveModule.gridView(this)"><span class="glyphicon glyphicon-th"> </span></span>';
  },
  renderLinks: function (inode, sharedFlag) {
    let path, ipath;
    if (sharedFlag) {
      path = this.getShareInfo(inode)['path'];
      ipath = this.getShareInfo(inode)['ipath'];
    }
    else {
      path = this.getFileInfo(inode)['path'];
      ipath = this.getFileInfo(inode)['ipath'];
    }
    let links = path.split('/');
    let ilinks = ipath.split('-');
    if (sharedFlag)
      path = '<div class="rlink" onClick="webdriveModule.renderWebDrive(' + this.getSnode() + ',' + sharedFlag + ');webdriveModule.driveShareReload(' + this.getSnode() + ');">Shared by (SAP ID: ' + this.getSnode() + ')</div>';
    else
      path = '<div class="rlink rootlink" onClick="webdriveModule.renderWebDrive(' + this.getRnode() + ',' + sharedFlag + ');webdriveModule.driveReload();">My Drive</div>';

    let len = links.length;
    let name = '';
    for (let i = 1; i < len; i++) {
      name = links[i];
      inode = ilinks[i];
      let partlen = Math.min(20, name.lastIndexOf(" "));
      if (partlen > 0)
        name = name.substr(0, partlen) + '...';
      path = path + '<div class="clink" onClick="webdriveModule.renderWebDrive(' + inode + ',' + sharedFlag + ');">' + name + '</div>';
    }
    return path;
  },
  createFolder: function () {
    let pnode = this.getDboardPWD();
    let path = this.getFileInfo(pnode)['path'];
    let ipath = this.getFileInfo(pnode)['ipath'];
    let input = document.getElementById('dnode-name-createNew');
    let name = input.value;
    if (name == null || name == "") {
      showNotificationMsg('alert', "Folder name is empty. Please write your folder name.");
      input.focus();
      return false;
    }
    else if (/[^a-zA-Z0-9_ \-\_\.\[\]\(\)]/.test(name)) {
      showNotificationMsg('alert', 'Folder name can only contain alphanumeric characters, hyphens(-), underscores(_), space( ), dots(.) and brackets([])');
      input.focus();
      return false;
    }

    if (window.XMLHttpRequest)
      xmlhttp = new XMLHttpRequest();
    else
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    var url = "/modules/webdrive/drive_create_new.php";
    xmlhttp.open('POST', url, true);
    var formData = new FormData();
    formData.append('filename', name);
    formData.append('path', path);
    formData.append('auth_ph', auth_ph);
    formData.append('ph', ph);
    xmlhttp.onreadystatechange = function () {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        try {
          var res = JSON.parse(xmlhttp.responseText);
          if (res['opts']['status'] == true) {
            input.value = '';
            webdriveModule.cancelNew();
            webdriveModule.discardActionFor();
            let fileinfo = [], inode = res['inode'];
            fileinfo['dir'] = true;
            fileinfo['ext'] = '';
            fileinfo['inode'] = inode;
            fileinfo['ipath'] = ipath + '-' + inode;
            fileinfo['dir'] = true;
            fileinfo['mtime'] = res['mtime'];
            fileinfo['name'] = res['name'];
            fileinfo['parent'] = pnode;
            fileinfo['path'] = res['path'];
            fileinfo['size'] = res['size'];
            webdriveModule.actionFor.push(inode.toString());
            webdriveModule.updateFilelist('mkdir', inode, pnode, fileinfo);
          }
          else
            showNotificationMsg('failed', res['opts']['msg']);
        } catch (e) {
          console.log(e); //If any runtime error
        }
      }
    }
    xmlhttp.send(formData);
  },
  createNew: function () {
    this.uploadDirClose();
    this.discardActionForCopy();
    this.prepareActionFor();
    this.setOpcode('mkdir');
    document.getElementById('context-menu-id').style.display = 'none';
    let newDir = document.createElement("div");
    let tdata = '<img src="images\\webdrive\\folder.png">';
    newDir.id = 'dnode-createNew';
    if (this.getDnodesLayout() != 'list') {
      tdata += '<textarea style="text-align: center;" id="dnode-name-createNew" class="rename-corner-input-style" value="New Folder" onclick="event.stopPropagation();" onkeydown="webdriveModule.pressEnter(event)">New Folder</textarea>';
      newDir.classList.add("diconStyle");
    }
    else {
      tdata += '<textarea rows="1" style="resize:none; text-align: left;" id="dnode-name-createNew" class="rename-corner-input-style" value="New Folder" onclick="event.stopPropagation();" onkeydown="webdriveModule.pressEnter(event)">New Folder</textarea>';
      newDir.classList.add("dlistStyle");
    }
    newDir.innerHTML = tdata;

    document.getElementById('webDriveDashboard').appendChild(newDir);
    let el = document.getElementById('dnode-name-createNew');
    el.focus();
    el.select();
  },
  cancelNew: function () {
    if (document.getElementById("dnode-createNew") != null)
      document.getElementById('webDriveDashboard').removeChild(document.getElementById("dnode-createNew"));
  },
  renderActionFor: function () {
    let files = webdriveModule.actionFor;
    if (files.length > 0) {
      let len = files.length;
      for (let i = 0; i < len; i++) {
        let el = document.getElementById('dnode-' + files[i]);
        if (el != null) el.classList.add('actionFor');
      }
      this.enableTopMenus();
    }
  },
  discardActionFor: function () {
    if (this.getActionFor().length > 0) {
      let list = document.getElementById('webDriveDashboard').getElementsByClassName('actionFor');
      let len = list.length;
      for (let i = len - 1; i >= 0; i--)
        list[i].classList.remove('actionFor');
      webdriveModule.actionFor = [];
      this.disableTopMenus();
    }
  },
  discardActionForCopy: function () {
    if (this.getActionForCopy().length > 0) {
      this.setActionForCopy([]);
      this.setActionForMoveFlag(false);
    }
    this.setCopyFromShare(false);
    this.pasteMenu.classList.remove('wdrive-group-menu-active');
  },
  prepareActionFor: function () {
    let rlist = document.getElementById('webDriveDashboard').getElementsByClassName('actionFor');
    let len = rlist.length;
    for (let i = len - 1; i >= 0; i--) {
      rlist[i].style.opacity = '1.0';
      rlist[i].style.filter = 'alpha(opacity=100)';
    }
  },
  enableActionForMove: function () {
    let rlist = document.getElementById('webDriveDashboard').getElementsByClassName('actionFor');
    let len = rlist.length;
    for (let i = len - 1; i >= 0; i--) {
      rlist[i].style.opacity = '0.7';
      rlist[i].style.filter = 'alpha(opacity=70)';
    }
  },
  displayContextMenu: function (item, e) {
    let sharedFlag = webdriveModule.getSharedflag();
    webdriveModule.discardRename('');
    let tdata = '', lft, top;
    let div = document.getElementById('context-menu-id');
    lft = div.style.left;
    top = div.style.top;
    let menuStyle = 'style="display:block;pointer-events: auto;"';
    if (sharedFlag) menuStyle = 'style="display:none;pointer-events: none;"';

    if (item.id == 'webDriveDashboard' || item.id == 'uploaderID') {
      tdata = '<span onclick="webdriveModule.cmActHandler(\'mkdir\')" ' + menuStyle + '>New Folder</span>';
      tdata = tdata + '<span onclick="webdriveModule.cmActHandler(\'refresh\')">Refresh</span>';
      if (webdriveModule.getActionForCopy().length > 0)
        tdata = tdata + '<span ' + menuStyle + ' onclick="webdriveModule.cmActHandler(\'paste\',' + webdriveModule.getDboardPWD() + ')">Paste</span>';
      else {
        webdriveModule.discardActionFor();
        //no need to show Paste menu when nothing was copied
        // tdata = tdata + '<span style="color:gray;pointer-events: none;">Paste</span>';
      }
      div.innerHTML = tdata;
    }
    else {
      let inode = parseInt(item.id.split('-')[1]);
      if (webdriveModule.actionFor.indexOf(inode) == -1) {
        webdriveModule.discardActionFor();
        webdriveModule.actionFor.push(inode);
        item.classList.add('actionFor');
      }
      let dboardpwd = webdriveModule.getDboardPWD();
      if (webdriveModule.actionFor.length > 1) {
        tdata = '';
        tdata = tdata + '<span onclick="webdriveModule.cmActHandler(\'copy\',' + dboardpwd + ')">Copy</span>\
        <span onclick="webdriveModule.cmActHandler(\'download\','+ dboardpwd + ')">Download</span>';
        tdata = tdata + '<span ' + menuStyle + '  onclick="webdriveModule.cmActHandler(\'move\',' + dboardpwd + ')">Move</span>';
        if (webdriveModule.getShareaccess() == true)
          tdata = tdata + '<span ' + menuStyle + '  onclick="webdriveModule.cmActHandler(\'share\')">Share</span>';
        tdata = tdata + '<span ' + menuStyle + '  onclick="webdriveModule.cmActHandler(\'delete\')">Delete</span>\
        <span '+ menuStyle + '  onclick="webdriveModule.cmActHandler(\'compress\',' + dboardpwd + ')">Compress</span>';
        div.innerHTML = tdata;
      }
      else {
        let file;
        if (sharedFlag)
          file = webdriveModule.getShareInfo(inode);
        else
          file = webdriveModule.getFileInfo(inode);
        tdata = '';

        if (file['dir']) {
          tdata = tdata + '<span ' + menuStyle + ' onclick="webdriveModule.cmActHandler(\'open\',' + inode + ')">Open</span>\
          <span '+ menuStyle + ' onclick="webdriveModule.cmActHandler(\'rename\',' + inode + ')">Rename</span>\
          <span onclick="webdriveModule.cmActHandler(\'copy\','+ dboardpwd + ')">Copy</span>\
          <span onclick="webdriveModule.cmActHandler(\'download\','+ dboardpwd + ')">Download</span>';
          tdata = tdata + '<span ' + menuStyle + ' onclick="webdriveModule.cmActHandler(\'move\',' + dboardpwd + ')">Move</span>';
          if (webdriveModule.getShareaccess() == true)
            tdata = tdata + '<span  ' + menuStyle + ' onclick="webdriveModule.cmActHandler(\'share\')">Share</span>';
          tdata = tdata + '<span ' + menuStyle + ' onclick="webdriveModule.cmActHandler(\'delete\')">Delete</span>';
          if (webdriveModule.getActionForCopy().length > 0)
            tdata = tdata + '<span ' + menuStyle + ' onclick="webdriveModule.cmActHandler(\'paste\',' + inode + ')">Paste</span>';
          else {
            // no need when menu is disabled
            // tdata = tdata + '<span style="color:gray;pointer-events:none">Paste</span>';
          }
          tdata = tdata + '<span ' + menuStyle + ' onclick="webdriveModule.cmActHandler(\'compress\',' + inode + ')">Compress</span>';
        }
        else if (file['ext'] == 'shared') {
          tdata = tdata + '<span ' + menuStyle + ' onclick="webdriveModule.driveShareReload(' + inode + ');">Open</span>\
          <span '+ menuStyle + ' onclick="event.stopPropagation();webdriveModule.removesharelink(' + inode + ')">Remove</span>';
        }
        else {
          if (file['ext'] == 'pdf')
            tdata = tdata + '<span ' + menuStyle + ' onclick="webdriveModule.openPdf(' + inode + ');">Preview</span>';
          else if (file['ext'] == 'png' || file['ext'] == 'jpeg' || file['ext'] == 'gif' || file['ext'] == 'jpg')
            tdata = tdata + '<span ' + menuStyle + ' onclick="webdriveModule.openImg(' + inode + ');">Preview</span>';

          tdata = tdata + '<span ' + menuStyle + ' onclick="webdriveModule.cmActHandler(\'rename\',' + inode + ')">Rename</span>\
          <span onclick="webdriveModule.cmActHandler(\'copy\','+ dboardpwd + ')">Copy</span>\
          <span onclick="webdriveModule.cmActHandler(\'download\','+ dboardpwd + ')">Download</span>';
          tdata = tdata + '<span ' + menuStyle + ' onclick="webdriveModule.cmActHandler(\'move\',' + dboardpwd + ')">Move</span>';
          if (webdriveModule.getShareaccess() == true)
            tdata = tdata + '<span  ' + menuStyle + ' onclick="webdriveModule.cmActHandler(\'share\')">Share</span>';
          tdata = tdata + '<span ' + menuStyle + ' onclick="webdriveModule.cmActHandler(\'delete\')">Delete</span>';
          if (file['ext'] == 'zip' || file['ext'] == 'rar')
            tdata = tdata + '<span ' + menuStyle + ' onclick="webdriveModule.cmActHandler(\'extract\',' + inode + ')">Extract</span>';
          else {
            //no need to show extract menu when clicked on normal file except compress
            // tdata = tdata + '<span style="color:gray;pointer-events:none">Extract</span>';
          }
        }
        div.innerHTML = tdata;
      }
    }
    div.style.display = 'flex';
    let parentDiv = document.getElementById("uploaderID");
    let dboard = document.getElementById('webDriveDashboard');
    lft = e.clientX - dboard.offsetLeft + 10;
    top = e.clientY - (dboard.offsetTop + 40);
    if (parentDiv.clientHeight < top + div.clientHeight) {
      top = (top - div.clientHeight - 20);
      lft = lft - 10;
    }
    if (parentDiv.clientWidth < lft + div.clientWidth) {
      top = top - 10;
      lft = (lft - div.clientWidth - 20);
    }
    div.style.left = lft + "px";
    div.style.top = top + "px";
  },
  discardRename: function (inode) {
    let pnode = this.getPreRenameID();
    if (pnode != '' && pnode != 'undefined' && pnode != null) {
      let el = document.getElementById('dnode-name-' + pnode);
      if (this.getDnodesLayout() != 'list')
        el.outerHTML = '<span id="' + el.id + '">' + this.getFileInfo(pnode)['name'] + '</span>';
      else
        el.outerHTML = '<span class="dlistNameStyle" id="' + el.id + '">' + this.getFileInfo(pnode)['name'] + '</span>';
      this.setPreRenameID(inode);
    }
    else this.setPreRenameID('');
  },
  renameFile: function (e, el, inode, pname, pnode) {
    let code = (e.keyCode ? e.keyCode : e.which);
    if (code == 13 && !e.shiftKey) {
      chk_session();
      e.preventDefault();
      let path = this.getFileInfo(pnode)['path'];
      let filename = el.value;
      if (filename == pname || filename == '' || /[^a-zA-Z0-9_ \,\-\_\.\[\]\(\)]/.test(filename)) {
        if (filename.length > 13) filename = filename.substr(10, 13) + "...";
        el.outerHTML = '<span id="' + el.id + '">' + pname + '</span>';
      }
      else {
        if (window.XMLHttpRequest)
          xmlhttp = new XMLHttpRequest();
        else
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        var url = "/modules/webdrive/drive_rename_file.php";
        xmlhttp.open('POST', url, true);
        var formData = new FormData();
        formData.append('oldname', pname);
        formData.append('newname', filename);
        formData.append('path', path);
        formData.append('inode', inode);
        formData.append('auth_ph', auth_ph);
        formData.append('ph', ph);
        xmlhttp.onreadystatechange = function () {
          if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            try {
              var res = JSON.parse(xmlhttp.responseText);
              if (res['opts']['status'] == true) {
                webdriveModule.updateFilelist('rename', inode, pnode, filename);
              }
              else
                showNotificationMsg('failed', res['opts']['msg']);
            } catch (e) {
              console.log(e); //If any runtime error
            }
          }
        }
        xmlhttp.send(formData);
      }
    }
    else increaseHeight(el);
  },
  discardShare: function () {
    if (document.getElementById('shareWithID') != null) {
      let el = document.getElementById('shareWithID');
      el.style.bottom = '-100px';
      el.style.visibility = 'hidden';
      el.style.opacity = '0';
      el.style.filter = 'alpha(opacity=0)';
      if(document.getElementById('wdrive_share_with_input')!=null)
        document.getElementById('wdrive_share_with_input').value = "";
      this.prepareUserPopups();
    }
  },
  menuActHandler: function (option) {
    let inode = this.getDboardPWD();
    if (option == 'rename' || option == 'extract') inode = this.getActionFor()[0];
    this.cmActHandler(option, inode);
  },
  cmActHandler: function (option, inode) {
    this.setOpcode(option);
    let alertID;
    document.getElementById('context-menu-id').style.display = 'none';
    if (option == 'share') {

      this.cancelNew();
      this.uploadDirClose();
      let wdmc = document.getElementById("shareWithID");
      if (document.getElementById('shareWithID') != null) {
        this.prepareFilesToShare();
        //   this.prepareFilesToShare();
        //   let el = document.getElementById('shareWithID');
        //   el.style.left = document.getElementById("webDriveDashboard").clientWidth / 2 - 335;
        //   //Because of initial location of share div
        //   // el.style.top = - document.getElementById("webDriveDashboard").clientHeight / 2 - 20;
        //   el.style.visibility = 'visible';
        //   el.style.opacity = '1.0';
        //   el.style.filter = 'alpha(opacity=100)';
        //   let titleID = document.getElementById('wd-user-list-move');
        //   titleID.addEventListener('mousedown', function (e) {
        //     webdriveModule.isDown = true;
        //     webdriveModule.offset = [
        //       el.offsetLeft - e.clientX,
        //       el.offsetTop - e.clientY
        //     ];
        //   }, true);
        //   webdriveModule.targetDiv = el;
        wdmc.setAttribute('style', '');
        wdmc.classList.add('modal-body-content');
        displaySuperModal('shareWithID');
      }
    }
    else {
      this.discardShare();
      if (option == 'paste') {
        chk_session();
        let len = this.actionForCopy.length;
        this.pasteMenu.classList.remove('wdrive-group-menu-active');
        if (len > 0) {
          let remarks = '';
          let pwd = [];
          var url = "/modules/webdrive/drive_move_files.php";
          let files = this.getActionForCopy();
          if (this.getCopyFromShare() == true) {
            url = "/modules/webdrive/drive_move_shared_files.php";
            remarks = this.getSharebase(this.getSnode());
            for (let i = 0; i < len; i++)
              pwd.push(this.getShareInfo(files[i])['realpath']);
          }
          else
            pwd = this.getFileInfo(this.getCopiedFrom())['path'];
          let dest = this.getFileInfo(inode)['path'];
          let operation = this.getMoveStatus();
          if (window.XMLHttpRequest)
            xmlhttp = new XMLHttpRequest();
          else
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
          alertID = createTlUnit(xmlhttp, this.getOpcode(), 'Copy/Moving... ' + len + ' file(s)');

          xmlhttp.open('POST', url, true);
          var formData = new FormData();
          formData.append('filenames', files);
          formData.append('pwd', pwd);
          formData.append('dest', dest);
          formData.append('operation', operation);
          formData.append('remarks', remarks);
          formData.append('auth_ph', auth_ph);
          formData.append('ph', ph);
          xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
              try {
                var res = JSON.parse(xmlhttp.responseText);
                if (res['opts']['status'] == true) {
                  webdriveModule.driveReload();
                }
                webdriveModule.updateActionStatus(alertID, res['opts']['status'], res['opts']['msg']);
              } catch (e) {
                console.log(e); //If any runtime error
              }
            }
          }
          xmlhttp.send(formData);
        }
        webdriveModule.discardActionForCopy();
        webdriveModule.resetOpcode();
      }
      else {
        if (option == 'mkdir') {
          if (document.getElementById("dnode-createNew") != null) {
            chk_session();
            webdriveModule.createFolder();
          }
          webdriveModule.createNew();
        }
        else {
          this.discardActionForCopy();
          this.prepareActionFor();
          this.cancelNew();
          this.uploadDirClose();
          if (option == 'open') {
            this.renderWebDrive(inode, this.getSharedflag());
          }
          else if (option == 'refresh') {
            webdriveModule.driveReload();
          }
          else if (option == 'rename') {
            this.discardRename(inode);
            pnode = this.getFileInfo(inode)['parent'];
            let name = this.getFileInfo(inode)['name'];
            let style, el = document.getElementById('dnode-name-' + inode);
            let ht = el.clientHeight - 2, wt = el.clientWidth - 2;
            if (this.getDnodesLayout() == 'list') style = 'text-align: left;';
            else style = 'text-align: center;';
            el.outerHTML = '<textarea style="' + style + '" id="dnode-name-' + inode + '" class="rename-corner-input-style" value="' + name + '" onclick="event.stopPropagation();" onkeydown="webdriveModule.renameFile(event, this, ' + inode + ',\'' + name + '\',\'' + pnode + '\')">' + name + '</textarea>';
            el = document.getElementById('dnode-name-' + inode);
            el.style.height = ht; el.style.width = wt;
            el.focus();
            el.select();
            this.setPreRenameID(inode);
          }
          else if (option == 'copy') {
            this.setMoveStatus(option);
            this.setCopiedFrom(inode);
            this.setActionForMoveFlag(false);
            this.setActionForCopy(this.getActionFor());
            if (this.getSharedflag() == true)
              this.setCopyFromShare(true);
            else {
              this.setCopyFromShare(false);
              this.pasteMenu.classList.add('wdrive-group-menu-active');
            }

          }
          else if (option == 'move') {
            this.setMoveStatus(option);
            this.setCopiedFrom(inode);
            this.setActionForMoveFlag(true);
            this.setActionForCopy(this.getActionFor());
            this.enableActionForMove();
            this.pasteMenu.classList.add('wdrive-group-menu-active');
          }
          else if (option == 'download') {
            chk_session();
            let remarks = '';
            let len = this.getActionFor().length;
            if (len > 0) {
              let pwd = [];
              let files = this.getActionFor();
              var url = "/modules/webdrive/drive_download_files.php";
              if (this.getSharedflag() == true) {
                var url = "/modules/webdrive/drive_download_shared_files.php";
                remarks = this.getSharebase(this.getSnode());
                for (let i = 0; i < len; i++)
                  pwd.push(this.getShareInfo(files[i])['realpath']);
              }
              else
                pwd = this.getFileInfo(this.getDboardPWD())['path'];

              if (window.XMLHttpRequest)
                xmlhttp = new XMLHttpRequest();
              else
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

              alertID = createTlUnit(xmlhttp, this.getOpcode(), 'Downloading... ' + len + ' file(s)');
              xmlhttp.onloadstart = function (ev) {
                xmlhttp.responseType = "blob";
              }
              xmlhttp.open('POST', url, true);
              // xmlhttp.addHeader("Access-Control-Expose-Headers", "Content-Disposition");
              var formData = new FormData();
              formData.append('filenames', files);
              formData.append('remarks', remarks);
              formData.append('pwd', pwd);
              formData.append('auth_ph', auth_ph);
              formData.append('ph', ph);

              xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  if (xmlhttp.getResponseHeader('Content-Length') != 0) {
                    var filename = xmlhttp.getResponseHeader('Content-Disposition').split("filename=\"")[1];
                    // var filename = "download_files";
                    // var disposition = xmlhttp.getResponseHeader('Content-Disposition');
                    // if (disposition && disposition.indexOf('inline') !== -1) {
                    //   var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    //   var matches = filenameRegex.exec(disposition);
                    //   if (matches != null && matches[1]) {
                    //     filename = matches[1].replace(/['"]/g, '');
                    //   }
                    // }

                    if (IE10orBelow() == '8' || IE10orBelow() == '9') {
                      var res = xmlhttp.responseText;
                      var w = window.open();
                      var doc = w.document;
                      doc.charset = "utf-8";
                      doc.write("<iframe width='100%' height='100%' src='base64, " + btoa(res) + "'></iframe>");
                      doc.execCommand("SaveAs", null, filename);
                    }
                    else {
                      var res = xmlhttp.response;
                      if (window.navigator && window.navigator.msSaveBlob) {
                        window.navigator.msSaveBlob(res, filename);
                      }
                      else {
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(res);
                        console.log(filename);
                        link.download = filename;
                        document.body.appendChild(link);
                        link.click();
                        setTimeout(function () {
                          document.body.removeChild(link);
                          window.URL.revokeObjectURL(link.href);
                        }, 1);
                      }
                    }
                    webdriveModule.updateActionStatus(alertID, true, 'File Downloaded, please wait for SAVE/OPEN alert.');
                  }
                  else
                    webdriveModule.updateActionStatus(alertID, false, 'Download Operation has been Failed. Please Contact with site Administrator..');
                  webdriveModule.resetOpcode();
                }
              }
              xmlhttp.send(formData);
            }
            else
              showNotificationMsg('alert', 'Pleaes select file(s) before download.');
          }
          else if (option == 'compress') {
            chk_session();
            let len = this.getActionFor().length;
            if (len > 0) {
              let pwd = this.getFileInfo(this.getDboardPWD())['path'];
              let files = this.getActionFor();
              if (window.XMLHttpRequest)
                xmlhttp = new XMLHttpRequest();
              else
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
              alertID = createTlUnit(xmlhttp, this.getOpcode(), 'Compressing... ' + len + ' file(s)');

              var url = "/modules/webdrive/drive_compress_files.php";
              xmlhttp.open('POST', url, true);
              var formData = new FormData();
              formData.append('filenames', files);
              formData.append('pwd', pwd);
              formData.append('auth_ph', auth_ph);
              formData.append('ph', ph);
              xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  try {
                    var res = JSON.parse(xmlhttp.responseText);
                    if (res['opts']['status'] == true) {
                      webdriveModule.driveReload();
                      webdriveModule.discardActionFor();
                    }
                    webdriveModule.updateActionStatus(alertID, res['opts']['status'], res['opts']['msg']);
                    webdriveModule.resetOpcode();
                  } catch (e) {
                    console.log(e); //If any runtime error
                  }
                }
              }
              xmlhttp.send(formData);
            }
          }
          else if (option == 'extract') {
            chk_session();
            let len = this.getActionFor().length;
            if (len == 1) {
              let pwd = this.getFileInfo(this.getDboardPWD())['path'];
              let file = this.getFileInfo(inode)['path'];
              if (window.XMLHttpRequest)
                xmlhttp = new XMLHttpRequest();
              else
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
              alertID = createTlUnit(xmlhttp, this.getOpcode(), 'Extracting... ');
              var url = "/modules/webdrive/drive_extract_file.php";
              xmlhttp.open('POST', url, true);
              var formData = new FormData();
              formData.append('filename', file);
              formData.append('pwd', pwd);
              formData.append('auth_ph', auth_ph);
              formData.append('ph', ph);
              xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  try {
                    var res = JSON.parse(xmlhttp.responseText);
                    if (res['opts']['status'] == true) {
                      webdriveModule.driveReload();
                      webdriveModule.discardActionFor();
                    }
                    webdriveModule.updateActionStatus(alertID, res['opts']['status'], res['opts']['msg']);
                    webdriveModule.resetOpcode();
                  } catch (e) {
                    console.log(e); //If any runtime error
                  }
                }
              }
              xmlhttp.send(formData);
            }
          }
          else if (option == 'delete') {
            let proceedToDelete = true;
            let files = this.getActionFor();
            let sharedFiles = this.getSharedfiles();
            for (let i = 0; i < sharedFiles.length; i++) {
              if (files.indexOf(sharedFiles[i]) != -1)
                proceedToDelete = false;
            }
            if (proceedToDelete == true) {
              chk_session();
              if (confirm('Are you sure to delete?')) {
                let pwd = this.getFileInfo(this.getDboardPWD())['path'];
                if (window.XMLHttpRequest)
                  xmlhttp = new XMLHttpRequest();
                else
                  xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                alertID = createTlUnit(xmlhttp, this.getOpcode(), 'Deleting... file(s)');
                var url = "/modules/webdrive/drive_delete_files.php";
                xmlhttp.open('POST', url, true);
                var formData = new FormData();
                formData.append('filenames', files);
                formData.append('pwd', pwd);
                formData.append('auth_ph', auth_ph);
                formData.append('ph', ph);
                xmlhttp.onreadystatechange = function () {
                  if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    try {
                      var res = JSON.parse(xmlhttp.responseText);
                      if (res['opts']['status'] == true) {
                        webdriveModule.discardActionFor();
                        webdriveModule.driveReload();
                      }
                      webdriveModule.updateActionStatus(alertID, res['opts']['status'], res['opts']['msg']);
                      webdriveModule.resetOpcode();
                    } catch (e) {
                      console.log(e); //If any runtime error
                    }
                  }
                }
                xmlhttp.send(formData);
              }
            }
            else {
              showNotificationMsg('alert', 'The file (s) could not be deleted due to shared by others')
            }

          }
        }
      }
    }
  },
  disableTopMenus: function () {
    this.commonMenu.classList.remove('wdrive-group-menu-active');
    this.cautionMenu.classList.remove('wdrive-group-menu-active');
    this.singleFDMenu.classList.remove('wdrive-group-menu-active');
    this.compressMenu.classList.remove('wdrive-group-menu-active');
    this.extractMenu.classList.remove('wdrive-group-menu-active');
    if (this.getActionForCopy().length > 0 && this.getSharedflag() == false)
      this.pasteMenu.classList.add('wdrive-group-menu-active');
    else
      this.pasteMenu.classList.remove('wdrive-group-menu-active');
  },
  enableTopMenus: function () {
    let totalSelected = this.getActionFor().length;
    if (totalSelected >= 1) {
      this.commonMenu.classList.add('wdrive-group-menu-active');
      if (this.getSharedflag() == false) {
        this.cautionMenu.classList.add('wdrive-group-menu-active');
        if (totalSelected == 1) {
          this.singleFDMenu.classList.add('wdrive-group-menu-active');
          let file = this.getFileInfo(this.getActionFor());
          if (file['dir'] == true)
            this.compressMenu.classList.add('wdrive-group-menu-active');
          else {
            if (file['ext'] == 'zip' || file['ext'] == 'rar') {
              this.extractMenu.classList.add('wdrive-group-menu-active');
            }
          }
        }
        else
          this.compressMenu.classList.add('wdrive-group-menu-active');
      }
    }
  },
  selectFileFor: function (item, e) {
    e.stopPropagation();
    this.cancelNew();
    this.uploadDirClose();
    this.discardRename('');
    if (this.getActionForCopy().length > 0 && this.getActionForMoveFlag() == true) {
      this.discardActionFor();
      this.setActionForMoveFlag(false);
      this.prepareActionFor();
    }
    document.getElementById('context-menu-id').style.display = 'none';
    if (e.ctrlKey == false && e.shiftKey == false) {
      this.discardActionFor();
    }
    let inode = parseInt(item.id.split('-')[1]);
    let index = this.actionFor.indexOf(inode);
    if (index == -1) {
      this.actionFor.push(inode);
      item.classList.add('actionFor');
    }
    else {
      this.actionFor.splice(index, 1);
      item.classList.remove('actionFor');
    }
    this.disableTopMenus();
    this.enableTopMenus();
    if (this.getOpcode() == 'share') {
      if (this.getActionFor().length == 0) {
        this.discardShare();
        this.resetOpcode();
      }
      else this.prepareFilesToShare();
    }
  },
  backDir: function () {
    let pwd = this.getRnode();
    if (this.backStack.length > 1) {
      pwd = this.backStack.pop();
      let value = pwd.split('-');
      if (value[0] == 's') this.setSharedflag(true);
      else this.setSharedflag(false);
      pwd = value[1];
      if (this.getDboardPWD() == pwd) {
        pwd = this.backStack.pop();
        value = pwd.split('-');
        if (value[0] == 's') this.setSharedflag(true);
        else this.setSharedflag(false);
        pwd = value[1];
        if (this.backStack.length == 0) {
          this.backStack.push('r-' + pwd);
          document.getElementById('wdrive-back-img-id').style.color = '';
          document.getElementById('wdrive-back-btn-id').style.pointerEvents = 'none';
        }
      }
    }
    else {
      document.getElementById('wdrive-back-img-id').style.color = '';
      document.getElementById('wdrive-back-btn-id').style.pointerEvents = 'none';
    }
    this.renderDashboard(pwd, this.getSharedflag());
    this.discardActionFor();
    // this.resetTnode(pwd); thinking....
    if (pwd == this.getRnode()) {
      document.getElementById('wdrive-up-img-id').src = 'images\\webdrive\\upinactive.png';
      document.getElementById('wdrive-up-btn-id').style.pointerEvents = 'none';
    }
    else {
      document.getElementById('wdrive-up-img-id').src = 'images\\webdrive\\upactive.png';
      document.getElementById('wdrive-up-btn-id').style.pointerEvents = 'auto';
    }
  },
  pushDir: function (inode, sharedFlag) {
    if (webdriveModule.backStack[this.backStack.length - 1] != inode) {
      this.discardActionFor();
      if (sharedFlag)
        webdriveModule.backStack.push('s-' + inode);
      else
        webdriveModule.backStack.push('r-' + inode);

      document.getElementById('wdrive-back-img-id').src = 'images\\webdrive\\backactive.png';
      document.getElementById('wdrive-back-btn-id').style.pointerEvents = 'auto';
    }
    if (inode == this.getRnode()) {
      document.getElementById('wdrive-up-img-id').src = 'images\\webdrive\\upinactive.png';
      document.getElementById('wdrive-up-btn-id').style.pointerEvents = 'none';
    }
    else {
      document.getElementById('wdrive-up-img-id').src = 'images\\webdrive\\upactive.png';
      document.getElementById('wdrive-up-btn-id').style.pointerEvents = 'auto';
    }
  },
  upDir: function () {
    let pwd = this.getDboardPWD();
    let rnode = this.getRnode();
    let sharedFlag = this.getSharedflag();
    if (pwd != rnode) {
      if (sharedFlag) {
        if (pwd == this.getSnode()) { sharedFlag = !sharedFlag; pwd = rnode; this.renderShareInbox(); }
        else pwd = this.getShareInfo(pwd)['parent'];
      }
      else pwd = this.getFileInfo(pwd)['parent'];
      if (pwd == rnode) {
        document.getElementById('wdrive-up-img-id').src = 'images\\webdrive\\upinactive.png';
        document.getElementById('wdrive-up-btn-id').style.pointerEvents = 'none';
      }
      this.renderWebDrive(pwd, sharedFlag);
    }
    else {
      document.getElementById('wdrive-up-img-id').src = 'images\\webdrive\\upinactive.png';
      document.getElementById('wdrive-up-btn-id').style.pointerEvents = 'none';
    }
  },
  uploadDirClose: function () {
    this.updateActionMenuList('');
  },
  fileUpload: function () {
    var fileUploadElement = document.getElementById('web-drive-file-upload-id');
    if (typeof fileUploadElement === "undefined" || fileUploadElement.value === "") {
      showNotificationMsg('alert', 'File can\'t be uploaded. Contact with site Administrator..');
    }
    else {
      this.setOpcode("upload");
      this.now = 0;
      this.queue = [];
      this.start(fileUploadElement.files);
    }
  },
  shareReq: function (genid) {
    let alertID;
    chk_session();
    let pnode = this.getDboardPWD();
    let files = [], pwd = this.getFileInfo(pnode)['path'];
    let len = this.getActionFor().length;
    for (let i = 0; i < len; i++)
      files[i] = this.getFileInfo(this.actionFor[i])['path'];
    if (window.XMLHttpRequest)
      xmlhttp = new XMLHttpRequest();
    else
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    alertID = createTlUnit(xmlhttp, 'share', 'Sharing file');

    var url = "/modules/webdrive/drive_share_files.php";
    xmlhttp.open('POST', url, true);
    var formData = new FormData();
    formData.append('filenames', files);
    formData.append('genid', genid);
    formData.append('pwd', pwd);
    formData.append('auth_ph', auth_ph);
    formData.append('ph', ph);
    xmlhttp.onreadystatechange = function () {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        try {
          var files, inode, res = JSON.parse(xmlhttp.responseText);
          if (res['opts']['status'] == true) {
            files = res['sharesucceed'];
            for (let i = 0; i < files.length; i++) {
              inode = files[i];
              if (webdriveModule.sharedfiles.indexOf(inode) == -1) {
                webdriveModule.sharedfiles.push(inode);
                webdriveModule.sharedbyme[inode] = [];
              }
              webdriveModule.sharedbyme[inode].push(genid);
            }
            document.getElementById('wdrive_share_with_input').value = "";
            webdriveModule.setMysharesize(webdriveModule.sizeInMegaBytes(res['mysharesize'], false));
            document.getElementById('wdrive-myshare-size-id').innerHTML = webdriveModule.getMysharesize();
            webdriveModule.prepareFilesToShare();
          }
          webdriveModule.updateActionStatus(alertID, res['opts']['status'], res['opts']['msg']);
        } catch (e) {
          console.log(e); //If any runtime error
        }
      }
    }
    xmlhttp.send(formData);
  },
  prepareFilesToShare: function () {
    let inboxes = document.getElementsByClassName("fcoder-inbox");
    for (let i = 0; i < inboxes.length; i++) {
      inboxes[i].innerHTML = "";
    }
    let div = document.getElementById('existing-sharewith-id');
    let sharedwith = [], files = this.getActionFor(), genid, file, filename, sfilename, partlen;
    var el, tdata = '', ext;
    let sinboxid;
    for (let i = 0; i < files.length; i++) {
      sharedwith = [];
      file = files[i];
      el = 'dnode-' + file;
      filename = this.getFileInfo(file)['name'];
      if (this.getFileInfo(file)['dir'] == true)
        ext = '<img src="images\\webdrive\\myshrfolder.png">';
      else
        ext = '<img src="images\\webdrive\\myshrfile.png">';
      sfilename = filename;
      if (filename.length > 20) {
        partlen = Math.min(20, filename.lastIndexOf(" "));
        sfilename = filename.substr(0, partlen == -1 ? 20 : partlen) + "...";
      }

      sharedwith = this.getSharedbyme(file);
      tdata = tdata + '<ul>';
      if (sharedwith != null && sharedwith.length > 0) {
        tdata = tdata + '<li><span title="' + filename + '"><span>' + (i + 1) + '. </span>' + sfilename + '</span><img onclick="webdriveModule.removesharewith(' + file + ',\'\')" title="Remove all user shares." style="height:1.2em;vertical-align:middle;margin-left:5px" src="images\\webdrive\\delete.png"></li>';
        for (let j = 0; j < sharedwith.length; j++) {
          genid = sharedwith[j];
          sinboxid = "fcoder-inbox-" + genid;
          if (this.getUser(genid) != null)
            tdata = tdata + '<li class="activeItemStyle" onclick="webdriveModule.removesharewith(' + file + ',\'' + genid + '\')"><span style="margin-right:5px">' + this.getUser(genid)['name'] + '</span><span style="color:red;font-width:bold">[-]</span></li>';
          else
            tdata = tdata + '<li class="activeItemStyle" onclick="webdriveModule.removesharewith(' + file + ',\'' + genid + '\')"><span style="margin-right:5px"> User at other Office </span><span style="color:red;font-width:bold">[-]</span></li>';
          document.getElementById(sinboxid).innerHTML = document.getElementById(sinboxid).innerHTML + '<li class="activeItemStyle" onclick="webdriveModule.removesharewith(' + file + ',\'' + genid + '\')">' + ext + '<span style="margin-right:5px" title="' + filename + '">' + sfilename + '</span><span style="color:red;font-width:bold">[-]</span></li>';

        }
      }
      else {
        tdata = tdata + '<li><span title="' + filename + '"><span>' + (i + 1) + '. </span>' + sfilename + '</span></li>';
      }
      tdata = tdata + '</ul>';
    }
    div.innerHTML = tdata;
  },
  removesharewith: function (inode, sharecancelwith) {
    let index, file = this.getFileInfo(inode)['path'];
    if (inode != '' && inode != null) {
      chk_session();
      if (window.XMLHttpRequest)
        xmlhttp = new XMLHttpRequest();
      else
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      var url = "/modules/webdrive/drive_share_remove.php";
      xmlhttp.open('POST', url, true);
      var formData = new FormData();
      formData.append('file', file);
      formData.append('sharecancelwith', sharecancelwith);
      formData.append('auth_ph', auth_ph);
      formData.append('ph', ph);
      xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          try {
            var res = JSON.parse(xmlhttp.responseText);
            if (res['opts']['status'] == true) {
              if (sharecancelwith != '' && sharecancelwith != null && res['sharecount'] != 0) {
                index = webdriveModule.getSharedbyme(inode).indexOf(sharecancelwith);
                webdriveModule.sharedbyme[inode].splice(index, 1);
              }
              else {
                delete webdriveModule.sharedbyme[inode];
                index = webdriveModule.sharedfiles.indexOf(inode);
                webdriveModule.sharedfiles.splice(index, 1);
                webdriveModule.setMysharesize(webdriveModule.sizeInMegaBytes(res['mysharesize'], false));
                document.getElementById('wdrive-myshare-size-id').innerHTML = webdriveModule.getMysharesize();
              }
              webdriveModule.prepareFilesToShare();
            }
            else
              showNotificationMsg('failed', res['opts']['msg']);
          } catch (e) {
            console.log(e); //If any runtime error
          }
        }
      }
      xmlhttp.send(formData);
    }
  },

  removesharelink: function (snode) {
    chk_session();
    if (window.XMLHttpRequest)
      xmlhttp = new XMLHttpRequest();
    else
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    var url = "/modules/webdrive/drive_share_cancel.php";
    xmlhttp.open('POST', url, true);
    var formData = new FormData();
    formData.append('snode', snode);
    formData.append('auth_ph', auth_ph);
    formData.append('ph', ph);
    xmlhttp.onreadystatechange = function () {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        try {
          var res = JSON.parse(xmlhttp.responseText);
          if (res['opts']['status'] == true) {
            //   let snode = res['snode'];
            //   webdriveModule.setSnode(snode);
            //   webdriveModule.setSharedflag(true);
            //   webdriveModule.renderShareInbox();
            //   res['filelist'][snode]['parent'] = webdriveModule.getRnode();
            //   webdriveModule.setSharelist(res['filelist']);
            //   webdriveModule.setSharelinks(res['filelink']);
            //   document.getElementById('context-menu-id').style.display = 'none';
            //   webdriveModule.resetOpcode();
            //   webdriveModule.refreshBackStack();
            webdriveModule.renderWebDrive(snode, true);
          }
          else
            showNotificationMsg('failed', res['opts']['msg']);
          // webdriveModule.toggleShareInbox();
        } catch (e) {
          console.log(e); //If any runtime error
        }
      }
    }
    xmlhttp.send(formData);
  },
  pressEnter: function (e, inode) {
    e.stopPropagation();
    let option = this.getOpcode();
    let code = (e.keyCode ? e.keyCode : e.which);
    if (option == 'mkdir') {
      if (code == 13 && !e.shiftKey) {
        chk_session();
        webdriveModule.createFolder();
        e.preventDefault();
        return false;
      }
    }
    else if (option == 'opendir') {
      webdriveModule.renderWebDrive(inode, this.getSharedflag());
    }
    else if (option == 'share') {
      if (code == 13 && !e.shiftKey) {
        

      }
    }
  },
  queue: [], // upload queue
  now: 0, // current file being uploaded
  start: function (files) {
    if (webdriveModule.queue.length == 0) {
      let startupload = false;
      // VISUAL - DISABLE UPLOAD UNTIL DONE
      let name, size, count = 0, len = files.length;
      for (let i = 0; i < len; i++) {
        name = files[i].name;
        size = files[i].size;
        if (this.duplicateUpload(name) == true) {
          startupload = true;
          this.queue.push(files[i]);
          name = name.length > 20 ? name.substr(0, 18) + '...' : name;
          count++;
        }
      }
      document.getElementById('uploaderID').classList.add('disabled');
      // PROCESS UPLOAD - ONE BY ONE
      if (startupload == true) {
        webdriveModule.run();
      }
    }
  },

  run: function () {
    chk_session();
    var xhr = "", name = this.queue[this.now].name, cuid;
    if (window.XMLHttpRequest)
      xhr = new XMLHttpRequest();
    else
      xhr = new ActiveXObject("Microsoft.XMLHTTP");
    let id = createTlUnit(xhr, this.getOpcode(), 'Uploading...');
    let filepath = webdriveModule.getFileInfo(webdriveModule.getDboardPWD())['path'];
    xhr.timeout = 300000;
    xhr.ontimeout = function (e) {
      showNotificationMsg('alert', "File Upload Timed-out. Kindly try to upload this file again.");
    };
    let url = "/modules/webdrive/drive_upload_file.php";
    xhr.open('POST', url, true);
    var formData = new FormData();
    formData.append('filepath', filepath);
    formData.append('filename', this.queue[this.now].name);
    formData.append('filesize', this.queue[this.now].size);
    formData.append('auth_ph', auth_ph);
    formData.append('ph', ph);
    xhr.setRequestHeader("X_FILENAME", name);
    xhr.send(formData);
    xhr.onerror = function (error) {
      webdriveModule.updateActionStatus(id, false, 'Failed');
    };
    xhr.onload = function (e) {
      if (this.readyState == 4 && this.status == 200) {
        try {
          var res = JSON.parse(this.responseText);
          if (res['opts']['status'] == true) {
            cuid = res['chunk_upload_id'];
            webdriveModule.chunk_upload_queue[cuid] = 1;
            webdriveModule.uploadFile(id, filepath, cuid);
          }
          else {
            showNotificationMsg('alert', res['opts']['msg'])
          }
        } catch (e) {
          console.log(e); //If any runtime error
        }
      }
    };
  },
  uploadFile: function (id, filepath, cuid) {
    var blob = this.queue[this.now];
    let bpc = this.getBytesPerChunk();
    const SIZE = blob.size;
    var start = 0;
    var count = 0;
    var end = bpc;
    var totalChunks = parseInt(SIZE / bpc);
    if (SIZE % bpc != 0)
      totalChunks = totalChunks + 1;
    while (start < SIZE) {
      webdriveModule.chunk_upload_queue[cuid] = 1;
      this.chunkUpload(id, blob.slice(start, end), blob.name, count, filepath, totalChunks, cuid);
      start = end;
      end = start + bpc;
      count = count + 1;
    }
  },
  chunkUpload: function (id, chunk, filename, part, filepath, totalChunks, cuid) {
    var action = this.getOpcode();
    var fd = new FormData();
    let url = "/modules/webdrive/drive_upload_chunk.php";
    fd.append("fileToUpload", chunk);
    fd.append("seq", part);
    fd.append("filename", filename);
    fd.append("filepath", filepath);
    fd.append("cuid", cuid);
    fd.append("total_chunks", totalChunks);
    var xhr = new XMLHttpRequest();
    // xhr.upload.addEventListener("progress", function (event) { progressUploadHandler(event, action, id, totalChunks, webdriveModule.chunk_upload_queue[cuid]) }, false);
    if (webdriveModule.chunk_upload_queue[cuid] == totalChunks)
       xhr.addEventListener("load", function (event) { completeHandler(event, action, id) }, false);
    xhr.addEventListener("error", function (event) {
      webdriveModule.chunkUpload(id, chunk, filename, part, filepath, totalChunks, cuid);
     }, false);
    xhr.addEventListener("abort", function (event) { abortHandler(event, action, id) }, false);
    // xhr.timeout = 60000;
    // xhr.ontimeout = function (e) {
    //    webdriveModule.chunkUpload(id, chunk, filename, part, filepath, totalChunks, cuid);
    //   return;
    // };
    xhr.open("POST", url);
    xhr.onload = function (e) {
      if (this.readyState == 4 && this.status == 200) {
        try {
          var res = JSON.parse(this.responseText);
          if (res[cuid]['status'] == true) {
            if (res[cuid]['succeed'] == true) {
              setTimeout(function () {
                deleteTlUnit(id);
              }, 5000);
              webdriveModule.now++;
              if (webdriveModule.now < webdriveModule.queue.length) {
                webdriveModule.run();
              }
              else {
                webdriveModule.now = 0;
                webdriveModule.queue = [];
                document.getElementById('uploaderID').classList.remove('disabled');
                if (document.getElementById('web-drive-file-upload-id') != null)
                  document.getElementById('web-drive-file-upload-id').value = "";
                webdriveModule.driveReload();
              }
            }
            progressUploadHandler(id, totalChunks, webdriveModule.chunk_upload_queue[cuid]);
            webdriveModule.chunk_upload_queue[cuid]++;
          }
        } catch (e) {
          console.log(e); //If any runtime error
        }
      }

    };
    xhr.send(fd);
  },
  updateActionStatus: function (id, status, msg) {
    if (status == true) {
      document.getElementById('tl-unit-' + id).style = 'color: lightgreen';
      document.getElementById('tl-unit-' + id + '-img').src = 'images\\webdrive\\success.png';
      showNotificationMsg('succeed', msg);
    }
    else {
      document.getElementById('tl-unit-' + id).style = 'color:lightred';
      document.getElementById('tl-unit-' + id + '-img').src = 'images\\webdrive\\failed.png';
      showNotificationMsg('failed', msg);
    }
    // document.getElementById('tl-unit-' + id).title = msg;
    previousTlDtl = document.getElementById('operationActionStatus').innerHTML;
    setTimeout(function () {
      deleteTlUnit(id);
    }, 10000);
  },

  duplicateUpload: function (value) {
    let filenames = this.getFileNames();
    if (filenames.indexOf(value) == -1) return true;
    else return alert('File (' + value + ') already exist.');
  },
  dismissAll: function () {
    webdriveModule.discardActionFor();
    document.getElementById('context-menu-id').style.display = 'none';
    webdriveModule.discardRename(this.getPreRenameID());
    if (this.getActionForCopy().length > 0 && this.getActionForMoveFlag() == true) {
      this.discardActionFor();
      this.setActionForMoveFlag(false);
      this.prepareActionFor();
    }
    this.cancelNew();
    this.uploadDirClose();
    this.discardShare();
    this.resetOpcode();
  },
  sizeInMegaBytes: function (size, flag) {
    size = size / 1048576;
    if (flag)
      return (Math.round(size * 100) / 100) + ' MB';
    else
      return (Math.round(size * 100) / 100);
  },
  clearSearchUsers: function () {
    document.getElementById('wdrive_share_with_input').value = '';
    this.filterUsers();
  },
  filterUsers: function () {
    let input = document.getElementById('wdrive_share_with_input').value;
    let data = this.getUsers();
    if (typeof data != null && data != "undefined") {
      let filter, li, u, i, count, user, len, ele, genid;
      filter = input.toUpperCase();
      li = document.getElementById('wd-user-list-id').getElementsByClassName('wd-user-unit');
      len = li.length;
      count = 0;
      for (i = 0; i < len; i++) {
        ele = li[i];
        genid = ele.id.slice(7, 15);
        user = this.getUser(genid);
        if (user['selected'] == false) {
          if (typeof ele != null && ele != "undefined") {
            u = user['name'] + user['genid'] + user['contact_no'];
            if (u.toUpperCase().indexOf(filter) > -1) {
              ele.style.display = "";
              count++;
            }
            else
              ele.style.display = "none";
          }
          else {
            console.log(user['genid']);
          }
        }
      }
      // document.getElementById('wd-count-totalusers').innerHTML = count;
    }
  },
  prepareUserPopups: function () {
    let users = this.getUsers();
    let ids = users['ids'];
    let user, genid, img, len, count, tdata = '';
    len = ids['total'];
    count = 0;
    for (let i = 0; i < len; i++) {
      genid = ids[i];
      this.setUser(genid, 'selected', false);
      user = this.getUser(genid);
      count++;
      if (user['avater_count'] > 0)
        img = '<img  src="/images/profile/' + user['imgid'] + '"/>';
      else
        img = '<img src="/images/logged-user.png"/>';
      tdata += '<div id="wd-uid-' + genid + '" class="wd-user-unit" >\
      <div  class="wd-user-detail">'+ img + '<div class="hd-fcct" style="flex-grow:1;" >\
      <div class="hd-frbc"><span title="Phone: ' + user['contact_no'] + '" style="font-weight:bold">' + user['name'] + ' </span>\
      </div>\
      <span>Email: '+ user['email_id'] + '</span>\
      </div>\
      <div class="hd-frcc" style="align-self:flex-e"><span class="emailButton" onclick="webdriveModule.shareEmail(' + genid + ')">Email</span>\
      <span class="shareButton"  onclick="webdriveModule.shareReq(' + genid + ')">Share</span></div>\
      </div><div id="fcoder-inbox-'+ genid + '" class="fcoder-inbox"></div></div>';
    }
    document.getElementById('wd-user-list-id').innerHTML = tdata;
  },
};
