<?php
//include PDO and PHPAuth requirements
require("../config.php");
//Session Check - If not logged in redirect to Login.php
if (!$auth->isLogged()) {
    echo "Forbidden";
    header('Location: ../login.php');
	exit();
}
//Session Expiration Update
$hash=$auth->getSessionHash();
$expire = date("Y-m-d H:i:s", strtotime("+30 minutes"));
$sessresult = $dbh->prepare('UPDATE sessions SESS SET SESS.expiredate=? WHERE SESS.hash=?');
$sessresult->execute([$expire,$hash]);
include '../config.php';

$vizname = $_POST['filename'];
$filename = $_POST['filename']."_".date('YmdHis');


//Get visualization specific data
		$vizdata = $dbh->prepare("SELECT dbtype, dbhost, dbuser, dbpass, dbname, query FROM visualizations WHERE name=?");
		//$tab='testab0';
		$vizdata->execute([$vizname]);
		if (!$vizdata) {die("Query to show fields from table failed.");}
		foreach ($vizdata as $dataobjectrow){
		$dbtype = $dataobjectrow['dbtype'];
		$dbhost = $dataobjectrow['dbhost'];
		$dbname = $dataobjectrow['dbname'];
		$dbuser = $dataobjectrow['dbuser'];
		$dbpass = $dataobjectrow['dbpass'];
		$query = $dataobjectrow['query'];
		}

//var_dump($_POST);

//Optional Echo File Name in Data 
echo $filename."
";

/*
 * send response headers to the browser
 * following headers instruct the browser to treat the data as a csv file called export.csv
 */

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="'.$filename.'.csv"');

//Test connection and close/script and variable if failureoccurs
try {

if ($dbtype=='mssql'){
//$mssqldriver = 'SQL Server';
	$vdbh = new PDO("odbc:Driver=SQL Server;Server=".$dbhost.";Database=".$dbname, $dbuser, $dbpass);}
	else{$vdbh = new PDO("mysql:host=".$dbhost.";dbname=".$dbname."", "".$dbuser."", "".$dbpass."");}

$vdbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$viz = $vdbh->prepare($query);
$viz->execute();

} catch (PDOException $e) {return;}

//echo $column0;
$result = $viz->fetchAll(PDO::FETCH_ASSOC);

foreach($result as $key=>$value){
  foreach($value as $keyval=>$val){
    $result2[]=$keyval;
	}
}

//Column names! Finally!
$result3=array_unique($result2);


/*
 * output header row (if atleast one row exists)
 */

if ($result) {
    echocsv($result3);
}

/*
 * output data rows (if atleast one row exists)
 */

foreach ($result as $row){
    echocsv($row);
}

/*
 * echo the input array as csv data maintaining consistency with most CSV implementations
 * - uses double-quotes as enclosure when necessary
 * - uses double double-quotes to escape double-quotes 
 * - uses CRLF as a line separator
 */

function echocsv($fields)
{
    $separator = '';
    foreach ($fields as $field) {
        if (preg_match('/\\r|\\n|,|"/', $field)) {
            $field = '"' . str_replace('"', '""', $field) . '"';
        }
        echo $separator . $field;
        $separator = ',';
    }
    echo "\r\n";
}
?>
