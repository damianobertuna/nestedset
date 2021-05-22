<?php

/**
 * Class nestedSet
 */
class nestedSet
{
    private $db;
    private $requestData;
    private $responseObj;

    /**
     * nestedSet constructor.
     * @param Database $db
     * @param requestData $requestData
     * @param responseClass $responseObj
     */
    public function __construct(Database $db, requestData $requestData, responseClass $responseObj)
    {
        $this->db           = $db;
        $this->requestData  = $requestData;
        $this->responseObj  = $responseObj;
    }


    /** Questo metodo dato un idNode parent ed i parametri per filtrare la
     *  ricerca, ritorna un json con i dati dei nodi da restituire
     * * @return false|string
     */
    public function Children()
    {
        $idNode                 = $this->requestData->getIdNode();
        $language               = $this->requestData->getLanguage();
        $searchKeyword          = $this->requestData->getSearchKeyword();
        $pageNum                = $this->requestData->getPageNum();
        $pageSize               = $this->requestData->getPageSize();

        try {
            $totalPage = $this->checkPaginationData($idNode, $pageNum, $pageSize);
            $this->responseObj->setTotalPage($totalPage);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $this->responseObj->setCurrentPage($pageNum);

        /*
         * recupero i figli del nodo passato tramite GET
         */
        $resNodes = $this->db->getChildren($idNode, $language, $searchKeyword, $pageNum, $pageSize);

        /*
         * calcolo il numero di figli del nodo passato tramite GET
         */
        $rootChildNumber = $this->db->childrenCount($idNode);

        /*
         * salvo nella struttura da ritornare il numero di figli del nodo root passato
         */
        $this->responseObj->setRootNodesNumber($rootChildNumber);

        /*
        * l'array verrà rimpito con i dati relativi ai nodi figli trovati
        */
        $jsonChildrenStructure = array();
        if ($resNodes) {
            if ($resNodes->num_rows) {
                /*
                 * metodo che ritorna i dati dei nodi figli
                 */
                $jsonChildrenStructure = $this->fillJsonChildrenStructure($resNodes);
            }
        }

        $this->responseObj->setNodes($jsonChildrenStructure);
        $response = $this->responseObj->getStructure();
        return $this->responseObj->toJson($response);
    }

    /** metodo che crea e ritorna un array con i dati dei nodi trovati
     * @param mysqli_result $resNodes
     * @return array
     */
    private function fillJsonChildrenStructure(mysqli_result $resNodes) {
        $jsonChildrenStructure = array();
        while($row = mysqli_fetch_assoc($resNodes))
        {
            extract($row);
            $childrenCount = $this->db->childrenCount($idNode);
            $jsonChildrenStructure[] = array(
                'node_id'           => $idNode,
                'name'              => utf8_encode($nodeName),
                'children_count'    => $childrenCount,
            );
        }
        return $jsonChildrenStructure;
    }

    /** Metodo usato per testare la coerenza dei parametri page_num e page_size
     * in relazione al node_id passato, ritorna il numero max di pagine in relazione
     * ai nodi figli trovati, se c'è qualche errore con i dati di paginazione passati
     * lancia una eccezione
     * @param int $idNode
     * @param int $pageNum
     * @param int $pageSize
     */
    private function checkPaginationData(int $idNode, int $pageNum, int $pageSize)
    {
        /*
         * calcolo il numero di figli del nodo passato tramite la richiesta
         */
        $nodeChildrenNumber = $this->db->childrenCount($idNode);

        $maxPageNumber = 1;
        /*
         * se il numero di figli del nodo è minore o uguale
         * a pageSize passato, allora potrò avere al più 1 pagina
         */
        if ($nodeChildrenNumber <= $pageSize) {
            /*
             * se pageNum è > 1 allora la richiesta non è valida
             */
            if ($pageNum > 1) {
                throw new Exception($this->requestData->getErrorMessage('invalid_page_num'));
            }
        } else {
            /*
             * se invece il numero di figli è maggiore di pageSize
             * il numero di pagine non può essere superiore al
             * risultato della divisione tra numero di figli e pageSize
             * arrotondato all'intero superiore
             */
            $maxPageNumber = ceil($nodeChildrenNumber/$pageSize);
            if ($pageNum > $maxPageNumber) {
                throw new Exception($this->requestData->getErrorMessage('invalid_page_num'));
            }
        }
        return $maxPageNumber;
    }
}