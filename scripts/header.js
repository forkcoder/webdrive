/*******************Start   Browser Backspace & Keyboard backspace for back Disabling script *****************************/
(function (global) {
  if (typeof (global) === "undefined") {
    throw new Error("window is undefined");
  }
  var _hash = "!";
  var noBackPlease = function () {
    global.location.href += "#";
    global.setTimeout(function () {
      global.location.href += "!";
    }, 50);
  };
  // Earlier we had setInerval here....
  global.onhashchange = function () {
    if (global.location.hash !== _hash) {
      global.location.hash = _hash;
    }
  };
  global.onload = function () {
    noBackPlease();
    document.body.onkeydown = function (event) {
      event = event || window.event;
      var elm = (event.target || event.srcElement).nodeName.toLowerCase();
      if (event.which === 8 && (elm !== 'input' && elm !== 'textarea')) {
        event.preventDefault();
        event.returnValue = false;
      }
      if (event == window.event)
        event.stopPropagation();
    };
    document.getElementById('mainContentDiv').style.height = (document.documentElement.clientHeight - 136);
  };
})(window);
document.addEventListener('DOMContentLoaded', function() {
  // webdriveModule.init();
}, false);
// document.addEventListener("DOMContentLoaded", function() {
//   webdriveModule.init();
// });
/*******************End Browser Backspace & Keyboard backspace for back Disabling script *****************************/

/******************** Start Session management Function ************************/
var previousTlDtl = '';
var loginSession = localStorage.getItem('loginSession');
var redirInProgress = 0;
var activeTab = "login-main-menu-table";
//Sample Login Function
function _login() {
  //Set login session = 1 after a successful login
  sessionStorage.setItem('loginNotice', 'showOnce');
  localStorage.setItem('loginSession', 1);
}
function sv() { var sv = localStorage.getItem('loginSession'); return sv; }
function _logout() {
  localStorage.setItem('loginSession', 0);
}

function checkSession() {
  var loginSession = localStorage.getItem('loginSession');
  if (parseInt(loginSession) !== 1 && location.href.indexOf('index.php') < 0) {
    console.log("Session Status: LoggedOut");
    redirInProgress = 1;
  }
  else
    console.log("Session Status: LoggedIn");
}
window.addEventListener('resize', function () {
  document.getElementById('mainContentDiv').style.height = (document.documentElement.clientHeight - 136);
});
//See BackCache tutorial
window.addEventListener('pagehide', function () {
  if (event.persisted) {

  }
});
window.addEventListener('pageshow', function () {
  if (event.persisted) {

  }
});
window.addEventListener('click', function () {
  if (document.getElementById('loggeduserinfodiv') != null) {
    document.getElementById('loggeduserinfodiv').style.display = 'none';
    document.getElementById('editLoggedUserImgDiv').style.display = 'none';
  }
  if (document.getElementById('logged-user-img-id') != null) {
    document.getElementById('logged-user-img-id').style.backgroundColor = 'white';
    document.getElementById('logged-user-img-id').classList.remove('errorinput');
  }
});
/**************** End Inactivity Check  ************************************/

function chk_session() {
  var id = 2;
  var xmlhttp;
  if (window.XMLHttpRequest)
    xmlhttp = new XMLHttpRequest();
  else
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  xmlhttp.onreadystatechange = function () {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
      var res = xmlhttp.responseText;
      var f = "";
      if (res.length > 1) f = res.substr(0, 1);
      ph = res.substr(1, res.length - 1);
      if (f != 1)
        window.location.href = "index.php";
    }
  }
  xmlhttp.open("GET", "modules/auth/chk_session.php?id=" + id + "&auth_ph=" + auth_ph, false);
  xmlhttp.send();
}
/******************** End  Session management Function ************************/

function increaseHeight(el) {
  el.height = "";
  el.style.height = Math.min(el.scrollHeight) + "px";
}

function toggleTimelineFeeds() {
  let tldtl = document.getElementById("");
  let tlicon = document.getElementById("timeline-icon-id");
  if (tldtl.style.display == "none") {
    tldtl.style.display = "flex";
    // tldtl.src = "images\\webdrive\\notification.png";
  }
  else {
    tldtl.style.display = "none";
    // tldtl.src = "images\\webdrive\\notification.png";
  }

}

