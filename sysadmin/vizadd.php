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
<head><title>Viz Add</title>
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
<div class="well" id="vizaddwell">
<?php


include '../config.php';

//<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
echo "<input style=\"display:none\" type=\"text\" name=\"fakeusernameremembered\"/>
<input style=\"display:none\" type=\"password\" name=\"fakepasswordremembered\"/>";

echo "<form action='vizinsert.php' method='post'>";



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

	//EDIT TITLE
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><a class=\"btn btn-primary\" href='sysadmin.php'><i>Back</i></a><h4>Add Visualization: </h4></div></div></div>";
		
		echo "
<div class=\"row\">
<div class=\"col-md-6\">
<div class=\"form-group\">
<input  class=\"btn btn-warning\" type = 'submit' name = 'createrecord' value = 'Create Visualization'>
</div>
</div>
</div>
";
		
		//Name
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizname\">Name:</label><input type='text'  required=\"required\" pattern=\"[a-zA-Z0-9-]+\" class='form-control viztext' id ='newvizname' name='newvizname' value=\"\"></div></div>";
		
		//Title
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newviztitle\">Title:</label><input type='text' class='form-control viztext' id ='newviztitle' name='newviztitle' value=\"\"></div></div></div>";
		
		//Tab
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newviztab\">Tab:</label><select class='form-control viztext' id ='newviztab' name='newviztab' value=\"\">";
		
		$tabs = $dbh->prepare(
		'SELECT 
		`id`,
		`title`
		FROM `tabs`');
		$tabs->execute();
		if (!$tabs) {    die("Query to show fields from table failed.");}
				foreach ($tabs as $tabrow){
				echo "<option value=\"".$tabrow['id']."\">".$tabrow['title']."</option>";
				}
		echo "</select></div></div>";
		
		//Position
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizposition\">Position:</label><input type='number' pattern=\"[0-9]+\" class='form-control viztext' id ='newvizposition' name='newvizposition' value=\"\"></div></div></div>";
		
		//Size
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizsize\">Size:</label><select class='form-control viztext' id ='newvizsize' name='newvizsize' value=\"\">";
		echo "<option value=\"col-md-4\">col-md-4</option>";
		echo "<option value=\"col-md-6\">col-md-6</option>";
		echo "<option value=\"col-md-12\">col-md-12</option>";
		echo "</select></div></div>";
		
		
		//Class
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizclass\">Class:</label><input type='text' class='form-control viztext' id ='newvizclass' name='newvizclass' value=\"\"></div></div></div>";
		
		//Type --read files from template dir
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newviztype\">Type:</label><select class='form-control viztext' id ='newviztype' name='newviztype' value=\"\">";
		// Open a directory, and read its contents
		$dir = "../template/";
		$path = "../template/*.php";
		foreach (glob($path) as $pathitem) {
		$file=str_replace(".php","",str_replace("../template/","",$pathitem));
		echo "<option value=\"".$file."\">".$file."</option>";}
		echo "</select></div></div>";
		
		//DBType
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizdbtype\">DBType:</label><select class='form-control viztext' id ='newvizdbtype' name='newvizdbtype' value=\"\">";
		echo "<option value=\"\"></option>";
		echo "<option value=\"mysql\">MySQL</option>";
		echo "<option value=\"mssql\">MS-SQL</option>";
		//echo "<option value=\"csv\">csv</option>";
		echo "</select></div></div></div>";
		
		//DBHost
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizdbhost\">DBHost:</label><input type='text' class='form-control viztext' id ='newvizdbhost' name='newvizdbhost' value=\"\"></div></div>";
		
		//DBName
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizdbname\">DBName:</label><input type='text' class='form-control viztext' id ='newvizdbname' name='newvizdbname' value=\"\"></div></div></div>";
		
		//DBUser
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizdbuser\">DBUser:</label><input type='text' class='form-control viztext' id ='newvizdbuser' name='newvizdbuser' value=\"\"></div></div>";
		
		//DBPass
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizdbpass\">DBPass:</label><input type='password' class='form-control viztext' id ='newvizdbpass' name='newvizdbpass' value=\"\"></div></div></div>";
		
		//Query
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizquery\">Query:</label><textarea class='form-control viztextarea' id ='newvizquery' name='newvizquery'></textarea></div></div>";
		
		//newvizheight
		//Height Override(default250)
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizheight\">Height Override:<i>(Default 250px)</i></label><input type='text' class='form-control viztext' id ='newvizheight' name='newvizheight' value=\"\"></div></div></div>";
		
		//Date Override(default %Y-%m-%d)
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizdate\">Date Format Override:<i>(Default %Y-%m-%d)</i></label><input type='text' class='form-control viztext' id ='newvizdate' name='newvizdate' value=\"\"></div></div>";
		
		
		//Notes
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newviznotes\">Notes:</label><textarea class='form-control viztextarea' id ='newviznotes' name='newviznotes'></textarea></div></div></div>";
		
		


echo "</form>";

?>
</div>
</body></html>
