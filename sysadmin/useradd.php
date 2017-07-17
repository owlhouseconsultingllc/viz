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
<head><title>User Add</title>
<link rel="stylesheet" href="../scripts/bs/css/bootstrap.min.css">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />  <!--forces IE to prevent compatibility mode!!-->
<script src="../scripts/jquery.min.js"></script>
<script src="../scripts/bs/js/bootstrap.min.js"></script>
</head>
<style>
body { 
/*padding-top: 25px; keep items below navbar when fixed*/
}
.usertext {width:100%;}

.usertextarea {width: 100%;}
</style>
<body>
<div class="well" id="useraddwell">
<?php


include '../config.php';

//<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
echo "<input style=\"display:none\" type=\"text\" name=\"fakeusernameremembered\"/>
<input style=\"display:none\" type=\"password\" name=\"fakepasswordremembered\"/>";

echo "<form action='userinsert.php' method='post'>";


		//EDIT TITLE
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><a class=\"btn btn-primary\" href='sysadmin.php'><i>Back</i></a><h4>Add User</h4></div></div></div>";
		
		echo "
<div class=\"row\">
<div class=\"col-md-6\">
<div class=\"form-group\">
<input class=\"btn btn-warning\" type = 'submit' name = 'createrecord' value = 'Create User'>
</div>
</div>
</div>";
		
		//Username
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newusername\">Username:</label><input type='text' class='form-control usertext' id='newusername' name='newusername' value=\"\"></div></div></div>";
		
		//Password
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newuserpass\">Password:</label><input type='password' class='form-control usertext' id ='newuserpass' name='newuserpass' value=\"\"></div></div>";
		
		//Re-Enter Password
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newuserrepass\">Verify Password:</label><input type='password' class='form-control usertext' id ='newuserrepass' name='newuserrepass' value=\"\"></div></div></div>";
		
		
		


echo "</form>";

?>
</div>
</body></html>