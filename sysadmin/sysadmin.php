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

//Get Permissions--if($isedit==1){ Required for iPhone iframes(the config.php won't do
$userid = $auth->getSessionUID($auth->getSessionHash());
$usernameget = $dbh->prepare('SELECT email, isactive, isedit, isadmin FROM `users` USRS WHERE USRS.id=?');
$usernameget->execute([$userid]);
foreach ($usernameget as $usr)
		{
		$username=$usr['email'];
		$isactive=$usr['isactive'];
		$isedit=$usr['isedit'];
		$isadmin=$usr['isadmin'];
		}

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
<head><title>System Admin</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />  <!--forces IE to prevent compatibility mode!!-->
<!--<link rel="stylesheet" href="../scripts/bs/css/bootstrap.min.css">
<link rel="stylesheet" href="../scripts/bs/css/bootstrap.min.css">
<script src="../scripts/jquery.min.js"></script>
<script src="../scripts/bs/js/bootstrap.min.js"></script>-->
<link rel="stylesheet" type="text/css" href="../scripts/DataTables/datatables.min.css"/>
<script type="text/javascript" src="../scripts/DataTables/datatables.min.js"></script>
<!--<script type="text/javascript" src="../scripts/DataTables/pdfmake/0.1.18/build/pdfmake.min.js"></script>-->

</head>

<script>
// Spinner while page loads
	$(window).load(function() {
		// Animate loader off screen
		$(".preload-spinner").fadeOut("slow");;
	});
</script>

<style>
/*Spinner CSS*/
.no-js #loader { display: none;  }
.js #loader { display: block; position: absolute; left: 100px; top: 0; }
.preload-spinner {
	position: fixed;
	left: 0px;
	top: 0px;
	width: 100%;
	height: 100%;
	z-index: 9999;
	background: url(../images/page-loader.gif) center no-repeat #fff;
}

body { 
margin: 0px; 
background-color: none;
}



#admintable {
background: #f9f9f9 /*lightgray;*/
}



</style>

<script>
$(function () {
    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
		scrollTo(0,0);//scrolls to top of page
		//For IE
		if (navigator.userAgent.indexOf('MSIE') !== -1 || navigator.appVersion.indexOf('Trident/') > 0) {
		var evt = document.createEvent('UIEvents');
		evt.initUIEvent('resize', true, false, window, 0);
		window.dispatchEvent(evt);
		}
		//For the much better browsers
		else {
		window.dispatchEvent(new Event('resize'));
		}
    });
});

//DataTables Note!!!: make sure associated tables have structure <thead><tr><th></th></tr></thead><tbody><tr><td></td><tr></tbody>
$(document).ready(function() {
    $('table.data').DataTable({
	//responsive: true
	//"dom": '<"top"<"left"fi><"clear">t<"F"pl>'//dom locations for paging/search items //Add "B" for export buttons
	//, "buttons": ['pdf']
	//disables sort
	//,"ordering": false 
	//Required in order set set default sort column
	"order":[[1, 'asc']]
	//prevents sort on 1st column (if sorting enabled)
	,"columnDefs": [ {
      "targets": [ 0 ],
      "orderable": false
	}]
	,responsive: true
	,bPaginate: false
	,scrollY: '100vh' //'74vh' //74% of current window's vert height (dynamic)
    ,scrollCollapse: true
	});
	

/*$('#myTab a').click(function(e) {
  e.preventDefault();
  $(this).tab('show');
});*/


});

//Remember Tabs!!
$(function() { 
  //for bootstrap 3 use 'shown.bs.tab' instead of 'shown' in the next line
  $('a[data-toggle="tab"]').on('click', function (e) {
    //save the latest tab; use cookies if you like 'em better:
    localStorage.setItem('lastTab2', $(e.target).attr('href'));
  });
  //go to the latest tab, if it exists:
  var lastTab2 = localStorage.getItem('lastTab2');
  if (lastTab2) {
      $('a[href="'+lastTab2+'"]').click();
  }
});


</script>
<body>
<div class="preload-spinner"></div>
<div class="container-fluid" id="bigcontainer">
<div class="col-sm-12">
<?php

include '../config.php';
//Check to see if user has admin flag set in MySQL(else boot to index.php)
echo "<div class=\"panel panel-default\">";
echo "<table class='table display' id=\"admintable\">
<tr><td><h4>System Admin <span class=\"glyphicon glyphicon-cog\"></span></h4></td></tr>";

//Sys Admin Tabs
echo "<tr><td>
<ul class=\"nav nav-tabs\" id=\"myTab\">
<li class=\"active\"><a data-toggle=\"tab\" href=\"#managetabs\">Manage Tabs</a></li>
<li><a data-toggle=\"tab\" href=\"#manageviz\">Manage Visualizations</a></li>";
//Only generate Admin section for admins
if($isadmin==1){echo "<li><a data-toggle=\"tab\" href=\"#useradmin\">User Admin</a></li>";}
echo "</ul>
</td></tr>
";

//Tabs!
echo "<tr><td><div class=\"tab-content\">";