/********************************************************/
document.addEventListener('mouseup', function () {
  if (typeof webdriveModule !== "undefined" && webdriveModule.active != null && webdriveModule.active == true)
    webdriveModule.isDown = false;
}, true);
document.addEventListener('mousemove', function (event) {
  event.preventDefault();
  if (typeof webdriveModule !== "undefined" && webdriveModule.active != null && webdriveModule.active == true && webdriveModule.isDown) {
    webdriveModule.mousePosition = {
      x: event.clientX,
      y: event.clientY
    };
    webdriveModule.targetDiv.style.left = (webdriveModule.mousePosition.x + webdriveModule.offset[0]) + 'px';
    webdriveModule.targetDiv.style.top = (webdriveModule.mousePosition.y + webdriveModule.offset[1]) + 'px';
  }
}, true);
window.addEventListener("resize", function () {
  if (typeof webdriveModule !== "undefined" && webdriveModule.active != null && webdriveModule.active == true)
    webdriveModule.adjustHeight();
});
window.addEventListener("load", function () {
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
        webdriveModule.start(e.dataTransfer.files);
      });
    }
  }
  else {
    document.getElementById('uploaderID').style.display = "none";
  }
});
window.onkeyup = function (event) {
  if (event.keyCode == 27) {
    if (typeof conferenceModule !== "undefined" && conferenceModule.active != null && conferenceModule.active == true)
      conferenceModule.dismissPopups();
    if (typeof filetransferModule !== "undefined" && filetransferModule.active != null && filetransferModule.active == true)
      filetransferModule.dismissPopups();
    if (typeof userassetsModule !== "undefined" && userassetsModule.active != null &&
      userassetsModule.active == true) {
      let el = document.getElementById('listofworkstationsdiv');
      if (el != null) {
        el.style.visibility = 'hidden';
        el.style.opacity = 0;
      }
    }
  }
  if (event.keyCode == 13) {
    if (activeTab == 'user-assets-table' && userassetsModule.actove == true) {

    }
  }
  event.stopPropagation();
};
function showNotificationMsg(type, notification) {
  let color = '#c51244';
  if (type == 'succeed') color = 'seagreen';
  else if (type == 'failed') color = 'darkred';
  else if (type == 'alert') color = 'darkyellow';
  var errordiv = document.getElementById('msgDisplayDiv');
  errordiv.innerHTML = '<div class="errormsgstyle" style="background:' + color + ' !important"><span> ' + notification + '</span> <img src="images\\clear.png" onclick="this.parentNode.parentNode.removeChild(this.parentNode);"></div>';
  errordiv.style.visibility = 'visible';
  errordiv.style.opacity = '1';
  errordiv.style.bottom = "100px";
  setTimeout(function () {
    closeErrorMsg();
  }, 10000);
}
function showErrorMsg(errors) {
  let tdata = '', totalerrors = errors['total'];
  for (let i = 0; i < totalerrors; i++) {
    if (errors['key'][i] != 'aua__only_error')
      document.getElementById(errors['key'][i]).classList.add('errorinput');
    tdata += '<div class="errormsgstyle" style="background:#c51244 !important"><span> ' + errors['msg'][i] + '</span> <img src="images\\clear.png" onclick="this.parentNode.parentNode.removeChild(this.parentNode);"></div>';
  }
  var errordiv = document.getElementById('msgDisplayDiv');
  errordiv.innerHTML = tdata;
  errordiv.style.visibility = 'visible';
  errordiv.style.opacity = '1';
  errordiv.style.bottom = "100px";
  setTimeout(function () {
    closeErrorMsg();
  }, 10000);
}
function closeErrorMsg() {
  var errordiv = document.getElementById('msgDisplayDiv');
  if (errordiv != null) {
    errordiv.style.bottom = "0";
    errordiv.style.visibility = 'hidden';
    errordiv.style.opacity = '0';
  }
}
function IE10orBelow() {
  var ua = window.navigator.userAgent;
  var msie = ua.indexOf('MSIE ');
  if (msie > 0) {
    return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
  }
}
function renderHelpDetail(option, title) {
  var el = document.getElementById('helpMsgDisplayDiv');
  let ext = '';
  if (option == 'hostname') ext = '.png';
  else if (option == 'ipaddress') ext = '.gif';
  else if (option == 'mac') ext = '.gif';
  document.getElementById('help-header-div-id').innerHTML = title;
  document.getElementById('help-body-div-id').innerHTML = '<img style="height:430px;width:740px;margin:5px;vertical-align:middle" onclick="exitHelpDetail()" src="images\\helps\\' + option + ext + '">';

  if (navigator.userAgent.match(/MSIE 8/) !== null) {
    el.style.opacity = "0.2";
    el.style.filter = 'alpha(opacity=20)';
  }
  el.style.display = 'flex';
}
function exitHelpDetail() {
  document.getElementById('helpMsgDisplayDiv').style.display = 'none';
}

