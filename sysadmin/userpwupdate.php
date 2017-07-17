<?php
//include PDO and PHPAuth requirements
require("../config.php");
//Session Check - If not logged in redirect to Login.php
if (!$auth->isLogged()) {
    echo "Forbidden";
    //header('Location: ../login.php');
	echo "<script>top.location.href = \"../logout.php\";</script>"; //escape iframe
	exit();
}
//Session Expiration Update
$hash=$auth->getSessionHash();
$expire = date("Y-m-d H:i:s", strtotime("+30 minutes"));
$sessresult = $dbh->prepare('UPDATE sessions SESS SET SESS.expiredate=? WHERE SESS.hash=?');
$sessresult->execute([$expire,$hash]);

//Boot if users permissions do not allow
	
	if ($isadmin==0) {
    echo "Forbidden";
    //header('Location: ../login.php');
	echo "<script>top.location.href = \"../logout.php\";</script>"; //escape iframe
	exit();
}


$uid = $_POST["uid"];
$newpassword=$_POST["newpassword"];
$newpassword2=$_POST["newpassword2"];

if ($newpassword2!=$newpassword){ 
	echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><a class=\"btn btn-primary\" href='userpwreset.php?edit=$uid'><i>Back</i></a></div></div></div>";
	echo "Passwords do not match!";
	return;}
	
if ($newpassword=='' || strlen($newpassword) < 4 ){ 
	echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><a class=\"btn btn-primary\" href='userpwreset.php?edit=$uid'><i>Back</i></a></div></div></div>";
	echo "Password Invalid!";
	return;}

	

$pword = $auth->getHash($newpassword);
//echo "$pword";
$result = $dbh->prepare('UPDATE `users` SET `password`=? WHERE id=?');
$result->execute([$pword,$uid]);
header("Location: sysadmin.php");
?>