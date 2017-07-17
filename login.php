<!DOCTYPE html>
<html lang="en">
<head>
<title>LOGIN</title>
<meta name="viewport" content="width=device-width, initial-scale=1"> <!--scales for mobile-->
<meta http-equiv="X-UA-Compatible" content="IE=edge" />  <!--forces IE to prevent compatibility mode!!-->
<link rel="stylesheet" href="scripts/bs/css/bootstrap.min.css">
<script src="scripts/jquery.min.js"></script>
<script src="scripts/bs/js/bootstrap.min.js"></script>
<link rel="shortcut icon" href="favicon.ico">
</head>
<style>

.vertical-center {
  min-height: 100%;
  min-height: 90vh;
  height: 1px; /*fix for IE11*/
  display: flex;
  align-items: center;
  }

.form-signin-heading {
color: charcoal;
align: center;
}
#loginwell {
max-width: 300px;
background-color: #222222;
}
}

</style>

<body>
<center>
<div class="vertical-center">
<div class="container">
<?php
session_start();
echo "
<div class=\"col-md-12\">
<div class=\"well\" id=\"loginwell\">
<h4 class=\"form-signin-heading\"><span class=\"glyphicon glyphicon-grain\" style=\"color:lightgray\"></span></h4>
<div class='vertspace'  style=\"height:10px;\"></div>
<form action='auth.php' method='post'>
<input type=\"text\" class=\"form-control\" autocomplete=\"off\" autocorrect=\"off\" autocapitalize=\"off\" spellcheck=\"false\" name=\"username\" placeholder=\"Username\">
<div class='vertspace'  style=\"height: 5px;\"></div>
<input type=\"password\" class=\"form-control\" name=\"password\" placeholder=\"Password\">
<div class='vertspace'  style=\"height: 5px;\"></div>
<button class=\"btn btn-sm btn-primary btn-block\" type=\"submit\">Login</button>
</form>
</div>
</div>";
?>
</div>
</div>
</center>
</body>
</html>