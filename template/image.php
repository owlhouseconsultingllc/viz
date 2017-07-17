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
<meta charset="utf-8">

<style>
</style>

<div class="<?php echo $objectrow['class'];?>" id="<?php echo $objectrow['name'];?>">
<img class="center-block" src="<?php echo $objectrow['query'];?>" style=<?php if($objectrow['height']>0){echo "'height: ".$objectrow['height']."px; max-width: 100%;'";} else{echo "'height: 250px; max-width: 100%;'";}?>></img>
</div>