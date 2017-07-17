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
	
	if ($isedit==0) {
    echo "Forbidden";
    //header('Location: ../login.php');
	echo "<script>top.location.href = \"../logout.php\";</script>"; //escape iframe
	exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Viz Insert</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />  <!--forces IE to prevent compatibility mode!!-->
<link rel="stylesheet" href="../scripts/bs/css/bootstrap.min.css">
<script src="../scripts/jquery.min.js"></script>
  <script src="../scripts/bs/js/bootstrap.min.js"></script>
</head>
<body>
<?php

include '../config.php';

/*
name
title
tab
position
size
class
type
dbtype
dbhost
dbuser
dbpass
dbname
query
notes*/

$newvizname=$_POST["newvizname"];
$newviztitle=$_POST["newviztitle"];
$newviztab=$_POST["newviztab"];
$newvizposition=$_POST["newvizposition"];
$newvizsize=$_POST["newvizsize"];
$newvizclass=$_POST["newvizclass"];
$newviztype=$_POST["newviztype"];
$newvizdbtype=$_POST["newvizdbtype"];
$newvizdbhost=$_POST["newvizdbhost"];
$newvizdbuser=$_POST["newvizdbuser"];
$newvizdbpass=$_POST["newvizdbpass"];
$newvizdbname=$_POST["newvizdbname"];
$newvizquery=$_POST["newvizquery"];
$newvizheight=$_POST["newvizheight"];
$newvizdate=$_POST["newvizdate"];
$newviznotes=$_POST["newviznotes"];

if($newvizposition==''){$newvizposition=0;}


// MySQL connect

$result = $dbh->prepare('
INSERT INTO `visualizations` 
(`name`,
`title`,
`tab`,
`position`,
`size`,
`class`,
`type`,
`dbtype`,
`dbhost`,
`dbuser`,
`dbpass`,
`dbname`,
`query`,
`height`,
`dateformat`,
`notes`)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
$result->execute([$newvizname,
$newviztitle,
$newviztab,
$newvizposition,
$newvizsize,
$newvizclass,
$newviztype,
$newvizdbtype,
$newvizdbhost,
$newvizdbuser,
$newvizdbpass,
$newvizdbname,
$newvizquery,
$newvizheight,
$newvizdate,
$newviznotes
]);

//var_dump($_POST);
header("Location: sysadmin.php");

?>
</center>
</body>
</html>