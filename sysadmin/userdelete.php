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
<head><title>Delete Tab</title>
<link rel="stylesheet" href="../scripts/bs/css/bootstrap.min.css">
<script src="../scripts/jquery.min.js"></script>
  <script src="../scripts/bs/js/bootstrap.min.js"></script>
</head>
<body>
<?php

include '../config.php';

$uid = $_GET["id"];

// MySQL connect
$result = $dbh->prepare('DELETE FROM `users` WHERE `id` = ?');
$result->execute([$uid]);

header("Location: sysadmin.php");

?>
</center>
</body>
</html>