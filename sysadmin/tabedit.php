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
<head><title>Applications Inventory - Analyst Edit</title>
<link rel="stylesheet" href="../scripts/bs/css/bootstrap.min.css">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />  <!--forces IE to prevent compatibility mode!!-->
<script src="../scripts/jquery.min.js"></script>
  <script src="../scripts/bs/js/bootstrap.min.js"></script>
</head>
<style>
</style>
<body>

<div class="well" id="tabeditwell">

<?php

include '../config.php';

$tabid = $_GET["edit"];
echo "<form action='tabupdate.php' method='post'>";  


// MySQL connect
$result = $dbh->prepare(
'SELECT 
`id`
,`name`
,`title`
,`position`
,`notes`
FROM `tabs` 
WHERE `id`=?');
$result->execute([$tabid]);
if (!$result) {    die("Query to show fields from table failed.");}
		
		//Loop through Query results and populate
		foreach ($result as $row)
		{
		
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><a class=\"btn btn-primary\" href='sysadmin.php'><i>Back</i></a><h4>Edit Tab: ".$row['name']."</h4></div></div></div>";
		
		echo "
<div class=\"row\">
<div class=\"col-md-6\">
<div class=\"form-group\">
<a class=\"btn btn-danger\" href='tabdelete.php?id=$tabid'>Delete Tab</a>
<input type='hidden' name='tabid' value='$tabid'>
<input  class=\"btn btn-warning\" type = 'submit' name = 'submitedit' value = 'Save Changes'>
</div>
</div>
</div>
";
		
		//Name
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newtabname\">Name:</label><input type='text' required=\"required\" pattern=\"[a-zA-Z0-9-]+\" class='form-control tabtext' id='newtabname' name='newtabname' value=\"".$row['name']."\"></div></div>";
		
		//Title
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newtabtitle\">Title:</label><input type='text' required=\"required\" class='form-control tabtext' id='newtabtitle' name='newtabtitle' value=\"".$row['title']."\"></div></div></div>";	
		
		//Position
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newtabposition\">Position:</label><input type='number' pattern=\"[0-9]+\" class='form-control tabtext' id='newtabposition' name='newtabposition' value=\"".$row['position']."\"></div></div>";	
		
		//Notes
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newtabnotes\">Notes:</label><textarea class='form-control tabtextarea' id='newtabnotes' name='newtabnotes'>".$row['notes']."</textarea></div></div></div>";
		}


echo "</form>";

?>
</body></html>