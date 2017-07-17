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
<head><title>User Edit</title>
<link rel="stylesheet" href="../scripts/bs/css/bootstrap.min.css">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />  <!--forces IE to prevent compatibility mode!!-->
<script src="../scripts/jquery.min.js"></script>
  <script src="../scripts/bs/js/bootstrap.min.js"></script>
</head>
<style>
</style>
<body>

<div class="well" id="usereditwell">

<?php

include '../config.php';

$uid = $_GET["edit"];
echo "<form action='userupdate.php' method='post'>";  


// MySQL connect
$result = $dbh->prepare(
'SELECT id, email, isactive, isedit, isadmin FROM users WHERE id=?');
$result->execute([$uid]);
if (!$result) {    die("Query to show fields from table failed.");}

		
		//Loop through Query results and populate
		foreach ($result as $row)
		{
		
			echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><a class=\"btn btn-primary\" href='sysadmin.php'><i>Back</i></a><h4>Edit user: ".$row['email']."</h4></div></div></div>";
			
			echo "
<div class=\"row\">
<div class=\"col-md-6\">
<div class=\"form-group\">
<a class=\"btn btn-danger\" href='userdelete.php?id=$uid'>Delete User</a>
<input type='hidden' name='uid' value='$uid'>
<input  class=\"btn btn-warning\" type = 'submit' name = 'submitedit' value = 'Save Changes'>
</div>
</div>
</div>
";
			
			//Username
			echo "<div class=\"row\"><div class=\"col-md-6\"><label for=\"newusername\">Username:<input type='text' id=\"newusername\" name='newusername' value=\"".$row['email']."\"></label></div>";
			
			//View
			echo "<div class=\"col-md-6\"><div class=\"col-md-4\"><label class=\"checkbox-inline\" for=\"newisview\">";
			if ($row['isactive'] == '1'){
			echo "<input type='checkbox'  id=\"newisview\" name='newisview' value=\"1\" checked>";
			}
			else {
			echo "<input type='checkbox' id=\"newisview\" name='newisview' value=\"1\">";
			}
			echo ":View </label></div>";
			//Edit
			echo "<div class=\"col-md-4\"><label class=\"checkbox-inline\" for=\"newisedit\">";
			if ($row['isedit'] == '1'){
			echo "<input type='checkbox' id=\"newisedit\" name='newisedit' value=\"1\" checked>";
			}
			else {
			echo "<input type='checkbox' id=\"newisedit\" name='newisedit' value=\"1\">";
			}
			echo ":Edit </label></div>";
			
			//Admin
			echo "<div class=\"col-md-4\"><label class=\"checkbox-inline\" for=\"newisadmin\">";
			if ($row['isadmin'] == '1'){
			echo "<input type='checkbox' id=\"newisadmin\" name='newisadmin' value=\"1\" checked>";
			}
			else {
			echo "<input type='checkbox' id=\"newisadmin\" name='newisadmin' value=\"1\">";
			}
			echo "<span class=\"glyphicon glyphicon-alert\"></span>:Admin </label></div></div></div>";
		}
		

echo "</form>";

?>
</body></html>