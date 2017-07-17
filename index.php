<?php
//include PDO and PHPAuth requirements
require("config.php");
//Session Check - If not logged in redirect to Login.php
if (!$auth->isLogged()) {
    echo "Forbidden";
    header('Location: login.php');
	exit();
}
//Session Expiration Update
$hash=$auth->getSessionHash();
$expire = date("Y-m-d H:i:s", strtotime("+30 minutes"));
$sessresult = $dbh->prepare('UPDATE sessions SESS SET SESS.expiredate=? WHERE SESS.hash=?');
$sessresult->execute([$expire,$hash]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Dashboard</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />  <!--forces IE to prevent compatibility mode!!-->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="scripts/bs/css/bootstrap.min.css">
<script src="scripts/jquery.min.js"></script>
<script src="scripts/bs/js/bootstrap.min.js"></script>
<script src="scripts/d3.min.js"></script>
<script src="scripts/build/nv.d3.min.js"></script>
<link rel="stylesheet" href="scripts/build/nv.d3.css">
<link rel="shortcut icon" href="favicon.ico">


</head>
<style>
/*Additional navbar collapse options*/
@media (min-width: 768px) and (max-width: 991px) {
    .navbar-collapse.collapse {
        display: none !important;
    }
    .navbar-collapse.collapse.in {
        display: block !important;
    }
    .navbar-header .collapse, .navbar-toggle {
        display:block !important;
    }
    .navbar-header {
        float:none;
    }
}

/*iphone collapsed nav menu fix*/
.dropdown-backdrop {
    position: static;
}

body { 
padding-top: 55px; /*keep items below navbar when fixed*/
margin: 0px;
/*background-color:gray;*/
}



/* This only works with JavaScript, 
if it's not present, don't show loader */
.no-js #loader { display: none;  }
.js #loader { display: block; position: absolute; left: 100px; top: 0; }
.loading-animation {
	position: fixed;
	left: 0px;
	top: 0px;
	width: 100%;
	height: 100%;
	z-index: 9999;
	background: url(images/page-loader.gif) center no-repeat #fff;
}

#navbar-btn { 
margin-right:6px;
 }

.glyphicon.glyphicon-grain {
    font-size: 30px;
	 margin-top: 7px;
		margin-bottom: 0px;
        margin-left: 0px;
		margin-right: 15px;
}
 
.navbar-brand {
        padding: 0;        
    }

#signin .form-control{ max-width: 120px; }

.adminpane {
position: fixed; 
  right: 0; 
  bottom: 0; 
  left: 0;
  top:55px;
  -webkit-overflow-scrolling: touch;
  overflow-y: scroll;
}


</style>

<script>
// Added this to force NVD3 to redraw the chart
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

/*Hide animation/SHOW Animation on login submit*/
$(document).ready(function(){

$(".loading-animation").hide();
    $("#loginbutton").click(function(){
        $(".loading-animation").show();
    });
 });

//auto collapse on collapsed selection
$(function() {
    $('.data-src-lazy a').on('click', function(){ 
        if($('.navbar-toggle').css('display') !='none'){
            $(".navbar-toggle").trigger( "click" );
        }
    });
});

//Lazy bootstrap tabs
$(function(){
  $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
  var that=$($(e.target).attr('href')).find('.map');
  if(!that.find('iframe').length){ 
    that.append($('<iframe/>',{src:that.data('map')})
                  .css({height:'100%',width:'100%',border:'none'}));
  }
}).first().trigger('shown.bs.tab');
});

//Lazy Data Sources (Quicker Loads times)
$(function() {
    $('#CPAModal').on('show.bs.modal', function(){
        var iframe = $("#cpaiframe");
    iframe.attr("src", iframe.data("src")); 
    });
});

</script>


</head>

<body>

<div class="loading-animation"></div> <!--Loading Animation-->

<div class="container-fluid" id="bigcontainer">
<div class="row">

