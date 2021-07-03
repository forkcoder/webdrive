<?php
session_start();
include "../dbproxy/dbconnect.php";
$u_userid=$_GET['nid_reg'];
$password=$_GET['password'];

/*****************************************
$data = json_decode(stripslashes($_POST['data']));

  // here i would like use foreach:

  foreach($data as $d){
     echo $d;
  }
*****************************************/

$lk = isset($_SESSION['login_key'])?$_SESSION['login_key']:uniqid();
if($_GET['auth_ph']=="" || $_GET['auth_ph'] != $lk)
{
        header("Location: /index.php");
                die();
}
else if($_GET['auth_ph'] == $_SESSION['login_key'])
{
                $_SESSION['login_key'] = $_GET['auth_ph'];

$res=2;
if (!isset($_SESSION['fcoder_userid']))
{
include "ldap.php";

$password=str_ireplace("replace_with_and","&",$password);
$password=str_ireplace("replace_with_hash","#",$password);
$password=str_ireplace("replace_with_add","+",$password);


$res="";
if($password=="bb")$res=1;
else $res=0;


/*****************************need to be active while live on domain*******************************/
/*
$ldap = new LDAP("ldap://10.36.200.50","389", "bb.org.bd", $u_userid,$password);
$user= new User($u_userid,$password);
$res=$ldap->authenticate($user);

$temp=$res;

if(strlen($temp)>1)
$res= substr($temp, strlen($temp)-1, 1);
*/


$_SESSION['fcoder_name']="Debabrata Das";      //wil be removed later
$_SESSION['fcoder_genid']="18305912";  //wiil be removed later
//$res=1;                              //wiil be removed later



if($res==1)
{
unset($_SESSION['login_key']);
$_SESSION['fcoder_userid']=$u_userid;  //will be removed



                //$sql = "INSERT INTO `fcoder_access_log` (`u_id`,`date`,`time`,`access_status`)VALUES('$u_userid','$d','$t','login')";
                        $sql = "INSERT INTO `fcoder_access_log` (`u_id`,`login_at`,`access_status`)VALUES('$u_userid',NOW(),'login')";
                        mysqli_query($con, $sql) or die ("could not inserted to access log");


echo $res;
//echo $password.$res;

}
else echo $res;
//else echo $password.$res;

/*************************************************************/
}
else
        {
                if ($_SESSION['fcoder_userid']==$u_userid)
                        echo 2;
                else echo 3;
        }
}
else echo 4;
?>
