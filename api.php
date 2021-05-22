<?php
include("config/config.php");
include("class/Database.php");
include("class/requestData.php");
include("class/responseClass.php");
include("class/nestedSet.php");

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");

$db = new Database($user, $password, $dbname, $host);

$jsonResponseStructure      = $jsonStructureObj->getStructure();*/
$responseObj                = new responseClass(0, array(), 0, 0, "");
$requestData                = new requestData($_GET, $responseObj);
$nestedObj                  = new nestedSet($db, $requestData, $responseObj);
$response                   = $nestedObj->Children();
echo $response;