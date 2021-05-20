<?php

include("config/config.php");
include("class/Database.php");
include("class/nestedSet.php");
include("class/helperClass.php");

/*header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");*/

$db = new Database($user, $password, $dbname, $host);
$dbconn = $db->databaseConnection();
$helperClass = new helperClass($dbconn);

/*if (!empty($dbconn)) {
    echo json_encode(array("response"=>"Database connection OK"));
    //echo "Database connection OK";
} else {
    echo json_encode($jsonResponseStructure['error'] = "Problems connecting to database");
    //echo "Problems connecting to database";
    exit();
}*/

/**
 * richiamo il metodo per validare i parametri passati tramite GET
 */
$validateResponse = $helperClass->validateParams($_GET);

if ($validateResponse === false) {
    echo "<pre>";
    var_dump($jsonResponseStructure);
    echo "</pre>";
} else {
    $helperClass->setParams($_GET);
    $nestedObj = new nestedSet($dbconn, $helperClass);
    $jsonStructure = $nestedObj->Children($idNode, $language, $searchKeyword, $pageNum, $pageSize);
    echo "<pre>";
    var_dump($jsonStructure);
    echo "</pre>";
}
