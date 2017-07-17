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
return [
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
echo "{
key: \"$value\",
values:
[
";
$isdate=0;
foreach($result as $key=>$value){
  foreach($value as $keyval=>$val){
		if ($keyval==$column0 && (strpos($val, '-') !== false || strpos($val, '/') !== false )) {
		$xval=strtotime($val)*1000;
		$isdate=1;
		if(!$objectrow['dateformat']){$dateformat='%Y-%m-%d';} else{$dateformat=$objectrow['dateformat'];}
		}
		else {$xval=$val;
			}  
  if ($keyval==$column0){echo "{ x : ".$xval;} if($keyval==${'column'.$i}){ echo ", y : ".$val.",shape: shapes[".($i-1)." % shapes.length] },
";}
}}
echo "]},";
$i++;
}

/*var_dump($result);var_dump($result2);var_dump($result3);var_dump($result4);var_dump($columnarray0);var_dump($columnarray1);*/

?>
]
}

 	var shapes = ['circle', 'cross', 'triangle-up', 'triangle-down', 'diamond', 'square'];

    // create the chart
    var <?php echo $objectrow['name'];?>;
    nv.addGraph(function() {
        <?php echo $objectrow['name'];?> = nv.models.scatterChart().margin({right: 35})/*fixes last pixel from getting clipped in bootstrap*/
            .showDistX(true)
            .showDistY(true)
            .useVoronoi(true)
			.color(d3.scale.category10().range())
			//.pointSize(function (d) { return d.size || 10})
			.pointRange([45,50])
            .duration(300)
			;
			
							
        <?php echo $objectrow['name'];?>.dispatch.on('renderEnd', function(){
            console.log('render complete');
        });

        <?php echo $objectrow['name'];?>.xAxis
		<?php //Handle the x-axis formattign(determine if its a date..)
		if($isdate==1){
		echo ".tickFormat(function (d) {return d3.time.format('".$dateformat."')(new Date(d))});
		//Fixes Time scale for when x axis = dates
		".$objectrow['name'].".scatter.xScale(d3.time.scale());";
		}
		else {echo "
		.tickFormat(d3.format(\"d\"));
		";}
		?>
		
		//abbreviates large numbers so they fit in margin (including tick format below)
		var si_prefix_formatter = d3.format('.3s'),
		integer_formatter = d3.format(',.1d');
	
        <?php echo $objectrow['name'];?>.yAxis.tickFormat(function(d){
		if(d >= Math.pow(10,4)){
			return si_prefix_formatter(d);
		}
		return integer_formatter(d);
		});

        d3.select('#<?php echo $objectrow['name'];?> svg')
            .datum(<?php echo $objectrow['name'];?>Data())
            .call(<?php echo $objectrow['name'];?>);

        nv.utils.windowResize(<?php echo $objectrow['name'];?>.update);

        <?php echo $objectrow['name'];?>.dispatch.on('stateChange', function(e) { ('New State:', JSON.stringify(e)); });
        return <?php echo $objectrow['name'];?>;
    });



</script>

