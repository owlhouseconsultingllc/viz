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
<head><title>Tab Add</title>
<link rel="stylesheet" href="../scripts/bs/css/bootstrap.min.css">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />  <!--forces IE to prevent compatibility mode!!-->
<script src="../scripts/jquery.min.js"></script>
<script src="../scripts/bs/js/bootstrap.min.js"></script>
</head>
<style>
body { 
/*padding-top: 25px; keep items below navbar when fixed*/
}
.tabtext {width:100%;}

.tabtextarea {width: 100%;}
</style>
<body>
<div class="well" id="tabaddwell">
<?php


include '../config.php';
echo "<form action='tabinsert.php' method='post'>";


		//EDIT TITLE
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><a class=\"btn btn-primary\" href='sysadmin.php'><i>Back</i></a><h4>Add Tab: </h4></div></div></div>";
		
		echo "
<div class=\"row\">
<div class=\"col-md-6\">
<div class=\"form-group\">
<input class=\"btn btn-warning\" type = 'submit' name = 'createrecord' value = 'Create Tab'>
</div>
</div>
</div>";

		//Name
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newtabname\">Name:</label><input type='text' required=\"required\" pattern=\"[a-zA-Z0-9-]+\" class='form-control tabtext' id='newtabname' name='newtabname' value=\"\"></div></div>";
		
		//Title
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newtabtitle\">Title:</label><input type='text' required=\"required\" class='form-control tabtext' id='newtabtitle' name='newtabtitle' value=\"\"></div></div></div>";	
		
		//Position
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newtabposition\">Position:</label><input type='number' pattern=\"[0-9]+\" class='form-control tabtext' id='newtabposition' name='newtabposition' value=\"\"></div></div>";	
		
		//Notes
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newtabnotes\">Notes:</label><textarea class='form-control tabtextarea' id='newtabnotes' name='newtabnotes'></textarea></div></div></div>";
		
		


echo "</form>";

?>
</div>
</body></html>