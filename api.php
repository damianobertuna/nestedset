<?php

include("config/config.php");
include("class/Database.php");
include("class/nestedSet.php");
include("class/helperClass.php");

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");

$db = new Database($user, $password, $dbname, $host);
$dbconn = $db->databaseConnection();

if (empty($dbconn)) {
    echo json_encode($jsonResponseStructure['error'] = "Problems connecting to database");
    echo "Problems connecting to database";
    exit();
}

/**
 * Class helperClass - metodi helper per fare query al database
 * e verificare parametri passati tramite GET
 * e per fornire metodi utili all verifica della paginazione
 */
$helperClass = new helperClass($dbconn);

/**
 * richiamo il metodo per validare i parametri passati tramite GET
 */
$validateResponse = $helperClass->validateParams($_GET);

if ($validateResponse === false) {
    echo $jsonResponseStructure;
} else {
    $helperClass->setParams($_GET);
    $nestedObj = new nestedSet($dbconn, $helperClass);
    $jsonStructure = $nestedObj->Children($idNode, $language, $searchKeyword, $pageNum, $pageSize);
    echo $jsonStructure;
}
