var webdriveModule = {
  active: false,
  previewPath: '',
  access_key: '',
  countuploadtry: 0,
  opcode: '',
  divholder: '',
  app_storage: '',
  sharedbyme: [],
  sharedfiles: [],
  filelist: [],
  filelink: [],
  share_access: false,
  ftransfer_access: false,
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
  actionFor: [],
  actionForCopy: [],
  backStack: [],
  sharedFlag: false,
  sendStatus: [],
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
  chunk_upload_queue:{},
  bytes_per_chunk: 1048576,
  getBytesPerChunk: function(){
    return this.bytes_per_chunk;
  },
  setBytesPerChunk: function(val){
    this.bytes_per_chunk = val;
  },
  getAccessKey: function () {
    return this.access_key;
  },
  setAccessKey(val) {
    this.access_key = val;
  },
  getPreviewPath: function (inode) {
    if (this.getSharedflag())
      return this.previewPath + this.getSharebase(this.getSnode()) + "\\" + this.getShareInfo(inode)['realpath'];
    else {
      if (this.getAccessKey().split(" ")[2] != null)
        return this.previewPath + this.getAccessKey().split(" ")[2] + "\\" + this.getFileInfo(inode)['path'];
      else return false;
    }
  },
  setPreviewPath: function (val) {
    this.previewPath = val;
  },
  getSendStatus: function () {
    return this.sendStatus;
  },
  setSendStatus: function (val) {
    this.sendStatus = val;
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
  getUser: function (id) {
    return this.users[id];
  },
  setAppStorage: function (val) {
    this.app_storage = val;
  },
  getAppStorage: function () {
    return this.app_storage;
  },
  setUser: function (id, key, val) {
    this.users[id][key] = val;
  },
  selectedUsers: [],
  getSelectedUsers: function () {
    return this.selectedUsers;
  },
  setSelectedUsers: function (selectedUsers) {
    this.selectedUsers = selectedUsers;
  },
  pushSelectedUsers: function (genid) {
    this.selectedUsers.push(genid);
  },
  popSelectedUsers: function (genid) {
    let index = this.selectedUsers.indexOf(genid);
    if (index != -1) this.selectedUsers.splice(index, 1);
  },
  selectUser: function (el, genid) {
    let user = this.getUser(genid);
    let parent = document.getElementById("wd-user-list-id");
    let actionImg;
    let btn = document.getElementById('wd-send-share-btn');
    if (user['selected'] == true) {
      this.setUser(genid, 'selected', false);
      el.classList.remove('selected');
      parent.appendChild(el);
      this.popSelectedUsers(genid);
      actionImg = el.getElementsByClassName('wd-remove-user-button')[0];
      actionImg.classList.remove("wd-remove-user-button");
      actionImg.classList.add("wd-add-user-button");
      if (this.getSelectedUsers().length <= 0) {
        btn.style.visibility = "hidden";
        btn.style.opacity = "0";
        btn.style.filter = 'alpha(opacity=0)';
      }
    }
    else {
      if (this.getSelectedUsers().length < this.getTotalRecipients()) {
        this.setUser(genid, 'selected', true);
        el.classList.add('selected');
        parent.insertBefore(el, parent.firstChild);
        this.pushSelectedUsers(genid);
        actionImg = el.getElementsByClassName('wd-add-user-button')[0];
        actionImg.classList.remove("wd-add-user-button");
        actionImg.classList.add("wd-remove-user-button");
        btn.style.visibility = "visible";
        btn.style.opacity = "1";
        btn.style.filter = 'alpha(opacity=100)';
      }
      else {
        showNotificationMsg('alert', 'You can Share/Send File to Maximum ' + this.getTotalRecipients() + ' recipients at once.');
      }
    }
    document.getElementById('wd-count-selectedusers').innerHTML = this.getSelectedUsers().length;
  },
  setFTransferaccess: function (value) {
    this.ftransfer_access = value;
  },
  getFTransferaccess: function () {
    return this.ftransfer_access;
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
    document.getElementById("body-webdriveModule").style.height = height;
    document.getElementById("webDriveLeftDiv").style.height = height - 20;
    document.getElementById("webDriveRightDiv").style.height = height - 20;
    document.getElementById("webDriveTree").style.height = height - 20;
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
              document.getElementById("body-webdriveModule").innerHTML = res['body-webdriveModule'];
              webdriveModule.commonMenu = document.getElementById('wdrive-common-menu-id');
              webdriveModule.cautionMenu = document.getElementById('wdrive-caution-menu-id');
              webdriveModule.singleFDMenu = document.getElementById('wdrive-singlefileordir-menu-id');
              webdriveModule.compressMenu = document.getElementById('wdrive-compress-menu-id');
              webdriveModule.extractMenu = document.getElementById('wdrive-extract-menu-id');
              webdriveModule.pasteMenu = document.getElementById('wdrive-paste-menu-id');
              webdriveModule.setUsers(res['users']);
              webdriveModule.setUploadLimit(res['opts']['ul']);
              webdriveModule.setTotalRecipients(res['opts']['tr']);
              webdriveModule.setTotalUploads(res['opts']['tu']);
              webdriveModule.setAppStorage(res['app_storage']);
              webdriveModule.setBytesPerChunk(res['bytes_per_chunk']);
              webdriveModule.setAccessKey(res['opts']['key']);
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
    xmlhttp.open("GET", "modules/webdrive/drive_init.php?auth_ph=" + auth_ph + "&ph=" + ph, true);
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
    var url = this.getAppStorage() + "/wdproxy/webdrive/drive_reload.php";
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
    formData.append('access_key', this.getAccessKey());
    xmlhttp.send(formData);
    xmlhttp.onreadystatechange = function () {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        try {
          var res = JSON.parse(xmlhttp.responseText);
          if (res['opts']['status'] == true) {
            document.getElementById('wdrive-free-space-id').innerHTML = webdriveModule.sizeInMegaBytes(res['wdrivefreesize']);
            let wdfs = Math.floor(res['wdrivefsfactor'])
            document.getElementById('wdrive-fs-factor').style.strokeDasharray = "" + (100 - wdfs) + ", 200";
            document.getElementById('wdrive-fs-percentage').innerHTML = wdfs + '%';
            webdriveModule.setMysharesize(webdriveModule.sizeInMegaBytes(res['mysharesize']));
            webdriveModule.setMysharelimit(res['sharelimit']);
            webdriveModule.setSharedbyme(res['sharedbyme']);
            webdriveModule.setSharedfiles(res['sharedfiles']);
            webdriveModule.setFilelist(res['filelist']);
            webdriveModule.setFilelink(res['filelink']);
            webdriveModule.setSharenodes(res['sharenodes']);
            webdriveModule.setShareindex(res['shareindex']);
            webdriveModule.setShareaccess(res['opts']['share']);
            webdriveModule.setFTransferaccess(res['opts']['ftransfer']);
            if (webdriveModule.getShareaccess() || webdriveModule.getFTransferaccess())
              webdriveModule.prepareUserPopups();

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
        } catch (e) {
          logout();
        }
      }
    }
  },
  driveShareReload: function (snode) {
    chk_session();
    if (window.XMLHttpRequest)
      xmlhttp = new XMLHttpRequest();
    else
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    var url = this.getAppStorage() + "/wdproxy/webdrive/drive_share_reload.php";
    xmlhttp.open('POST', url, true);
    var formData = new FormData();
    formData.append('snode', snode);
    formData.append('access_key', this.getAccessKey());
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
  toggleShareInbox: function () {
    chk_session();
    if (window.XMLHttpRequest)
      xmlhttp = new XMLHttpRequest();
    else
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    var url = this.getAppStorage() + "/wdproxy/webdrive/drive_share_init.php";
    xmlhttp.open('POST', url, true);
    var formData = new FormData();
    formData.append('access_key', this.getAccessKey());
    xmlhttp.onreadystatechange = function () {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        try {
          var res = JSON.parse(xmlhttp.responseText);
          if (res['opts']['status'] == true) {
            webdriveModule.setSharenodes(res['sharenodes']);
            webdriveModule.setShareindex(res['shareindex']);
            document.getElementById('context-menu-id').style.display = 'none';
            // if(snode=='flex')
            webdriveModule.renderShareInbox();
          }
          else
            showNotificationMsg('alert', res['opts']['msg']);
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
    document.getElementById('share-links-count-id').innerHTML = sharenodes['total'];
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
      if (file['dir'] == true) {
        fileTree = fileTree + '<div id="' + npf + inode + '" class="tnodeStyle" onClick="webdriveModule.renderWebDrive(' + inode + ',' + sharedFlag + ');" ><img id="' + ipf + inode + '" src="images\\webdrive\\' + nimg + '" width="20" height="20"/><span>' + name + '</span></div>';
        fileTree = fileTree + '<div class="innertnodeStyle"  id="' + inpf + inode + '"></div>';
      }
    }
    el.innerHTML = fileTree;
  },
  openImg: function (inode) {
    if (this.getPreviewPath(inode) != false) {
      let ppath = this.getPreviewPath(inode);
      document.getElementById("help-body-div-id").innerHTML = '<img src="' + ppath + '" style="max-width:100%;">';
      displaySuperModal('previewPane');
    }
    else showNotificationMsg('alert', 'Failed to display image. Please download the image to view it.');
  },
  openPdf: function (inode) {
    if (this.getPreviewPath(inode) != false) {
      let ppath = this.getPreviewPath(inode);
      document.getElementById("help-body-div-id").innerHTML = '<object data="' + ppath + '" type="application/pdf" width="800px" height="700px">\
      <embed src="' + ppath + '">\
          This browser does not support PDFs. Please download the PDF to view it: <a href="' + ppath + '">Download PDF</a>.</p>\
      </embed>\
  </object>';
      displaySuperModal('previewPane');
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
      this.discardShareOrSend();
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
    let filesOnDesk = '', file, filename, inode, type, size, time;
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


      this.fileNames.push(filename);
      size = showFileSizeInBytes(file['size']);
      time = file['mtime'];
      inode = file['inode'];
      type = file['ext'];
      if (type == 'docx') type = 'doc'; else if (type == 'xlsx') type = 'xls'; else if (type == 'pptx') type = 'ppt'; else if (type == 'pdf') type = 'pdf';
      if (type == 'png' || type == 'jpeg' || type == 'gif' || type == 'jpg') type = 'image';
      if (type == 'zip' || type == 'rar') type = 'zip';
      if (type != 'doc' && type != 'xls' && type != 'ppt' && type != 'pdf' && type != 'image' && type != 'zip') type = 'file';
      if (this.getDnodesLayout() != 'list') {
        if (file['dir'] == true)
          filesOnDesk = filesOnDesk + '<div id="dnode-' + inode + '" class="diconStyle" onclick="webdriveModule.selectFileFor(this,event);" ondblclick="webdriveModule.renderWebDrive(' + inode + ',' + sharedFlag + ');" >\
        <img src="images\\webdrive\\folder.png" />';
        else {
          if (type == 'pdf')
            filesOnDesk = filesOnDesk + '<div id="dnode-' + inode + '" class="diconStyle" onclick="webdriveModule.selectFileFor(this,event)" ondblclick="webdriveModule.openPdf(' + inode + ');"  >\
          <img  src="images\\webdrive\\pdf.png">';
          else if (type == 'image')
            filesOnDesk = filesOnDesk + '<div id="dnode-' + inode + '" class="diconStyle" onclick="webdriveModule.selectFileFor(this,event)" ondblclick="webdriveModule.openImg(' + inode + ');" >\
          <img src="images\\webdrive\\image.png">';
          else
            filesOnDesk = filesOnDesk + '<div id="dnode-' + inode + '" class="diconStyle" onclick="webdriveModule.selectFileFor(this,event)" >\
          <img src="images\\webdrive\\'+ type + '.png"/>';
        }

        if (sharedFlag)
          filesOnDesk = filesOnDesk + '<div class="hd-rldv"><img style="position:absolute;right:-27px;bottom:-2px;z-index:1;width:20px;height:20px" src="images\\webdrive\\sharedinbox.png"/></div>';
        else {
          if (sharedFiles.indexOf(inode) > -1)
            filesOnDesk = filesOnDesk + '<div class="hd-rldv"><img style="position:absolute;right:-27px;bottom:-2px;z-index:1;width:24px;height:24px" src="images\\webdrive\\sharedfile.png"/></div>';
        }
        filesOnDesk = filesOnDesk + '<span class="diconNameStyle" title="' + filename + '" id="dnode-name-' + inode + '" >' + shortname + '</span></div>';
      }
      else {
        if (file['dir'] == true) {
          type = 'folder';
          if (sharedFlag) type = 'shr' + type;
          else if (sharedFiles.indexOf(inode) > -1) type = 'myshr' + type;
          filesOnDesk = filesOnDesk + '<div id="dnode-' + inode + '"   class="dlistStyle" onclick="webdriveModule.selectFileFor(this,event);" ondblclick="webdriveModule.renderWebDrive(' + inode + ',' + sharedFlag + ');" ><img src="images\\webdrive\\' + type + '.png" /><span class="dlistNameStyle" value="' + filename + '" id="dnode-name-' + inode + '" >' + filename + '</span><span>' + size + '</span><span>' + time + '</span></div>';
        }
        else {
          if (type == 'zip' || type == 'rar')
            type = 'zip';
          else type = 'file';

          if (sharedFlag) type = 'shr' + type;
          else if (sharedFiles.indexOf(inode) > -1) type = 'myshr' + type;
          filesOnDesk = filesOnDesk + '<div id="dnode-' + inode + '" class="dlistStyle" onclick="webdriveModule.selectFileFor(this,event)" ><img src="images\\webdrive\\' + type + '.png" /><span class="dlistNameStyle" value="' + filename + '" id="dnode-name-' + inode + '">' + filename + '</span><span>' + size + '</span><span>' + time + '</span></div>';
        }
      }
    }
    document.getElementById("webDriveDashboard").innerHTML = filesOnDesk;
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
    let item = '';
    for (let i = 0; i < totalFiles; i++) {
      if (sharedFlag)
        file = this.getShareInfo(files[i]);
      else
        file = this.getFileInfo(files[i]);
      inode = file['inode'];
      item = document.getElementById('dnode-' + inode);
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
    if (this.getActionFor().length > 0)
      this.renderActionFor();
    if (this.getActionForMoveFlag() && this.getActionForCopy().length > 0)
      this.enableActionForMove();
  },
  gridView: function (el) {
    this.setDnodesLayout('grid');
    this.renderDashboard(this.getDboardPWD(), this.getSharedflag());
    el.outerHTML = '<span class="menuButton" id="wdrive-grid-list-id" style="min-width:30px" onclick="webdriveModule.listView(this)"><img style="height:20px;vertical-align:middle"  src="images\\webdrive\\list.png"></span>';
  },
  listView: function (el) {
    this.setDnodesLayout('list');
    this.renderDashboard(this.getDboardPWD(), this.getSharedflag());
    el.outerHTML = '<span class="menuButton" id="wdrive-grid-list-id" style="min-width:30px" onclick="webdriveModule.gridView(this)"><img style="height:20px;vertical-align:middle"  src="images\\webdrive\\grid.png"></span>';
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
    // path= '<div class="rlink" onClick="webdriveModule.renderWebDrive('+this.getRnode()+','+sharedFlag+');">My Drive</div>';

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
    var url = this.getAppStorage() + "/wdproxy/webdrive/drive_create_new.php";
    xmlhttp.open('POST', url, true);
    var formData = new FormData();
    formData.append('filename', name);
    formData.append('path', path);
    formData.append('access_key', this.getAccessKey());
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
      tdata += '<textarea style="text-align: center;font-size:11px;row:" id="dnode-name-createNew" class="rename-corner-input-style" value="New Folder" onclick="event.stopPropagation();" onkeydown="webdriveModule.pressEnter(event)">New Folder</textarea>';
      newDir.classList.add("diconStyle");
    }
    else {
      tdata += '<textarea rows="1" style="resize:none; text-align: left;font-size:11px;" id="dnode-name-createNew" class="rename-corner-input-style" value="New Folder" onclick="event.stopPropagation();" onkeydown="webdriveModule.pressEnter(event)">New Folder</textarea>';
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
    let div = document.getElementById('context-menu-id');
    let menuStyle = 'style="display:block;pointer-events: auto;"';
    if (sharedFlag) menuStyle = 'style="display:none;pointer-events: none;"';
    let tdata = '';
    div.style.display = 'flex';
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
      div.style.left = e.clientX - item.offsetLeft + 10 + 'px';
      div.style.top = e.clientY - item.offsetTop + 20 + 'px';
    }
    else {
      let dboard = document.getElementById('webDriveDashboard');
      div.style.left = e.clientX - dboard.offsetLeft + 10 + 'px';
      div.style.top = e.clientY - dboard.offsetTop + 20 + 'px';
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
        if (webdriveModule.getFTransferaccess() == true)
          tdata = tdata + '<span  ' + menuStyle + ' onclick="webdriveModule.cmActHandler(\'send\',' + dboardpwd + ')">Send</span>';
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
          if (webdriveModule.getFTransferaccess() == true)
            tdata = tdata + '<span  ' + menuStyle + ' onclick="webdriveModule.cmActHandler(\'send\',' + dboardpwd + ')">Send</span>';

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
        else {
          tdata = tdata + '<span ' + menuStyle + ' onclick="webdriveModule.cmActHandler(\'rename\',' + inode + ')">Rename</span>\
          <span onclick="webdriveModule.cmActHandler(\'copy\','+ dboardpwd + ')">Copy</span>\
          <span onclick="webdriveModule.cmActHandler(\'download\','+ dboardpwd + ')">Download</span>';
          if (webdriveModule.getFTransferaccess() == true)
            tdata = tdata + '<span ' + menuStyle + ' onclick="webdriveModule.cmActHandler(\'send\',' + dboardpwd + ')">Send</span>';

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
  },
  discardRename: function (inode) {
    let pnode = this.getPreRenameID();
    if (pnode != '' && pnode != 'undefined' && pnode != null) {
      let el = document.getElementById('dnode-name-' + pnode);
      if (this.getDnodesLayout() != 'list')
        el.outerHTML = '<span class="diconNameStyle" id="' + el.id + '">' + this.getFileInfo(pnode)['name'] + '</span>';
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
        el.outerHTML = '<span class="diconNameStyle" id="' + el.id + '">' + pname + '</span>';
      }
      else {
        if (window.XMLHttpRequest)
          xmlhttp = new XMLHttpRequest();
        else
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        var url = this.getAppStorage() + "/wdproxy/webdrive/drive_rename_file.php";
        xmlhttp.open('POST', url, true);
        var formData = new FormData();
        formData.append('oldname', pname);
        formData.append('newname', filename);
        formData.append('path', path);
        formData.append('inode', inode);
        formData.append('access_key', this.getAccessKey());
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
  discardShareOrSend: function () {
    if (document.getElementById('shareOrSendWithID') != null) {
      let el = document.getElementById('shareOrSendWithID');
      el.style.bottom = '-100px';
      el.style.visibility = 'hidden';
      el.style.opacity = '0';
      el.style.filter = 'alpha(opacity=0)';
      document.getElementById('wdrive_share_with_input').value = "";
      this.setSelectedUsers([]);
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
    if (option == 'share' || option == 'send') {

      this.cancelNew();
      this.uploadDirClose();
      if (document.getElementById('shareOrSendWithID') != null) {
        this.prepareFilesToShareOrSend();
        let el = document.getElementById('shareOrSendWithID');
        el.style.left = document.getElementById("webDriveDashboard").clientWidth / 2 - 335;
        //Because of initial location of share div
        // el.style.top = - document.getElementById("webDriveDashboard").clientHeight / 2 - 20;
        el.style.visibility = 'visible';
        el.style.opacity = '1.0';
        el.style.filter = 'alpha(opacity=100)';
        let titleID = document.getElementById('wd-user-list-move');
        titleID.addEventListener('mousedown', function (e) {
          webdriveModule.isDown = true;
          webdriveModule.offset = [
            el.offsetLeft - e.clientX,
            el.offsetTop - e.clientY
          ];
        }, true);
        webdriveModule.targetDiv = el;
      }
      if (option == 'share')
        document.getElementById('wd-send-share-btn-text').innerHTML = 'Share';
      else if (option == 'send')
        document.getElementById('wd-send-share-btn-text').innerHTML = 'Send';
    }
    else {
      this.discardShareOrSend();
      if (option == 'paste') {
        chk_session();
        let len = this.actionForCopy.length;
        this.pasteMenu.classList.remove('wdrive-group-menu-active');
        if (len > 0) {
          let remarks = '';
          let pwd = [];
          var url = this.getAppStorage() + "/wdproxy/webdrive/drive_move_files.php";
          let files = this.getActionForCopy();
          if (this.getCopyFromShare() == true) {
            url = this.getAppStorage() + "/wdproxy/webdrive/drive_move_shared_files.php";
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
          formData.append('access_key', this.getAccessKey());
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
              var url = this.getAppStorage() + "/wdproxy/webdrive/drive_download_files.php";
              if (this.getSharedflag() == true) {
                var url = this.getAppStorage() + "/wdproxy/webdrive/drive_download_shared_files.php";
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
              formData.append('access_key', this.getAccessKey());

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

              var url = this.getAppStorage() + "/wdproxy/webdrive/drive_compress_files.php";
              xmlhttp.open('POST', url, true);
              var formData = new FormData();
              formData.append('filenames', files);
              formData.append('pwd', pwd);
              formData.append('access_key', this.getAccessKey());
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
              var url = this.getAppStorage() + "/wdproxy/webdrive/drive_extract_file.php";
              xmlhttp.open('POST', url, true);
              var formData = new FormData();
              formData.append('filename', file);
              formData.append('pwd', pwd);
              formData.append('access_key', this.getAccessKey());
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
                var url = this.getAppStorage() + "/wdproxy/webdrive/drive_delete_files.php";
                xmlhttp.open('POST', url, true);
                var formData = new FormData();
                formData.append('filenames', files);
                formData.append('pwd', pwd);
                formData.append('access_key', this.getAccessKey());
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
              showNotificationMsg('alert', 'The file (s) could not be deleted due to sharing by others')
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
    if (this.getOpcode() == 'share' || this.getOpcode() == 'send') {
      if (this.getActionFor().length == 0) {
        this.discardShareOrSend();
        this.resetOpcode();
      }
      else this.prepareFilesToShareOrSend();
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
          document.getElementById('wdrive-back-img-id').src = 'images\\webdrive\\backinactive.png';
          document.getElementById('wdrive-back-btn-id').style.pointerEvents = 'none';
        }
      }
    }
    else {
      document.getElementById('wdrive-back-img-id').src = 'images\\webdrive\\backinactive.png';
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
  // uploadDir: function () {
  //   this.discardShareOrSend();
  //   this.cancelNew();
  //   this.resetOpcode();
  //   this.countuploadtry = 0;
  //   this.previousUpStat = '';
  //   this.discardActionForCopy();
  //   this.prepareActionFor();
  //   this.discardActionFor();
  //   document.getElementById('context-menu-id').style.display = 'none';
  // },
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
  sendShareREQ: function () {
    // let genid = this.getSelectedgenid();
    let alertID, option = this.getOpcode();
    let selectedUsers = this.getSelectedUsers();
    let len = this.getActionFor().length;
    if (selectedUsers.length == 0 || len == 0) {
      if (len == 0) {
        showNotificationMsg('alert', 'File(s) has not been selected yet. Select one or more file and try again.');
      }
      else {
        showNotificationMsg('alert', 'Select User before Sharing/Sending files.');
      }
    }
    else {
      chk_session();
      let pnode = this.getDboardPWD();
      let files = [], pwd = this.getFileInfo(pnode)['path'];
      for (let i = 0; i < len; i++)
        files[i] = this.getFileInfo(this.actionFor[i])['path'];
      if (window.XMLHttpRequest)
        xmlhttp = new XMLHttpRequest();
      else
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      if (option == 'share')
        alertID = createTlUnit(xmlhttp, option, 'Sharing... ' + len + ' file(s)');
      else
        alertID = createTlUnit(xmlhttp, option, 'Sending... ' + len + ' file(s)');
      var url = this.getAppStorage() + "/wdproxy/webdrive/drive_" + option + "_files.php";
      xmlhttp.open('POST', url, true);
      var formData = new FormData();
      formData.append('filenames', files);
      formData.append('selectedUsers', selectedUsers);
      formData.append('pwd', pwd);
      formData.append('access_key', this.getAccessKey());
      xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          try {
            var user, files, inode, res = JSON.parse(xmlhttp.responseText);
            if (res['opts']['status'] == true) {
              if (option == 'share') {
                let succeedlist = res['sharesucceed'];
                for (let j = 0; j < selectedUsers.length; j++) {
                  user = webdriveModule.getUser(selectedUsers[j]);

                  files = succeedlist[user['genid']];
                  for (let i = 0; i < files.length; i++) {
                    inode = files[i];
                    if (webdriveModule.sharedfiles.indexOf(inode) == -1) {
                      webdriveModule.sharedfiles.push(inode);
                      webdriveModule.sharedbyme[inode] = [];
                    }
                    webdriveModule.sharedbyme[inode].push(user['genid']);
                  }
                }
                document.getElementById('wdrive_share_with_input').value = "";
                webdriveModule.setMysharesize(webdriveModule.sizeInMegaBytes(res['mysharesize']));
              }
              else if (option == 'send') {
                webdriveModule.setSendStatus(res['sendStatus']);
              }
              webdriveModule.prepareFilesToShareOrSend();
              webdriveModule.renderDashboard(pnode);
            }
            webdriveModule.updateActionStatus(alertID, res['opts']['status'], res['opts']['msg']);
          } catch (e) {
            console.log(e); //If any runtime error
          }
        }
      }
      xmlhttp.send(formData);
    }
  },
  prepareFilesToShareOrSend: function () {
    let div = document.getElementById('existing-sharewith-id');
    let tdata = '', sharedwith = [], files = this.getActionFor(), genid, file, filename, sfilename, partlen;
    tdata = '';
    let el;
    if (this.getOpcode() == 'share') {
      for (let i = 0; i < files.length; i++) {
        sharedwith = [];
        file = files[i];
        el = 'dnode-' + file;
        filename = this.getFileInfo(file)['name'];
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
            if (this.getUser(genid) != null)
              tdata = tdata + '<li class="activeUserItemStyle" onclick="webdriveModule.removesharewith(' + file + ',\'' + genid + '\')"><span style="margin-right:5px">' + this.getUser(genid)['name'] + '</span><span style="color:red;font-width:bold">[-]</span></li>';
            else
              tdata = tdata + '<li class="activeUserItemStyle" onclick="webdriveModule.removesharewith(' + file + ',\'' + genid + '\')"><span style="margin-right:5px"> User at other Office </span><span style="color:red;font-width:bold">[-]</span></li>';
          }
        }
        else {
          tdata = tdata + '<li><span title="' + filename + '"><span>' + (i + 1) + '. </span>' + sfilename + '</span></li>';
        }
        tdata = tdata + '</ul>';
      }
    }
    else {
      let sendStatus = this.getSendStatus();
      for (let i = 0; i < files.length; i++) {
        file = files[i];
        el = 'dnode-' + file;
        filename = this.getFileInfo(file)['name'];
        sfilename = filename;
        if (filename.length > 20) {
          partlen = Math.min(20, filename.lastIndexOf(" "));
          sfilename = filename.substr(0, partlen == -1 ? 20 : partlen) + "...";
        }
        tdata = tdata + '<ul>';
        tdata = tdata + '<li><span title="' + filename + '"><span>' + (i + 1) + '. </span>' + sfilename + '</span><span onclick="event.stopPropagation();document.getElementById(\'' + el + '\').click()" style="color:dimgray;font-width:bold">[X]</span></li>';
        if (sendStatus.length != 0) {
          if (sendStatus[file] != null) {
            if (sendStatus['invalidfile'].indexOf(parseInt(file)) == -1) {
              let succeedlist = sendStatus[file]['succeeduser'];
              let failedlist = sendStatus[file]['faileduser'];
              for (let j = 0; j < succeedlist.length; j++) {
                genid = succeedlist[j];
                tdata = tdata + '<li class="activeUserItemStyle" ><span style="margin-right:5px">' + this.getUser(genid)['name'] + '</span><img  title="File Transfered Successfully." style="height:18px;vertical-align:middle;margin-left:5px" src="images\\yes.png"></li>';
              }
              for (let j = 0; j < failedlist.length; j++) {
                genid = failedlist[j];
                tdata = tdata + '<li class="activeUserItemStyle" ><span style="margin-right:5px">' + this.getUser(genid)['name'] + '</span><img  title="Failed to Transfer File." style="height:18px;vertical-align:middle;margin-left:5px" src="images\\noo.png"></li>';
              }
            } else {
              tdata = tdata + '<li><span style="color:red;margin-right:5px">' + sendStatus[file]['msg'] + '</span><img  title="Failed to Transfer File." style="height:18px;vertical-align:middle;margin-left:5px" src="images\\noo.png"></li>';
            }
          }
        }
        tdata = tdata + '</ul>';
      }
      this.setSendStatus([]);
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
      var url = this.getAppStorage() + "/wdproxy/webdrive/drive_share_remove.php";
      xmlhttp.open('POST', url, true);
      var formData = new FormData();
      formData.append('file', file);
      formData.append('sharecancelwith', sharecancelwith);
      formData.append('access_key', this.getAccessKey());
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
                webdriveModule.setMysharesize(webdriveModule.sizeInMegaBytes(res['mysharesize']));
              }
              webdriveModule.prepareFilesToShareOrSend();
              webdriveModule.renderDashboard(webdriveModule.getDboardPWD());
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
    var url = this.getAppStorage() + "/wdproxy/webdrive/drive_share_cancel.php";
    xmlhttp.open('POST', url, true);
    var formData = new FormData();
    formData.append('snode', snode);
    formData.append('access_key', this.getAccessKey());
    xmlhttp.onreadystatechange = function () {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        try {
          var res = JSON.parse(xmlhttp.responseText);
          // if (res['opts']['status'] == true) {
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
          //   webdriveModule.renderWebDrive(snode, true);
          // }
          // else
          //   showNotificationMsg('failed', res['opts']['msg']);
          webdriveModule.toggleShareInbox();
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
        // this.setSelectedgenid(document.getElementById('wdrive_share_with_input').value);
        this.sendShareREQ();
      }
    }
  },
  queue: [], // upload queue
  now: 0, // current file being uploaded
  start: function (files) {
    if (webdriveModule.queue.length == 0) {
      let startupload = false;
      // VISUAL - DISABLE UPLOAD UNTIL DONE
      let name, size, count = 0, id, len = files.length;
      for (let i = 0; i < len; i++) {
        name = files[i].name;
        size = files[i].size;
        if (this.duplicateUpload(name) == true) {
          id = this.countuploadtry + count;
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
    let url = this.getAppStorage() + "/wdproxy/webdrive/drive_upload_file.php";
    xhr.open('POST', url, true);
    var formData = new FormData();
    formData.append('filepath', filepath);
    formData.append('filename', this.queue[this.now].name);
    formData.append('filesize', this.queue[this.now].size);
    formData.append('access_key', this.getAccessKey());
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
            webdriveModule.chunk_upload_queue[cuid]= [];
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
      this.chunk_upload_queue[cuid][count]=1;
      this.chunkUpload(id, blob.slice(start, end), blob.name, count, filepath, totalChunks, cuid);
      start = end;
      end = start + bpc;
      count = count + 1;
    }
  },
  chunkUpload: function (id, chunk, filename, part, filepath, totalChunks, cuid) {
    var action = this.getOpcode();
    var fd = new FormData();
    let trySeq = this.chunk_upload_queue[cuid][part];
    let url = this.getAppStorage() + "/wdproxy/webdrive/drive_upload_chunk.php?cuid_no="+cuid+"&chunk_seq="+part+"&try="+trySeq;
    fd.append("fileToUpload", chunk);
    fd.append('access_key', this.getAccessKey());
    fd.append("seq", part);
    fd.append("filename", filename);
    fd.append("filepath", filepath);
    fd.append("cuid", cuid);
    fd.append("total_chunks", totalChunks);
    var xhr = new XMLHttpRequest();
    xhr.upload.addEventListener("progress", function (event) { progressUploadHandler(event, action, id, totalChunks, part+1) }, false);
    if ((part+1) == totalChunks)
      xhr.addEventListener("load", function (event) { completeHandler(event, action, id) }, false);

    xhr.addEventListener("error", function (event) { abortHandler(event, action, id) }, false);
    xhr.addEventListener("abort", function (event) { abortHandler(event, action, id) }, false);
    
    xhr.open("POST", url);
    xhr.onload = function (e) {
      if (this.readyState == 4 && this.status == 200) {
        try {
          var res = JSON.parse(this.responseText); 
          if( res['status']== true){
            if (res['cuid_seq'] == totalChunks) {
              setTimeout(function () {
                deleteTlUnit(id);
              }, 5000);
              webdriveModule.now++;
              if (webdriveModule.now < webdriveModule.queue.length) {
                webdriveModule.run();
              }
              else {
                webdriveModule.countuploadtry = webdriveModule.countuploadtry + webdriveModule.now;
                webdriveModule.now = 0;
                webdriveModule.queue = [];
                document.getElementById('uploaderID').classList.remove('disabled');
                if (document.getElementById('web-drive-file-upload-id') != null)
                  document.getElementById('web-drive-file-upload-id').value = "";
                webdriveModule.driveReload();
              }
            }
          }
          else if(trySeq < 5){
            webdriveModule.chunkUpload(id, chunk, filename, part, filepath, totalChunks, cuid);
            webdriveModule.chunk_upload_queue[cuid][part]++;
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
    this.discardShareOrSend();
    this.resetOpcode();
  },
  sizeInMegaBytes: function (size) {
    size = size / 1048576;
    return (Math.round(size * 100) / 100) + ' MB';
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
      document.getElementById('wd-count-totalusers').innerHTML = count;
    }
  },
  prepareUserPopups: function () {
    let users = this.getUsers();
    let ids = users['ids'];
    let user, genid, img, len, count, tdata = '';
    len = ids['total'];
    count = 0;
    tdata += '<div style="padding:3px">Click to Add [ Users: <span id="wd-count-totalusers">' + len + ' </span> (Selected: <span id="wd-count-selectedusers">0</span>)]</div>';
    for (let i = 0; i < len; i++) {
      genid = ids[i];
      this.setUser(genid, 'selected', false);
      user = this.getUser(genid);
      count++;
      if (user['avater_count'] > 0)
        img = '<img  src="images\\profile\\' + user['userid'] + '"/>';
      else
        img = '<img src="images\\fcoder-logged-user.png"/>';
      tdata += '<div id="wd-uid-' + genid + '" class="wd-user-unit" onclick="webdriveModule.selectUser(this, \'' + genid + '\');">\
      '+ img + '<div class="wd-user-detail">\
      <span><span style="font-weight:bold">'+ user['name'] + ' </span><span >(' + user['userid'] + ')</span></span>\
      <span><span >Email: '+ user['email_id'] + ', </span><span > Phone: ' + user['contact_no'] + '</span></span>\
      </div>\
      <div class="wd-add-user-button" ></div>\
      </div>';
    }
    document.getElementById('wd-user-list-id').innerHTML = tdata;
    this.setSelectedUsers([]);
  },
};
