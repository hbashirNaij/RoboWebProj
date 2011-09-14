<?php
/**
 * This is a small procedural page to set the activated field for the user's account
 */
function __autoload($class)
{
	require_once $class . '.php';
}

$code = $_GET["acode"];
$dbConnection = new relationalDbConnections('RoboticsSIS', 'localhost:8889', 'root', 'root');

$resourceid = $dbConnection->selectFromTable("RoboUsers", "ActivationCode", $acode);
$arr = $dbConnection->formatQueryResults($resourceid, "ActivationCode");
if (is_null($arr[0])) // NOTE: can't destinguish between null value in table and invalid attribute parameter (both return array with single, null element)
{
	error_log("This account has already been activated.");
	echo 'This account has already been activated.';
	return false; // cuts execution early
}

$bool = 1; // any nonzero value to indicated activated status
$stuffing = "activated"; // clears the activation code field for clarity
$array = array("Activated" => $bool, "ActivationCode" => $stuffing);
$dbConnection->updateTable("RoboUsers", "RoboUsers", "ActivationCode", $code, "UserID", $array, "ActivationCode = '$code'");

print 'Congratulations! Your account has been activated.';

?>