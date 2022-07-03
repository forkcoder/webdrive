var adminModule = {
    updateUserData: function() {
    var formData = new FormData(document.getElementById('form-user-update-admin'));
    var url = '/admin/user_info_update.php?auth_ph='+auth_ph+'&ph='+ph;
    var xhrRequest = "";
    if (window.XMLHttpRequest)
      xhrRequest = new XMLHttpRequest();
    else
      xhrRequest = new ActiveXObject("Microsoft.XMLHTTP");
    xhrRequest.open('POST', url, true);
    xhrRequest.timeout = 300000;
    xhrRequest.send(formData);
    xhrRequest.onreadystatechange = function () {
      if (xhrRequest.readyState == 4 && xhrRequest.status == 200) {
        var res = JSON.parse(xhrRequest.responseText);
        let errors = res['errors'];
        let totalerrors = errors['total'];
        if (totalerrors == 0) {
          if (res['opts']['status'] == true) {
            showNotificationMsg('succeed', res['opts']['msg']);
          }
          else
            showNotificationMsg('failed', res['opts']['msg']);
        }
        else
          showErrorMsg(errors);
      }
    }
  },
  openCity:function (evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
  }
};