<?php
//PHPAuth Requirements
require_once("scripts/ZxcvbnPhp/ScorerInterface.php"); 
require_once("scripts/ZxcvbnPhp/Scorer.php"); 
require_once("scripts/ZxcvbnPhp/Searcher.php");
require_once("scripts/ZxcvbnPhp/Matcher.php");
require_once("scripts/ZxcvbnPhp/Matchers/MatchInterface.php");
require_once("scripts/ZxcvbnPhp/Matchers/Match.php");
require_once("scripts/ZxcvbnPhp/Matchers/Bruteforce.php");
require_once("scripts/ZxcvbnPhp/Matchers/DateMatch.php");
require_once("scripts/ZxcvbnPhp/Matchers/DictionaryMatch.php");
require_once("scripts/ZxcvbnPhp/Matchers/DigitMatch.php");
require_once("scripts/ZxcvbnPhp/Matchers/L33tMatch.php");
require_once("scripts/ZxcvbnPhp/Matchers/RepeatMatch.php");
require_once("scripts/ZxcvbnPhp/Matchers/SequenceMatch.php");
require_once("scripts/ZxcvbnPhp/Matchers/SpatialMatch.php");
require_once("scripts/ZxcvbnPhp/Matchers/YearMatch.php");
require_once("scripts/ZxcvbnPhp/Zxcvbn.php");
//PHPAuth Main Includes
include_once("scripts/PHPAuth/Config.php");
include_once("scripts/PHPAuth/Auth.php");

//MySQL PDO Connection (enter host/credentials here!)
$dbh = new PDO("mysql:host=;dbname=", "", "");
$config = new \PHPAuth\Config($dbh);
$auth   = new \PHPAuth\Auth($dbh, $config);

//Get Permissions--if($isedit==1){
$userid = $auth->getSessionUID($auth->getSessionHash());
$usernameget = $dbh->prepare('SELECT email, isactive, isedit, isadmin FROM `users` USRS WHERE USRS.id=?');
$usernameget->execute([$userid]);
foreach ($usernameget as $usr)
		{
		$username=$usr['email'];
		$isactive=$usr['isactive'];
		$isedit=$usr['isedit'];
		$isadmin=$usr['isadmin'];
		}

?>
