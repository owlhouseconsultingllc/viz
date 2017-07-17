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
<head><title>User Password Reset</title>
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

//<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
echo "<input style=\"display:none\" type=\"text\" name=\"fakeusernameremembered\"/>
<input style=\"display:none\" type=\"password\" name=\"fakepasswordremembered\"/>";

$uid = $_GET["edit"];
echo "<form action='userpwupdate.php' method='post'>";  


// MySQL connect
$result = $dbh->prepare(
'SELECT id, email FROM users WHERE id=?');
$result->execute([$uid]);
if (!$result) {    die("Query to show fields from table failed.");}

//Loop through Query results and populate
		foreach ($result as $row)
		{		
echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><a class=\"btn btn-primary\" href='sysadmin.php'><i>Back</i></a><h4>Reset Password for User: ".$row['email']."</h4></div></div></div>";
			
			echo "
<div class=\"row\">
<div class=\"col-md-6\">
<div class=\"form-group\">
<input type='hidden' name='uid' value='$uid'>
<input  class=\"btn btn-warning\" type = 'submit' name = 'submitedit' value = 'Save Changes'>
</div>
</div>
</div>
";
			
			//New Password
			echo "<div class=\"row\"><div class=\"col-md-6\"><label for=\"newpassword\">New Password:<input type='password' id=\"newpassword\" name='newpassword' value=\"\"></label></div>";
			
			//Re-Type Password
			echo "<div class=\"col-md-6\"><label for=\"newpassword2\">Re-Type New Password:<input type='password' id=\"newpassword2\" name='newpassword2' value=\"\"></label></div><div>";

}

echo "</form>";

?>
</div>
</body></html>