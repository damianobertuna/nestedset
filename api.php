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

$validateResponse = $helperClass->validateParams($_GET);

if ($validateResponse === false) {
    echo "<pre>";
    var_dump($jsonResponseStructure);
    echo "</pre>";
} else {
    $idNode         = intval($_GET['node_id']);
    $language       = $_GET['language'];
    $searchKeyword  = $_GET['search_keyword'];
    $pageNum        = 0;
    if (array_key_exists('page_num', $_GET) && $_GET['page_num'] != "") {
        $pageNum    = intval($_GET['page_num']);
    }
    $pageSize       = 100;
    if (array_key_exists('page_size', $_GET) && $_GET['page_size'] != "") {
        $pageSize   = intval($_GET['page_size']);
    }
    $nestedObj = new nestedSet($dbconn, $helperClass);
    $jsonStructure = $nestedObj->Children($idNode, $language, $searchKeyword, $pageNum, $pageSize);
    echo "<pre>";
    var_dump($jsonStructure);
    echo "</pre>";
}
