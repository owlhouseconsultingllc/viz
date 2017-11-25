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
<head><title>Viz Edit</title>
<link rel="stylesheet" href="../scripts/bs/css/bootstrap.min.css">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />  <!--forces IE to prevent compatibility mode!!-->
<script src="../scripts/jquery.min.js"></script>
  <script src="../scripts/bs/js/bootstrap.min.js"></script>
</head>
<style>

</style>
<body>

<div class="well" id="vizeditwell">

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
sqlquery
notes*/

$vizid = $_GET["edit"];
echo "<form action='vizupdate.php' method='post'>";  

//<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
echo "<input style=\"display:none\" type=\"text\" name=\"fakeusernameremembered\"/>
<input style=\"display:none\" type=\"password\" name=\"fakepasswordremembered\"/>";



// MySQL connect
$result = $dbh->prepare(
'SELECT 
`id`,
`name`,
`title`,
`tab`,
(SELECT TABS.name from tabs TABS WHERE TABS.id=VIZ.id) as tabname,
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
`notes`
FROM `visualizations` VIZ
WHERE `id`=?');
$result->execute([$vizid]);
if (!$result) {    die("Query to show fields from table failed.");}
		
		//Loop through Query results and populate
		foreach ($result as $row)
		{
		
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><a class=\"btn btn-primary\" href='sysadmin.php'><i>Back</i></a><h4>Edit Visualization: ".$row['name']."</h4></div></div></div>";
		
		echo "
<div class=\"row\">
<div class=\"col-md-6\">
<div class=\"form-group\">
<a class=\"btn btn-danger\" href='vizdelete.php?id=$vizid'>Delete Visualization</a>
<input type='hidden' name='vizid' value='$vizid'>
<input  class=\"btn btn-warning\" type = 'submit' name = 'submitedit' value = 'Save Changes'>
</div>
</div>
</div>
";
		
		//Name --Added REGEX Requirement(for now)
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizname\">Name:</label><input type='text'  required=\"required\" pattern=\"[a-zA-Z0-9-]+\" class='form-control viztext' id ='newvizname' name='newvizname' value=\"".$row['name']."\"></div></div>";
		
		//Title
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newviztitle\">Title:</label><input type='text' class='form-control viztext' id ='newviztitle' name='newviztitle' value=\"".$row['title']."\"></div></div></div>";
		
		//Tab
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newviztab\">Tab:</label><select class='form-control viztext' id ='newviztab' name='newviztab' value=\"".$row['tabname']."\">";
		
		$tabs = $dbh->prepare(
		'SELECT 
		`id`,
		`title`
		FROM `tabs`');
		$tabs->execute();
		if (!$tabs) {    die("Query to show fields from table failed.");}
				foreach ($tabs as $tabrow){
				echo "<option ".(($row['tab']==$tabrow['id'])? ' selected="selected"' : '')." value=\"".$tabrow['id']."\">".$tabrow['title']."</option>";
				}
		echo "</select></div></div>";
		
		
		//Position
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizposition\">Position:</label><input type='number' pattern=\"[0-9]+\" class='form-control viztext' id ='newvizposition' name='newvizposition' value=\"".$row['position']."\"></div></div></div>";
		
		//Size
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizsize\">Size:</label><select class='form-control viztext' id ='newvizsize' name='newvizsize' value=\"".$row['size']."\">";
		echo "<option ".(($row['size']=='col-md-4')? ' selected="selected"' : '')." value=\"col-md-4\">col-md-4</option>";
		echo "<option ".(($row['size']=='col-md-6')? ' selected="selected"' : '')." value=\"col-md-6\">col-md-6</option>";
		echo "<option ".(($row['size']=='col-md-12')? ' selected="selected"' : '')." value=\"col-md-12\">col-md-12</option>";
		echo "</select></div></div>";
		
		
		//Class
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizclass\">Class:</label><input type='text' class='form-control viztext' id ='newvizclass' name='newvizclass' value=\"".$row['class']."\"></div></div></div>";
		
		//Type --read files from template dir
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newviztype\">Type:</label><select class='form-control viztext' id ='newviztype' name='newviztype' value=\"".$row['type']."\">";
		// Open a directory, and read its contents
		$path = "../template/*.php";
		foreach (glob($path) as $pathitem) {
		$file=str_replace(".php","",str_replace("../template/","",$pathitem));
		echo "<option ".(($file==$row['type'])? ' selected="selected"' : '')." value=\"".$file."\">".$file."</option>";}
		echo "</select></div></div>";


		
		//DBType
			
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizdbtype\">DBType:</label><select class='form-control viztext' id ='newvizdbtype' name='newvizdbtype' value=\"".$row['dbtype']."\">";
		echo "<option ".(($row['dbtype']=='')? ' selected="selected"' : '')." value=\"\"></option>";
		echo "<option ".(($row['dbtype']=='mysql')? ' selected="selected"' : '')." value=\"mysql\">MySQL</option>";
		echo "<option ".(($row['dbtype']=='mssql')? ' selected="selected"' : '')." value=\"mssql\">MS-SQL</option>";
		//echo "<option ".(($row['dbtype']=='csv')? ' selected="selected"' : '')." value=\"csv\">csv</option>";
		echo "</select></div></div></div>";
		
		//DBHost
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizdbhost\">DBHost:</label><input type='text' class='form-control viztext' id ='newvizdbhost' name='newvizdbhost' value=\"".$row['dbhost']."\"></div></div>";
		
		//DBName
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizdbname\">DBName:</label><input type='text' class='form-control viztext' id ='newvizdbname' name='newvizdbname' value=\"".$row['dbname']."\"></div></div></div>";
		
		//DBUser
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizdbuser\">DBUser:</label><input type='text' class='form-control viztext' id ='newvizdbuser' name='newvizdbuser' value=\"".$row['dbuser']."\"></div></div>";
		
		//DBPass
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizdbpass\">DBPass:</label><input type='password' class='form-control viztext' id ='newvizdbpass' name='newvizdbpass' value=\"".$row['dbpass']."\"></div></div></div>";
				
		//Query
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizquery\">Query:</label><textarea class='form-control viztextarea' id ='newvizquery' name='newvizquery'>".$row['query']."</textarea></div></div>";
		
		//newvizheight
		//Height Override(default250)
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizdbpass\">Height Override:<i>(Default 250px)</i></label><input type='text' class='form-control viztext' id ='newvizheight' name='newvizheight' value=\"".$row['height']."\"></div></div></div>";
		
		//Date Override(default %Y-%m-%d)
		echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newvizdate\">Date Format Override:<i>(Default %Y-%m-%d)</i></label><input type='text' class='form-control viztext' id ='newvizdate' name='newvizdate' value=\"".$row['dateformat']."\"></div></div>";
		
		//Notes
		echo "<div class=\"col-md-6\"><div class=\"form-group\"><label for=\"newviznotes\">Notes:</label><textarea class='form-control viztextarea' id ='newviznotes' name='newviznotes'>".$row['notes']."</textarea></div></div></div>";
		}



echo "</form>";

?>

</div>
</body></html>