function createTlUnit(xmlhttp, action, name, title) {

    document.getElementById('operationActionStatus').style.display = 'flex';
    document.getElementById("developedbyCredit").style.display='none';
  
  let id = new Date().valueOf();
  let lcut=20, tcut, shortname = name;
  let sdata='';
  if (action == 'upload'){
    lcut = 13;
    sdata = '<span style="color:skyblue;margin:auto 3px" id="tl-unit-'+ id + '-status"></span> [<span style="color:orange;white-space:nowrap" id="tl-unit-' + id + '-statusBar">0 KB</span>]';
  }
  if (name.length > lcut) {
    tcut = Math.min(lcut, name.lastIndexOf(" "));
    shortname = name.substr(0, tcut == -1 ? lcut : tcut) + "...";
  }
  
     let tdata = '<div class="tl-unit" style="color:lightblue;" id="tl-unit-' + id + '"  title="' + title + '" >\
  <div class="tl-unit-info">\
  <img onclick="deleteTlUnit(' + id + ')"   id="tl-unit-' + id + '-img" src="images\\webdrive\\'+action+'.png"/>\
  <div class="progress"  data-label="'+ shortname +'"><span id="tl-unit-' + id + '-progressBar" class="value" style="width:0%;"></span></div>\
  </div>'+sdata+'</div>';
  previousTlDtl = previousTlDtl + tdata;

  document.getElementById('operationActionStatus').innerHTML = previousTlDtl;
  xmlhttp.addEventListener("load", completeHandler, false);
  if(action == 'upload')
    xmlhttp.upload.addEventListener("progress", function (event) { progressHandler(event, action, id) }, false);
  else
  xmlhttp.addEventListener("progress", function (event) { progressHandler(event, action, id) }, false);

  xmlhttp.addEventListener("error", function (event) { abortHandler(event, action, id) }, false);
  xmlhttp.addEventListener("abort", function (event) { abortHandler(event, action, id) }, false);
  return id;
}
function deleteTlUnit(id) {
  document.getElementById('operationActionStatus').removeChild(document.getElementById('tl-unit-' + id));
  previousTlDtl = document.getElementById('operationActionStatus').innerHTML;
  if(previousTlDtl==""){
    document.getElementById('operationActionStatus').style.display = 'none';
    document.getElementById("developedbyCredit").style.display='flex';
  } 
}
function progressHandler(event, action, id) {
  var percent = 0, et = event.total;

  if(event.total==0) et=event.loaded;
  percent = event.loaded / et * 100;
  if (action == 'upload') {
    document.getElementById('tl-unit-' + id + '-statusBar').innerHTML = showFileSizeInBytes(event.loaded);
    document.getElementById('tl-unit-' + id + '-progressBar').style.width = Math.round(percent);
    document.getElementById('tl-unit-' + id + '-status').innerHTML = Math.round(percent) + "%";
  }
  else if (action == 'compress') {
    if (event.lengthComputable == false) {
      var total = event.getResponseHeader('content-length')
      var encoding = event.getResponseHeader('content-encoding')
      if (total && encoding && (encoding.indexOf('gzip') > -1)) {
        total *= 4;
        percent = Math.min(100, event.loaded / total * 100)
      } else {
        console.log('lengthComputable failed')
      }
    }
  }
  else if (action == 'download') {

  }
}
function abortHandler(event, action, id) {
  document.getElementById('tl-unit-' + id + '-img').src = 'images\\webdrive\\failed.png';
  document.getElementById('tl-unit-' + id).title = 'Failed.';
  previousTlDtl = document.getElementById('operationActionStatus').innerHTML;
}
function completeHandler(event, action, id) {
  previousTlDtl = document.getElementById('operationActionStatus').innerHTML;
}