<div class="col-md-12">
<nav class="navbar navbar-inverse navbar-fixed-top">
<!-- <div class="navbar-brand"><span class="glyphicon glyphicon-grain pull-left"></span></div>-->

 <!-- Title -->
        <div class="navbar-header pull-left">
          <a href="" style="margin-left: 13px;" class="navbar-brand"><span class="glyphicon glyphicon-grain pull-left"></span></a>
        </div>

		
 <!-- 'Sticky' (non-collapsing) right-side menu item(s) -->
        <div class="navbar-header pull-right">
          <ul class="nav pull-left">
            <!-- This works well for static text, like a username -->
            <!-- <li class="navbar-text pull-left">Hello, <?php //echo $username;?></li> -->
            <!-- Add any additional bootstrap header items.  This is a drop-down from an icon -->
            <li class="dropdown pull-right">
              <a href="#" data-toggle="dropdown" style="color:#777; margin-top: 5px;" class="dropdown-toggle"><span class="glyphicon glyphicon-user"></span><b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li>
                  <a href="scripts/pwreset.php" title="Change Password">Change Password</a>
                </li>
                <li>
                  <a href="logout.php" title="Logout">Logout </a>
                </li>
              </ul>
            </li>
          </ul>

		
<!--Collapse stuff-->

      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myCollapseTabs">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div> 
<!--End Collapse stuff-->
<div class="collapse navbar-collapse" id="myCollapseTabs">
<ul class="nav navbar-nav head-menu data-src-lazy" id="myTabs">

<?php
//Loop through/generate tabs
$sth = $dbh->prepare("SELECT name, title FROM tabs ORDER BY position");
$sth->execute();
if (!$sth) {die("Query to show fields from table failed.");}
$i=0;
foreach ($sth as $row){
	if($i == 0){echo "<li class=\"active\">";}
	else{echo "<li>";}
	echo "<a href=\"#".$row['name']."\" data-toggle=\"tab\">".$row['title']."</a></li>";
	$i++;
}
?>

<?php //Auth Tab if access allowed --needs work 
if($isedit==1){echo "<li><a href=\"#useradmin\" data-toggle=\"tab\"><span class=\"glyphicon glyphicon-cog\"></span></a></li>";}?>
</ul>


</div>
</nav>
</div>
</div>


<div class="row">
<div class="col-md-12">
<div class="tabbable" id="primary-tabtable">
<div class="tab-content">

<!-- Panes-->
<?php //Loop through and generate a pane for each tab

//Loop through/generate tabs
$pane = $dbh->prepare("SELECT id, name FROM tabs ORDER BY position");
$pane->execute();
if (!$pane) {die("Query to show fields from table failed.");}
$i=0;
foreach ($pane as $panerow){
	if($i == 0){
		echo "<div class=\"tab-pane active\" id=\"".$panerow['name']."\">";}
		else{echo "<div class=\"tab-pane \" id=\"".$panerow['name']."\">";}
	echo "<div class=\"container\"><div class=\"row\">";
	
		//Loop through/generate objects for corresponding tab/pane
		$object = $dbh->prepare("SELECT name, title, tab, position, size, class, type, dbtype, dbhost, dbuser, dbpass, dbname, query, height, dateformat, notes FROM visualizations WHERE tab=? ORDER BY position");
		//$tab='testab0';
		$object->execute([$panerow['id']]);
		if (!$object) {die("Query to show fields from table failed.");}
		foreach ($object as $objectrow){
			echo "<div class=\" ".$objectrow['size']."\">";
			echo "<div align=\"center\"><h5><b>".$objectrow['title']."</b></h5></div>";
			include "template/".$objectrow['type'].".php"; 
			echo "</div>";
		}
	echo "</div></div></div>";
	$i++;	
}
?>



<!-- Admin Pane-->
<div  align="center" class="tab-pane" id="useradmin">
<?php

if($isedit==1){echo "
<div class=\"col-sm-12\">
<div class='embed-container'>
<div class=\"map adminpane\" data-map=\"sysadmin/sysadmin.php\" ></div>
</div>
</div>
";}
?>
</div>
</div>
</div>
</div>
</div>



</div>
</body>
</html>