<?php

include("class/Database.php");
include("class/nestedSet.php");
include("class/databaseHelper.php");
include("config/config.php");

/*header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");*/

$db = new Database($user, $password, $dbname, $host);
$dbconn = $db->databaseConnection();
$databaseHelper = new DatabaseHelper($dbconn);

/*if (!empty($dbconn)) {
    echo json_encode(array("response"=>"Database connection OK"));
    //echo "Database connection OK";
} else {
    echo json_encode($jsonResponseStructure['error'] = "Problems connecting to database");
    //echo "Problems connecting to database";
    exit();
}*/

$idNode         = $_GET['idNode'];
$language       = $_GET['language'];
$searchKeyword  = $_GET['search_keyword'];

$nestedObj = new nestedSet($dbconn, $databaseHelper);
$jsonStructure = $nestedObj->Children($idNode, $language, $searchKeyword);
echo "<pre>";
var_dump($jsonStructure);
echo "</pre>";
