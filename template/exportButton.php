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

$filename = $objectrow['name'];
$dbtype = $objectrow['dbtype'];
$dbhost = $objectrow['dbhost'];
$dbname = $objectrow['dbname'];
$dbuser = $objectrow['dbuser'];
$dbpass = $objectrow['dbpass'];
$query = $objectrow['query'];


?>
<!DOCTYPE html>
<meta charset="utf-8">

<style>
</style>

<div class=" text-center <?php echo $objectrow['class'];?>" id="<?php echo $objectrow['name'];?>">
<form action='scripts/export.php' method='post' style=<?php if($objectrow['height']>0){echo "'height: ".$objectrow['height']."px;'";} else{echo "'height: 250px;'";}?>>
<input type='hidden' name='filename' value='<?php echo $filename;?>'>
<input type='hidden' name='dbtype' value='<?php echo $dbtype;?>'>
<input type='hidden' name='dbhost' value='<?php echo $dbhost;?>'>
<input type='hidden' name='dbname' value='<?php echo $dbname;?>'>
<input type='hidden' name='dbuser' value='<?php echo $dbuser;?>'>
<input type='hidden' name='dbpass' value='<?php echo $dbpass;?>'>
<input type='hidden' name='query' value='<?php echo $query;?>'>
<input class="btn btn-primary" type = 'submit' name = 'submitexport' value = 'Export Data'>
</form>
</div>
  