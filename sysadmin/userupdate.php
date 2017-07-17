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
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Tab Update</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />  <!--forces IE to prevent compatibility mode!!-->
<link rel="stylesheet" href="../scripts/bs/css/bootstrap.min.css">
<script src="../scripts/jquery.min.js"></script>
<script src="../scripts/bs/js/bootstrap.min.js"></script>
</head>
<body>
<?php

include '../config.php';

if (isset($_POST["uid"])){$uid=$_POST["uid"];}
if (isset($_POST["newusername"])){$newusername=$_POST["newusername"];}
if (isset($_POST["newisview"])){$newisview=$_POST["newisview"];}else{$newisview='0';}
if (isset($_POST["newisedit"])){$newisedit=$_POST["newisedit"];}else{$newisedit='0';}
if (isset($_POST["newisadmin"])){$newisadmin=$_POST["newisadmin"];}else{$newisadmin='0';}


// MySQL connect

$result = $dbh->prepare(
'UPDATE `users`
SET
`email`=?,
`isactive`=?, 
`isedit`=?, 
`isadmin`=?
WHERE `id`=?');

$result->execute([
$newusername,
$newisview,
$newisedit,
$newisadmin,
$uid]
);
//var_dump($_POST);
header("Location: sysadmin.php");


?>
</center>
</body>
</html>