function showFileSizeInBytes(size) {
  var fSExt = new Array('B ', 'KB', 'MB', 'GB'), i = 0;
  while (size > 900) { size /= 1024; i++; }
  var exactSize = (Math.round(size * 100) / 100) + ' ' + fSExt[i];
  return exactSize;
}

function check_submit(e, fileObj, str, frm) {

  if (e && e.keyCode == 13) {
    //showNotificationMsg('alert', str);
    LoginRequest(fileObj, str, frm);
  }
}
function LoginRequest(fileObj, str, formid) {
  var v = str;
  var frm = fileObj.form;
  var nid_reg = document.forms[formid]["nid_reg"].value;
  if (nid_reg == null || nid_reg == "") {
    showNotificationMsg('alert', "User ID must be filled out");
    document.forms[formid]["nid_reg"].focus();
    return false;
  }

  if (v == "Login") {
    var password = document.forms[formid]["password"].value;
    if (password == null || password == "") {
      showNotificationMsg('alert', "password must be filled out");
      document.forms[formid]["password"].focus();
      return false;
    }
    else {
      var pwd = new Array();
      pwd = password;
      pwd = pwd.replace(/&/g, "replace_with_and");
      pwd = pwd.replace(/#/g, "replace_with_hash");
      pwd = pwd.replace(/\+/g, "replace_with_add");
      pwd = pwd.replace(/%/g, "replace_with_per");
    }
    authentication(nid_reg, pwd, frm);
  }
  else if(v == "Register"){
    userRegistration(nid_reg, frm);
  }
}
function authentication(nid_reg, password, frm) {
  if (window.XMLHttpRequest)
    xmlhttp = new XMLHttpRequest();
  else
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  xmlhttp.onreadystatechange = function () {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
      var res = xmlhttp.responseText;
      if (res.length > 1)
        var res = res.substr(res.length - 1, 1);
      if (res == 1) {
        _login();
        location.href = "home.php";
      }
          else 
        showNotificationMsg('failed', "Incorrect User Id or password.");
    }
  }
  xmlhttp.open("GET", "modules/auth/authentication.php?nid_reg=" + nid_reg + "&password=" + password + "&auth_ph=" + auth_ph + "&ph=" + ph, true);
  xmlhttp.send();
}

