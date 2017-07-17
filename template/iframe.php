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
<iframe class="center-block" src="<?php echo $objectrow['query'];?>" width="100%" height="<?php if($objectrow['height']>0){echo $objectrow['height'];}else{echo "250";}?>"></iframe>
</div>
  