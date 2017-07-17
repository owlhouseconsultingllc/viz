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

<div class="<?php echo $objectrow['class'];?>" id="<?php echo $objectrow['name'];?>" style=<?php if($objectrow['height']>0){echo "'height: ".$objectrow['height']."px;'";} else{echo "'height: 250px;'";}?>><svg></svg></div>

<script>
function <?php echo $objectrow['name'];?>Data() {
  return  [
<?php
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
	echo "]};</script>";
    return;
}



//echo $column0;
$result = $viz->fetchAll(PDO::FETCH_ASSOC);

foreach($result as $key=>$value){
  foreach($value as $keyval=>$val){
    $result2[]=$keyval;
	//echo $keyval."<br>";
  }
}

//Column names! Finally!
$result3=array_unique($result2);

//All but the first column --for key value roll-up
$i=0;
foreach($result3 as $key=>$value){
if($i!=0){$result4[]=$value;}
	//autoincrement variable names..eg $column0.. $column1 etc
	${'column'.$i}=$value;
	${'columnarray'.$i} = array_column($result, ${'column'.$i});
	$i++;
}

$i=1;
foreach($result4 as $key=>$value){

$isdate=0;
foreach($result as $key=>$value){
  foreach($value as $keyval=>$val){
	if($i<2){	  
		  if ($keyval==$column0){echo "{label: \"".$val."\"";} if($keyval==${'column'.$i}){ echo ", value: ".$val." },
		";}
		}}
	}
//echo "]},";
$i++;
}
/*var_dump($result);var_dump($result2);var_dump($result3);var_dump($result4);var_dump($columnarray0);var_dump($columnarray1);*/
?>
];}

//Donut chart example
nv.addGraph(function() {
  var <?php echo $objectrow['name'];?> = nv.models.pieChart()
      .x(function(d) { return d.label })
      .y(function(d) { return d.value })
      .showLabels(true)     //Display pie labels
      .labelThreshold(.05)  //Configure the minimum slice size for labels to show up
      .labelType("percent") //Configure what type of data to show in the label. Can be "key", "value" or "percent"
      .donut(true)          //Turn on Donut mode. Makes pie chart look tasty!
      .donutRatio(0.35)     //Configure how big you want the donut hole size to be.
      ;

    d3.select("#<?php echo $objectrow['name'];?> svg")
        .datum(<?php echo $objectrow['name'];?>Data())
        .transition().duration(350)
        .call(<?php echo $objectrow['name'];?>);

nv.utils.windowResize(
function() {
<?php echo $objectrow['name'];?>.update();
}
);
		
  return <?php echo $objectrow['name'];?>;
  
});


</script>