//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!Tabs Tab
echo "<div id=\"managetabs\" class=\"tab-pane fade in active\">";
echo "<table><tr><td><a class=\"btn btn-primary\" href='tabadd.php'>Add Tab</a></td></tr></table>";
echo "<table width=\"100%\" class='table table-striped display table-bordered data'>";

//Table Headers <th>//

echo "<thead><tr><th></th>";
echo "<th style=\"text-align:center\">Name</th>
<th style=\"text-align:center\"'>Title</th>
<th style=\"text-align:center\"'>Position</th>
<th style=\"text-align:center\"'>Notes</th>

</tr></thead>";
// MySQL connect

		$result = $dbh->prepare('SELECT `id` ,`name`, `title`, `position`, `notes` FROM `tabs` ORDER BY `id`');
		$result->execute();
		if (!$result) {    die("Query to show fields from table failed.");}
		
		//Loop through Query results and populate
		foreach ($result as $row)
		{
		echo "<tr><td><a href='tabedit.php?edit=".$row['id']."'>Edit</td><td>".$row['name']."</td><td>",$row['title']."</td><td>".$row['position']."</td><td>".$row['notes']."</td></tr>";
		}

echo "</table></div>";


//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!Visualizations Tab
echo "<div id=\"manageviz\" class=\"tab-pane fade in\">";
echo "<table><tr><td><a class=\"btn btn-primary\" href='vizadd.php'>Add Visualization</a></td></tr></table>";
echo "<table width=\"100%\" class='table table-striped display table-bordered data'>";

//Table Headers <th>//

echo "<thead><tr><th></th>";
echo "<th style=\"text-align:center\">Name</th>
<th style=\"text-align:center\"'>Title</th>
<th style=\"text-align:center\"'>Tab</th>
<th style=\"text-align:center\"'>Position</th>
<th style=\"text-align:center\"'>Size</th>
<th style=\"text-align:center\"'>Class</th>
<th style=\"text-align:center\"'>Type</th>
</tr></thead>";
// MySQL connect

		$result = $dbh->prepare(' 
		SELECT 
		VIZ.`id`, 
		VIZ.`name`, 
		VIZ.`title`, 
		VIZ.`tab`, 
		TABS.`title` as tabname,
		VIZ.`position`, 
		VIZ.`size`, 
		VIZ.`class`, 
		VIZ.`type` 
		FROM visualizations VIZ 
		LEFT JOIN `tabs` TABS ON TABS.`id`=VIZ.`tab`
		ORDER BY `id`');
		$result->execute();
		if (!$result) {    die("Query to show fields from table failed.");}
		
		//Loop through Query results and populate
		foreach ($result as $row)
		{
						
		echo "
		<tr>
		<td><a href='vizedit.php?edit=".$row['id']."'>Edit</td>
		<td>".$row['name']."</td>
		<td>",$row['title']."</td>
		<td>".$row['tabname']."</td>
		<td>".$row['position']."</td>
		<td>".$row['size']."</td>
		<td>".$row['class']."</td>
		<td>".$row['type']."</td>
		</tr>";
		}


echo "</table></div>";

//Only generate Admin section for admins
if($isadmin==1){
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!Access Admin Tab
echo "<div id=\"useradmin\" class=\"tab-pane fade in\">";
echo "<table><tr><td align = 'center'><a class=\"btn btn-primary\" href='useradd.php'>Create New User</a></td></tr></table>";
echo "<table width=\"100%\" class='table table-striped display table-bordered data'>";

//Table Headers <th>//

echo "<thead><tr><th></th>";
echo "<th>Username</th>
<th style=\"text-align:center\">View</th>
<th style=\"text-align:center\"'>Edit</th>
<th style=\"text-align:center\"><span class=\"glyphicon glyphicon-alert\"></span>User Admin</th>
<th></th>
</tr></thead>";
// MySQL connect

		$result = $dbh->prepare('SELECT id, email, isactive, isedit, isadmin FROM users');
		$result->execute();
		if (!$result) {    die("Query to show fields from table failed.");}
		
		//Loop through Query results and populate
		foreach ($result as $row)
		{
		
		if ($row['isactive'] == '1'){$isview ="<span class='glyphicon glyphicon-ok'></span>";} else {$isview = '';}
		if ($row['isedit'] == '1'){$isedit ="<span class='glyphicon glyphicon-ok'></span>";} else {$isedit = '';}
		if ($row['isadmin'] == '1'){$isadmin ="<span class='glyphicon glyphicon-ok'></span>";} else {$isadmin = '';}
				
		echo "<tr><td><a href='useredit.php?edit=".$row['id']."'>Edit</a></td><td>".$row['email']."</td><td align='center'>$isview</td><td align='center'>$isedit</td><td align='center'>$isadmin</td><td><a href='userpwreset.php?edit=".$row['id']."'>Reset Password</a></td></tr>";
		}

echo "</table></div>";
}
echo "</div>";//Close Tabbed Section
?>

 <?php

echo "</td></tr></table>";
echo "</td></tr></table></div>";

?>
</div></div>
</body></html>