function registerNow() {
  let tdata = '<div style="display:flex; justify-content:center"><form method="post" name="password_reset_form" action="">\
  <div class="loginModuleStyle"><div style="font-size:20px;padding-left: 20px;">User Registration</div>\
  <hr style="background-color:darkgray;width:90%;">\
  <div id="fcoder-registration-panel" style="width:90%;display:flex;flex-direction:column;align-self:flex-end;">\
  <div class="loginInputFieldStyle"><div>Email ID:</div><div><input class="roundCornerInput" placeholder="Enter Email ID" style="margin-right:50px" type="text" id="fdrive-login-uid" onfocus="this.classList.remove(\'errorinput\')" name="nid_reg" onKeyup="check_submit(event,this,\'Login\', \'password_reset_form\')"/></div></div>\
  <div class="loginInputFieldStyle" style="justify-content:center;align-self:flex-end;"> <input class="login-button-style" type="button" value="Register" onclick="LoginRequest(this,this.value, \'password_reset_form\')"></div>\
  </div>\
  <div class="last-login-link">\
  [<span style="color:darkblue;cursor:pointer" onclick="event.stopPropagation();logintoHelpdesk();">Back to Login</span>]\
  </div>\
  </div></form></div>';
  document.getElementById("menu-index-page").innerHTML = tdata;
  document.getElementById("fdrive-login-uid").select();
}
function userRegistration(nid_reg, frm){
  let act= document.getElementById('fcoder-registration-panel');
  if (window.XMLHttpRequest)
    xmlhttp = new XMLHttpRequest();
  else
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  xmlhttp.onreadystatechange = function () {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
      var res = JSON.parse(xmlhttp.responseText);
      if(res['opts']['status']){
        act.innerHTML= res['opts']['msg'];
        act.style.alignSelf="center";
      } 
      else{
        var errors = res['errors'];
        showErrorMsg( errors);
      }
    }
  }
  xmlhttp.open("GET", "modules/auth/userRegistration.php?email_id=" + nid_reg + "&auth_ph=" + auth_ph + "&ph=" + ph, true);
  xmlhttp.send();
}
function logintoHelpdesk(){
  let tdata = '<div style="display:flex; justify-content:center"><form method="post" name="login_form" action="home.php">\
  <div class="loginModuleStyle"><div style="font-size:20px;padding-left: 20px;">Login to Fork Drive</div>\
  <hr style="background-color:darkgray;width:90%;">\
  <div class="loginInputFieldStyle"><div>User ID:</div>\
  <div><input class="roundCornerInput" onfocus="this.classList.remove(\'errorinput\')" placeholder="Enter Domain ID" style="margin-right:50px" type="text" id="hdesk-login-uid" name="nid_reg" onKeyup="check_submit(event,this,\'Login\', \'login_form\')"/></div></div>\
  <div class="loginInputFieldStyle"><div>Password:</div>\
  <div><input class="roundCornerInput" placeholder="Enter Domain Password" style="margin-right:50px" type="password" id="hdesk-login-psw" name="password" onKeyup="check_submit(event,this,\'Login\', \'login_form\')"></div></div>\
  <div class="loginInputFieldStyle" style="justify-content:center;align-self:flex-end;"> <input class="login-button-style" type="button" value="Login" name="login" onclick="LoginRequest(this,this.value,\'login_form\')"></div>\
  </div></form></div>';
  document.getElementById("menu-index-page").innerHTML = tdata;
  document.getElementById("hdesk-login-uid").select();
}
function profileUpdateUserinfo() {
  let actype = document.getElementById('userinfo_actype').value;
  let name = document.getElementById('userinfo_name').value;
  let userid = document.getElementById('userinfo_uderid').value;
  let contactno = document.getElementById('userinfo_contact_no').value;
  let emailid = document.getElementById('userinfo_email_id').value;
  let password = document.getElementById('userinfo_password').value;
  let confirm_password = document.getElementById('userinfo_confirm_password').value;

  if (window.XMLHttpRequest) {
    xmlhttp = new XMLHttpRequest();
  }
  else {
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange = function () {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
      var res = JSON.parse(xmlhttp.responseText);
      if (res['opts']['success'] == true) {
        window.location.href = "home.php";
      }
      else
        showNotificationMsg('failed', res['opts']['msg']);
    }
  }
  xmlhttp.open("GET", "profile_update.php?actype="+actype+"&name=" + name + "&userid=" + userid + "&password=" + password + "&confirm_password=" + confirm_password + "&contact_no=" + contactno + "&email_id=" + emailid + "&auth_ph=" + auth_ph, true);
  xmlhttp.send();
}
var checkpasswd = function() {
  let msgid = document.getElementById('userinfo_message');
  if (document.getElementById('userinfo_password').value ==
    document.getElementById('userinfo_confirm_password').value) {
    msgid.style.color = 'green';
    msgid.innerHTML = 'matching';
  } else {
    msgid.style.color = 'red';
    msgid.innerHTML = 'not matching';
  }
}
function logout() {
  var xmlhttp;
  if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
  }
  else {// code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  }

  xmlhttp.onreadystatechange = function () {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
      var res = xmlhttp.responseText;
      _logout();
      sessionStorage.clear();
      localStorage.clear();
      window.location.replace("index.php?logout=1");
    }
  }
  xmlhttp.open("GET", "logout.php?auth_ph=" + auth_ph + "&ph=" + ph, true);
  xmlhttp.send();
}