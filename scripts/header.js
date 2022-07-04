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
  };
})(window);
document.addEventListener('DOMContentLoaded', function () {
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
  document.getElementById('mainContentDiv').style.height = (document.documentElement.clientHeight - 130);
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
  xmlhttp.open("GET", "/modules/auth/chk_session.php?id=" + id + "&auth_ph=" + auth_ph, false);
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
// document.addEventListener('mousemove', function (event) {
//   event.preventDefault();
//   if (typeof webdriveModule !== "undefined" && webdriveModule.active != null && webdriveModule.active == true && webdriveModule.isDown) {
//     webdriveModule.mousePosition = {
//       x: event.clientX,
//       y: event.clientY
//     };
//     webdriveModule.targetDiv.style.left = (webdriveModule.mousePosition.x + webdriveModule.offset[0]) + 'px';
//     webdriveModule.targetDiv.style.top = (webdriveModule.mousePosition.y + webdriveModule.offset[1]) + 'px';
//   }
// }, true);
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
  event.stopPropagation();
  if (event.keyCode == 27) {

  }
  if (event.keyCode == 13) {

  }
  // console.log(event);
};
function showNotificationMsg(type, notification) {
  let color = 'dimgrey';
  if (type == 'succeed') color = 'seagreen';
  else if (type == 'failed') color = 'dimgrey';
  else if (type == 'alert') color = 'darkyellow';
  var errordiv = document.getElementById('msgDisplayDiv');
  errordiv.innerHTML = '<div class="errormsgstyle" style="background:' + color + ' !important"><span> ' + notification + '</span> <img src="/images/close.png" onclick="this.parentNode.parentNode.removeChild(this.parentNode);"></div>';
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
    tdata += '<div class="errormsgstyle"><span> ' + errors['msg'][i] + '</span> <img src="/images/close.png" onclick="this.parentNode.parentNode.removeChild(this.parentNode);"></div>';
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

function exitHelpDetail() {
  document.getElementById('helpMsgDisplayDiv').style.display = 'none';
}
function createTlUnit(xmlhttp, action, name) {

  document.getElementById('operationActionStatus').style.visibility = 'visible';
  document.getElementById('operationActionStatus').style.opacity = '1';
  document.getElementById("developedbyCredit").style.display = 'none';

  let id = new Date().valueOf();
  let lcut = 20, tcut, shortname = name;
  let sdata = '';
  if (action == 'upload' || action == 'download') {
    lcut = 13;
    sdata = '<span style="color:indigo;margin:auto 3px" id="tl-unit-' + id + '-status"></span> [<span style="color:orange;white-space:nowrap" id="tl-unit-' + id + '-statusBar">0 KB</span>]';
  }
  if (name.length > lcut) {
    tcut = Math.min(lcut, name.lastIndexOf(" "));
    shortname = name.substr(0, tcut == -1 ? lcut : tcut) + "...";
  }

  let tdata = '<div class="tl-unit" id="tl-unit-' + id + '">\
  <div class="tl-unit-info">';
  tdata += '<img id="tl-unit-' + id + '-img" src="images\\webdrive\\' + action + '.png"/>';
  tdata += '<div class="progress"  data-label="' + shortname + '">\
  <span id="tl-unit-' + id + '-progressBar" class="value" style="width:0%;"></span></div>\
  </div>'+ sdata + '</div>';
  previousTlDtl = previousTlDtl + tdata;

  document.getElementById('operationActionStatus').innerHTML = previousTlDtl;

  document.getElementById('tl-unit-' + id + '-img').addEventListener('click', function (event) {
    xmlhttp.abort();
    document.getElementById('tl-unit-' + id).style = 'color:lightred';
    document.getElementById('tl-unit-' + id + '-img').src = 'images\\webdrive\\failed.png';
    showNotificationMsg('failed', 'Operation has been Cancelled by User.');
    previousTlDtl = document.getElementById('operationActionStatus').innerHTML;
    if (action == 'upload' || action == 'download') {
      setTimeout(function () {
        deleteTlUnit(id);
      }, 10000);
    }
  });
  xmlhttp.addEventListener("load", function (event) { completeHandler(event, action, id) }, false);
  // if (action == 'upload')
  //   xmlhttp.upload.addEventListener("progress", function (event) { progressUploadHandler(event, action, id) }, false);
  // else
  xmlhttp.addEventListener("progress", function (event) { progressHandler(event, action, id) }, false);

  xmlhttp.addEventListener("error", function (event) { abortHandler(event, action, id) }, false);
  xmlhttp.addEventListener("abort", function (event) { abortHandler(event, action, id) }, false);
  return id;
}
function deleteTlUnit(id) {
  document.getElementById('operationActionStatus').removeChild(document.getElementById('tl-unit-' + id));
  previousTlDtl = document.getElementById('operationActionStatus').innerHTML;
  if (previousTlDtl == "") {
    document.getElementById('operationActionStatus').style.visibility = 'hidden';
    document.getElementById('operationActionStatus').style.opacity = '0';
    document.getElementById("developedbyCredit").style.display = 'flex';
  }
}
function progressUploadHandler(id, total, part) {
  let bpc = webdriveModule.getBytesPerChunk();
  if (part == 'undefined' || part == null || part == '') part = 0;
  var percent = 0, et = total;
  percent = part / total * 100;
  document.getElementById('tl-unit-' + id + '-statusBar').innerHTML = showFileSizeInBytes(part * bpc);
  document.getElementById('tl-unit-' + id + '-progressBar').style.width = Math.round(percent);
  document.getElementById('tl-unit-' + id + '-status').innerHTML = Math.round(percent) + "%";
}
function progressHandler(evt, action, id) {
  var percent = 0, et = evt.total;

  if (evt.total == 0) et = evt.loaded;
  percent = evt.loaded / et * 100;
  if (action == 'upload' || action == 'download') {
    document.getElementById('tl-unit-' + id + '-statusBar').innerHTML = showFileSizeInBytes(evt.loaded);
    document.getElementById('tl-unit-' + id + '-progressBar').style.width = Math.round(percent);
    document.getElementById('tl-unit-' + id + '-status').innerHTML = Math.round(percent) + "%";
  }
  else if (action == 'compress') {
    if (evt.lengthComputable == true) {
      var total = evt.getResponseHeader('content-length');
      var encoding = evt.getResponseHeader('content-encoding');
      if (total && encoding && (encoding.indexOf('gzip') > -1)) {
        total *= 4;
        percent = Math.min(100, evt.loaded / total * 100)
      } else {
        console.log('lengthComputable failed')
      }
    }
  }
}
function abortHandler(evt, action, id) {
  document.getElementById('tl-unit-' + id + '-img').src = 'images\\webdrive\\failed.png';
  document.getElementById('tl-unit-' + id).title = 'Failed.';
  previousTlDtl = document.getElementById('operationActionStatus').innerHTML;
}
function completeHandler(evt, action, id) {
  previousTlDtl = document.getElementById('operationActionStatus').innerHTML;
}

function showFileSizeInBytes(size) {
  var fSExt = new Array('B ', 'KB', 'MB', 'GB'), i = 0;
  while (size > 900) { size /= 1024; i++; }
  var exactSize = (Math.round(size * 100) / 100) + ' ' + fSExt[i];
  return exactSize;
}
function check_submit(e, frm, btn) {
  if (e.keyCode == 13){
    e.stopPropagation();
    document.forms[frm][btn].click()
  }
}
function LoginRequest(req) {
  var login_id = document.forms["generalCardForm"]["fcoder-login-uid"].value;
  if (login_id == null || login_id == "") {
    showNotificationMsg('alert', "User ID must be filled out");
    document.forms["generalCardForm"]["fcoder-login-uid"].classList.add("errorinput");
    return false;
  }
  if (req== "Login") authentication(login_id, document.forms["generalCardForm"]["fcoder-login-psw"].value);
  else if (req== "Register") userRegistration(login_id);
  else if (req== "Get Link") getResetLink(login_id);
}
function authentication(login_id, password) {
  var url = "/modules/auth/authentication.php";
  var xmlhttp = "";  if (window.XMLHttpRequest)
    xmlhttp = new XMLHttpRequest();
  else
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  xmlhttp.open('POST', url, true);
  xmlhttp.timeout = 300000;
  xmlhttp.ontimeout = function (e) {
    showNotificationMsg('alert', "Timed-out. Kindly try again.");
  };
  var formData = new FormData();
  formData.append('fcoder-login-uid', login_id);
  formData.append('fcoder-login-psw', password);
  formData.append('auth_ph', auth_ph);
  formData.append('ph', ph);
  xmlhttp.onreadystatechange = function () {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
      var res = JSON.parse(xmlhttp.responseText);
      if (res['opts']['status'] == true) {
        _login();
        location.href = "index.php";
      }
      else {
        var errors = res['errors'];
        if (errors['total']>0)
          showErrorMsg(errors);
        else
          showNotificationMsg('failed', res['opts']['msg']);
      }
    }
  }
  xmlhttp.send(formData);
}
function imageUpload(token, imageUploadElement) {
  if (typeof imageUploadElement === "undefined" || imageUploadElement.value === "") {
    showNotificationMsg('alert', "Failed to Upload Image. Please try again.");
  }
  else {
    var formData = new FormData();
    formData.append('imageUpload', imageUploadElement.files[0]);
    formData.append('token', token);
    formData.append('auth_ph', auth_ph);
    formData.append('ph', ph);
    var filesize = imageUploadElement.files[0].size;
    if (filesize < 204800) {
      var url = '/modules/profile/profile_upload_image.php';
      var xhrRequest = "";
      if (window.XMLHttpRequest)
        xhrRequest = new XMLHttpRequest();
      else
        xhrRequest = new ActiveXObject("Microsoft.XMLHTTP");
      xhrRequest.open('POST', url, true);
      xhrRequest.timeout = 300000;
      xhrRequest.ontimeout = function (e) {
        showNotificationMsg('alert', "Image Upload Timed-out. Kindly try to upload image again.");
      };
      xhrRequest.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          var res = JSON.parse(this.responseText);
          let errors = res['errors'];
          let totalerrors = errors['total'];
          if (totalerrors == 0) {
            if (res['opts']['status'] == true)
              document.getElementById("logged-user-img-id").src = res['logged-user-img'] + '?' + new Date().getTime();
            else
              showNotificationMsg('failed', res['opts']['msg']);
          }
          else
            showErrorMsg(errors);
        }
      };
      xhrRequest.send(formData);
    }
    else {
      showNotificationMsg('failed', 'Please upload Image in size less or equal to 200KB.');
    }
  }
}
function registerNow() {
  let tdata = '<div style="display:flex; justify-content:center"><form name="generalCardForm">\
  <div class="cardModuleStyle">\
  <div class="headline">User Registration</div>\
  <hr style="background-color:darkgray;width:90%;">\
  <div id="fcoder-registration-panel" style="display:flex;flex-direction:column;align-self:flex-end;">\
  <div class="loginInputFieldStyle"><div>Email ID:</div><div><input class="roundCornerInput" placeholder="Enter Email ID"  type="text" id="fdrive-login-uid" onfocus="this.classList.remove(\'errorinput\')" name="fcoder-login-uid" onKeyup="check_submit(event,\'generalCardForm\',\'registerButton\')"/></div></div>\
  <div class="loginInputFieldStyle" >\
  <span onClick="logintoHelpdesk()" class="backlinks">Back to Login</span>\
  <input class="login-button-style" type="button" value="Register" name="registerButton"  onclick="LoginRequest(this.value)">\
  </div>\
  </div>\
  </div></form></div>';
  document.getElementById("menu-index-page").innerHTML = tdata;
  document.getElementById("fdrive-login-uid").select();
}
function passwordRecovery() {
  let tdata = '<div style="display:flex; justify-content:center"><form name="generalCardForm">\
  <div class="cardModuleStyle">\
  <div class="headline">Password Recovery</div>\
  <hr style="background-color:darkgray;width:90%;">\
  <div id="fcoder-registration-panel" style="display:flex;flex-direction:column;align-self:flex-end;">\
  <div class="loginInputFieldStyle"><div>Email ID:</div><div><input class="roundCornerInput" placeholder="Enter Email ID" type="text" id="fdrive-login-uid" onfocus="this.classList.remove(\'errorinput\')" name="fcoder-login-uid"  onKeyup="check_submit(event,\'generalCardForm\',\'getlinkButton\')"/></div></div>\
  <div class="loginInputFieldStyle" >\
  <span onClick="logintoHelpdesk()" class="backlinks">Back to Login</span>\
  <input class="login-button-style" type="button" value="Get Link"  name="getlinkButton" onclick="LoginRequest(this.value)">\
  </div>\
  </div>\
  </div></form></div>';
  document.getElementById("menu-index-page").innerHTML = tdata;
  document.getElementById("fdrive-login-uid").select();
}
function userRegistration(login_id) {
  let act = document.getElementById('fcoder-registration-panel');
  if (window.XMLHttpRequest)
    xmlhttp = new XMLHttpRequest();
  else
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  xmlhttp.onreadystatechange = function () {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
      var res = JSON.parse(xmlhttp.responseText);
      if (res['opts']['status']) {
        act.innerHTML = res['opts']['msg'];
        act.style.alignSelf = "center";
      }
      else {
        var errors = res['errors'];
        showErrorMsg(errors);
      }
    }
  }
  xmlhttp.open("GET", "/modules/auth/userRegistration.php?email_id=" + login_id + "&auth_ph=" + auth_ph + "&ph=" + ph, true);
  xmlhttp.send();
}
function getResetLink(login_id) {
  let act = document.getElementById('fcoder-registration-panel');
  if (window.XMLHttpRequest)
    xmlhttp = new XMLHttpRequest();
  else
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  xmlhttp.onreadystatechange = function () {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
      var res = JSON.parse(xmlhttp.responseText);
      if (res['opts']['status']) {
        act.innerHTML = res['opts']['msg'];
        act.style.alignSelf = "center";
      }
      else {
        var errors = res['errors'];
        if(errors['total']>0)
        showErrorMsg(errors);
        else 
        showNotificationMsg('failed', res['opts']['msg']);
      }
    }
  }
  xmlhttp.open("GET", "/modules/auth/getResetLink.php?email_id=" + login_id + "&auth_ph=" + auth_ph + "&ph=" + ph, true);
  xmlhttp.send();
}
function logintoHelpdesk() {
  let tdata = '<form method="post" name="generalCardForm" action="index.php">\
  <div class="cardModuleStyle">\
  <div class="headline">Sign In</div>\
  <hr style="background-color:darkgray;width:90%;margin:5px">\
  <div class="loginInputFieldStyle">\
  <div>User ID:</div>\
  <div><input class="roundCornerInput" onfocus="this.classList.remove(\'errorinput\')" placeholder="Email or User ID"  type="text" id="fcoder-login-uid" name="fcoder-login-uid" onKeyup="check_submit(event,\'generalCardForm\',\'loginButton\')" /></div>\
  </div>\
  <div class="loginInputFieldStyle">\
  <div>Password:</div>\
  <div><input class="roundCornerInput" onfocus="this.classList.remove(\'errorinput\')" placeholder="Type Password" type="password" id="fcoder-login-psw" name="fcoder-login-psw" onKeyup="check_submit(event,\'generalCardForm\',\'loginButton\')"></div>\
  </div>\
  <div class="loginInputFieldStyle" >\
  <span class="backlinks" onClick="passwordRecovery()" >Forget Password</span>\
  <input class="login-button-style"   type="button" value="Login" name="loginButton" onclick="LoginRequest(this.value);">\
  </div>\
  </div>\
  </form>';
  document.getElementById("menu-index-page").innerHTML = tdata;
  document.forms["generalCardForm"]["fcoder-login-uid"].select();
}
function profileUpdateUserinfo() {

  var url = '/modules/profile/profile_update.php?auth_ph=' + auth_ph;
  var xhrRequest = "";
  if (window.XMLHttpRequest)
    xhrRequest = new XMLHttpRequest();
  else
    xhrRequest = new ActiveXObject("Microsoft.XMLHTTP");
  xhrRequest.open('POST', url, true);
  xhrRequest.timeout = 300000;
  xhrRequest.send(new FormData(document.getElementById('form-user-update-self')));
  xhrRequest.onreadystatechange = function () {
    if (xhrRequest.readyState == 4 && xhrRequest.status == 200) {
      var res = JSON.parse(xhrRequest.responseText);
      let errors = res['errors'];
      let totalerrors = errors['total'];
      if (totalerrors == 0) {
        if (res['opts']['status'] == true) {
          _login();
          location.href = "index.php";
        }
        else
          showNotificationMsg('failed', res['opts']['msg']);
      }
      else
        showErrorMsg(errors);
    }
  }
}
function profileResetPassword() {

  var url = '/modules/profile/profile_reset_password.php?auth_ph=' + auth_ph;
  var xhrRequest = "";
  if (window.XMLHttpRequest)
    xhrRequest = new XMLHttpRequest();
  else
    xhrRequest = new ActiveXObject("Microsoft.XMLHTTP");
  xhrRequest.open('POST', url, true);
  xhrRequest.timeout = 300000;
  xhrRequest.send(new FormData(document.getElementById('form-user-update-self')));
  xhrRequest.onreadystatechange = function () {
    if (xhrRequest.readyState == 4 && xhrRequest.status == 200) {
      var res = JSON.parse(xhrRequest.responseText);
      let errors = res['errors'];
      let totalerrors = errors['total'];
      if (totalerrors == 0) {
        if (res['opts']['status'] == true) {
          _login();
          location.href = "index.php";
        }
        else
          showNotificationMsg('failed', res['opts']['msg']);
      }
      else
        showErrorMsg(errors);
    }
  }
}
var checkpasswd = function () {
  let msgid = document.getElementById('userinfo_message');
  if (document.getElementById('userinfo_password').value ==
    document.getElementById('userinfo_confirm_password').value) {
    msgid.style.color = 'green';
    msgid.innerHTML = 'Matched';
  } else {
    msgid.style.color = 'red';
    msgid.innerHTML = 'Mismatch';
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
  xmlhttp.open("GET", "/logout.php?auth_ph=" + auth_ph + "&ph=" + ph, true);
  xmlhttp.send();
}
function exitSuperModal() {
  let divs = document.getElementsByClassName('modal');
  for (let i = 0; i < divs.length; i++) {
    if (navigator.userAgent.match(/MSIE 8/) !== null) {
      divs[i].style.opacity = "0";
      divs[i].style.filter = 'alpha(opacity=0)';
    }
    divs[i].style.display = 'none';
  }
  document.getElementById("supermodal").style.display = "none";
}

function displaySuperModal(el) {
  let sm = document.getElementById(el);
  let divs = document.getElementsByClassName('modal');
  for (let i = 0; i < divs.length; i++) {
    if (navigator.userAgent.match(/MSIE 8/) !== null) {
      divs[i].style.opacity = "0";
      divs[i].style.filter = 'alpha(opacity=0)';
    }
    divs[i].style.display = 'none';
  }
  if (navigator.userAgent.match(/MSIE 8/) !== null) {
    sm.style.opacity = "0.2";
    sm.style.filter = 'alpha(opacity=20)';
  }
  sm.style.display = 'flex';
  document.getElementById("supermodal").style.display = "flex";
}