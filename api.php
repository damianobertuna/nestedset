<?php
include("config/config.php");
include("class/Database.php");
include("class/requestData.php");
include("class/responseClass.php");
include("class/nestedSet.php");

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");

try {
    /* clase per interagire con il database, causa una eccezione se
    * la connessione fallisce e il flusso si ferma */
    $db             = new Database($user, $password, $dbname, $host);

    /* $responseObj - inizializzo i valori della struttura da ritornare come risposta */
    $responseObj    = new responseClass(0, array(), 0, 0, "");

    /* $requestData - validiamo i parametri passati tramite GET
    * se la validazione fallisce viene generata una eccezione e il flusso si ferma */
    $requestData    = new requestData($_GET, $responseObj);
    /* $nestedObj - oggetto tramite il quale ricerco i figli del nodo passato tramite GET
    * in base agli altri parametri di filtro (language, search_keyword, page_num e page_size */
    $nestedObj      = new nestedSet($db, $requestData, $responseObj);

    /* il metodo children cerca i nodi figli e ritorna la stringa json creata*/
    $response       = $nestedObj->findChildren();

    echo $response;
} catch (Exception $e) {
    echo $e->getMessage();
}

