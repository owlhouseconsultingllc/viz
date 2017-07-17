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
.pane {
    display: inline-block;
    overflow-y: scroll;
	<?php if($objectrow['height']>0){echo "max-height:".$objectrow['height']."px;";} else{echo "max-height:220px;";}?> 
	min-width: 100%;
	
}
#<?php echo $objectrow['name'];?> th {
	text-align: center;
}
#<?php echo $objectrow['name'];?> table {
	min-width: 100%;
    margin: none;
}


</style>

<div class="<?php echo $objectrow['class'];?> table-responsive pane container-fluid" id="<?php echo $objectrow['name'];?>">

<?php
include 'config.php';


$query=null;
$vdbh=null;
$viz=null;

$result=null;
$result2=null;
$result3=null;
$result4=null;
$key=null;
$value=null;
$keyval=null;
$val=null;
$xval=null;


$query=$objectrow['query'];
//Test connection and close/script and variable if failureoccurs
try {

if ($objectrow['dbtype']=='mssql'){
//$mssqldriver = 'SQL Server';
	$vdbh = new PDO("odbc:Driver=SQL Server;Server=".$objectrow['dbhost'].";Database=".$objectrow['dbname'], $objectrow['dbuser'], $objectrow['dbpass']);}
	else{$vdbh = new PDO("mysql:host=".$objectrow['dbhost'].";dbname=".$objectrow['dbname']."", "".$objectrow['dbuser']."", "".$objectrow['dbpass']."");}

$vdbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$viz = $vdbh->prepare($query);
$viz->execute();

} catch (PDOException $e) {
	echo "</div>";
    return;
}

$result = $viz->fetchAll(PDO::FETCH_ASSOC);

//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!Table
echo "<table class=\"".$objectrow['class']." table-responsive table\">";

//Table Headers <th>//
foreach($result as $key=>$value){
  foreach($value as $keyval=>$val){
    $result2[]=$keyval;
	}
}

//Column names! Finally!
$result3=array_unique($result2);

//Generate Column Names
echo "<thead><tr>";
$i=0;
foreach($result3 as $key=>$value){
	${'column'.$i}=$value;
	echo "<th>".${'column'.$i}."</th>";
	$i++;
	}
echo "</tr></thead>

";


//Generate Table Data
$columncount=count($result3);
$i=0;
$z=0;
foreach($result as $key=>$value){
	
	foreach($value as $keyval=>$val){
		if ($i==$columncount){$i=0;}
		if($z==0){echo "<tr>";}
		if($keyval==${'column'.$i}){echo "<td>$val</td>";}
		if($z==$columncount-1){echo "</tr>";$z=-1;}
		$i++;
		$z++;	
	}
	
}
echo "

</tr>";
echo "</table>";
?>
</div>