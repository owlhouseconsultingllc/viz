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


?>
<!DOCTYPE html>
<html lang="en">
<head><title>Password Reset</title>
<link rel="stylesheet" href="../scripts/bs/css/bootstrap.min.css">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />  <!--forces IE to prevent compatibility mode!!-->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="../scripts/jquery.min.js"></script>
  <script src="../scripts/bs/js/bootstrap.min.js"></script>
</head>


<?php


$uid = $userid;
$currentpassword=$_POST["currentpassword"];
$newpassword=$_POST["newpassword"];
$newpassword2=$_POST["newpassword2"];



$result = $auth->login($username, $currentpassword, NULL);
    // 1 - error
    // 0 - ok
    
	if ($result['error']) {
        $output = json_encode(array("type" => 1, "result" => $result['message']));
		echo $result['message'];
		include ("pwreset.php");
		return;
    } else {
	
			// Logged in successfully, set cookie, display success message
			setcookie($config->cookie_name, $result['hash'], $result['expire'], $config->cookie_path, $config->cookie_domain, $config->cookie_secure, $config->cookie_http);

			if ($newpassword2!=$newpassword){ 
				echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><a class=\"btn btn-primary\" href='pwreset.php'><i>Back</i></a></div></div></div>";
				echo "Passwords do not match!";
				return;}
				
			if ($newpassword=='' || strlen($newpassword) < 4 ){ 
				echo "<div class=\"row\"><div class=\"col-md-6\"><div class=\"form-group\"><a class=\"btn btn-primary\" href='pwreset.php'><i>Back</i></a></div></div></div>";
				echo "Password Invalid!";
				return;}

				

			$pword = $auth->getHash($newpassword);
			//echo "$pword";
			$result = $dbh->prepare('UPDATE `users` SET `password`=? WHERE id=?');
			$result->execute([$pword,$uid]);
			header("Location: ../index.php");
			
			}
?>