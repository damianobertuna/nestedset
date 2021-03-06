<?php
include("config/config.php");
include("class/Database.php");
include("class/requestException.php");
include("class/requestData.php");
include("class/responseClass.php");
include("class/nestedSet.php");

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");

/* $responseObj - inizializzo i valori della struttura da ritornare come risposta */
$responseObj = new responseClass(0, array(), 0, 0, "");

try {
    /* classe per interagire con il database */
    $db          = new Database($user, $password, $dbname, $host);

    /* $requestData - validiamo i parametri passati tramite GET */
    $requestData = new requestData($_GET, $responseObj);

    /* $nestedObj - oggetto tramite il quale ricerco i figli del nodo passato tramite GET
     * in base agli altri parametri di filtro (language, search_keyword, page_num e page_size) */
    $nestedObj   = new nestedSet($db, $requestData, $responseObj);

    /* il metodo children cerca i nodi figli e ritorna la stringa json generata */
    $response       = $nestedObj->findChildren();

    echo $response;
} catch (requestException $e) {
    $responseObj->setError($e->errorMessage());
    http_response_code(400);
    echo $responseObj->toJson();
} catch (Exception $e) {
    $responseObj->setError("Something has gone wrong");
    http_response_code(500);
    echo $responseObj->toJson();
}
