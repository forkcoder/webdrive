<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/dbproxy/dbconnect.php');
$u_userid=$_GET['nid_reg'];
$password=$_GET['password'];

$lk = isset($_SESSION['login_key'])?$_SESSION['login_key']:uniqid();
if($_GET['auth_ph']=="" || $_GET['auth_ph'] != $lk)
{
                //print_r($_SESSION); die();
                                //echo "topAD";
        header("Location:/index.php");
                die();
}
else if($_GET['auth_ph'] == $_SESSION['login_key'])
{
        //echo "midAD";
                $_SESSION['login_key'] = $_GET['auth_ph'];
        //unset($_SESSION['login_key']);

$res=2;
if (!isset($_SESSION['bbank_userid']))
{
include "ldap.php";

$password=str_ireplace("replace_with_and","&",$password);
$password=str_ireplace("replace_with_hash","#",$password);
$password=str_ireplace("replace_with_add","+",$password);
$password=str_ireplace("replace_with_per","%",$password);


$res="";

/*************************************************************/
//$catagory=$_GET['catagory'];
//$p=md5($password);
/****************************** Will implement later********************************
$login_date_time="";
//function login_date_time()
//{
//$login_date_time="";
$b = time ();
$d=date("Y-m-d h:i:s",$b);
$login_date_time=$d;
$login_date_time=date("Y-m-d");
$login_date_time= $login_date_time." ".date("h:i:sa");   // s=leading 0 at second,a =am/pm
//}
*/

/***************************** live on domain*******************************/
$ldap = new LDAP("ldap://10.200.10.31","389", "bb.org.bd", $u_userid,$password);
//$ldap = new LDAP("ldap://10.200.10.31","389", "bb.org.bd", $u_userid,$password);
$user= new User($u_userid,$password);
$res=$ldap->authenticate($user);

$temp=$res;

if(strlen($temp)>1)
$res= substr($temp, strlen($temp)-1, 1);



//$_SESSION['bbank_name']="Debabrata Das";      //wil be removed later
//$_SESSION['bbank_genid']="18305912";  //wiil be removed later
//$res=1;                              //wiil be removed later



if($res==1)
{
$login_key = $_SESSION['login_key'];
unset($_SESSION['login_key']);
$_SESSION['bbank_userid']=$u_userid;  //will be removed

$_SESSION['bbank_name']=$user->name;
$_SESSION['bbank_genid']=$user->description;

                //$sql = "INSERT INTO `bbank_access_log` (`u_id`,`date`,`time`,`access_status`)VALUES('$u_userid','$d','$t','login')";
                        $sql = "INSERT INTO `bbank_access_log` (`u_id`,`login_at`,`access_status`, `login_key`)VALUES('$u_userid',NOW(),'login', '$login_key')";
                        mysqli_query($con, $sql) or die ("could not inserted to access log");


                   //add session
                   //$_SESSION["phash"]=$ph;


                   //$session_hash= hash('sha1', 'Dont try to illegal access.');
                   //$sql = "INSERT INTO `page_hash` (`page_hash`)VALUES('$session_hash')";
                        //mysqli_query($con, $sql) or die ("could not inserted to page_hash Table");
//echo $password.$res;
echo $res;
}
//else echo  $password.$res;
else echo  $res;

/*************************************************************/
}
else
        {
                if ($_SESSION['bbank_userid']==$u_userid)
                        echo 2;
                else echo 3;
        }
}
else echo 4;
?>
