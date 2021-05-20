
<?php

/**
 * Class helperClass - metodi helper per fare query al database
 * e verificare parametri passati tramite GET
 * e per fornire metodi utili all verifica della paginazione
 */
class helperClass
{
    /**
     * Database constructor.
     * @param $dbconn
     */
    private $dbconn;

    /**
     * helperClass constructor.
     * @param mysqli $dbconn
     */
    public function __construct(mysqli $dbconn) {
        if (is_object($dbconn) && get_class($dbconn) == 'mysqli') {
            $this->dbconn = $dbconn;
        } else {
            throw new ErrorException('Impossibile inizializzare classe helperClass');
        }
    }

    /** Questo metodo dati i seguenti parametri ritorna i figli di un dato nodo parent
     * @param int $idNode
     * @param string $language
     * @param string $searchKeyword
     * @param int $pageNum
     * @param int $pageSize
     * @return bool|mysqli_result
     */
    public function getChildren(int $idNode, string $language, string $searchKeyword, int $pageNum, int $pageSize)
    {
        $query = 'SELECT Child.idNode, Child.iLeft, Child.iRight, ntn.nodeName, Child.level level 
FROM node_tree Child 
    LEFT JOIN node_tree_names ntn ON ntn.idNode = Child.idNode,  node_tree Parent 
WHERE Child.level = Parent.level + 1 
  AND Child.iLeft > Parent.iLeft 
  AND Child.iRight < Parent.iRight 
  AND Parent.idNode = ? 
  AND ntn.language = ?';

        /*
         * se il parametro search_keyword non è vuoto allora filtro
         */
        if ($searchKeyword != '') {
            $searchKeyword = '%'.$searchKeyword.'%';
            $query .= ' AND LOWER(ntn.nodeName) LIKE ?';
        }

        /*
         * calcolo i valori per LIMIT e OFFSET relativi alla paginazione
         */
        $startingNode = $pageNum;
        if ($pageNum != 0) {
            $startingNode = (($pageNum - 1) * $pageSize + 1)-1;
        }
        $query .= ' ORDER BY ntn.nodeName ASC LIMIT ?, ?';

        /*
         * faccio il bind dei parametri per la query
         */
        $stmt = $this->dbconn->prepare($query);
        if ($searchKeyword != '') {
            $stmt->bind_param("issii", $idNode, $language, $searchKeyword, $startingNode, $pageSize);
        } else {
            $stmt->bind_param("isii", $idNode, $language, $startingNode, $pageSize);
        }
        $stmt->execute();
        $resNodes = $stmt->get_result();
        return $resNodes;
    }

    /** Questo metodo ritorna il numero di nodi figli a partire dal dato idNode parent
     * @param int $idNode
     * @return float|int
     */
    public function childrenCount(int $idNode)
    {
        $query = 'SELECT p.idNode AS Parent, COUNT(c.idNode) AS Children
                    FROM node_tree AS p
                    JOIN node_tree AS c
                      ON p.iLeft = (SELECT MAX(s.iLeft) FROM node_tree AS s
                                       WHERE c.iLeft > s.iLeft AND c.iLeft < s.iRight)
                    WHERE p.idNode = '.$idNode.'
                    GROUP BY Parent';

        $childrenCount = mysqli_query($this->dbconn, $query);
        $childrenCount = mysqli_fetch_assoc($childrenCount);
        if (isset($childrenCount["Children"])) {
            $childrenCount = $childrenCount["Children"];
        } else {
            $childrenCount = 0;
        }
        return $childrenCount;
    }

    /** metodo usato per validare i parametri forniti tramite GET
     * @param array $params
     * @return bool
     */
    public function validateParams(array $params)
    {
        global $errorDictionary;
        global $jsonResponseStructure;
        $mandatoryParams = array('node_id', 'language');

        /*
         * verifico se qualche parametro required è mancante (node_id e language)
         */
        foreach ($mandatoryParams as $mandatory) {
            if (!array_key_exists($mandatory, $params)) {
                $jsonResponseStructure['error'] = $errorDictionary[2];
                return false;
            }
        }

        /*
         * node_id non può essere vuoto
         */
        if (array_key_exists('node_id', $params) && $params['node_id'] == '') {
            $jsonResponseStructure['error'] = $errorDictionary[1];
            return false;
        }

        /*
         * page_num può solo essere un numero, uso una regex per verificarlo
         */
        if (array_key_exists('page_num', $params) && $params['page_num'] != '' &&
            !preg_replace( '/[^0-9]/', '', $params['page_num'])) {
            $jsonResponseStructure['error'] = $errorDictionary[3];
            return false;
        }

        /*
         * page_size può solo essere un numero, uso una regex per verificarlo
         */
        if (array_key_exists('page_size', $params) && $params['page_size'] != '' &&
            !preg_replace( '/[^0-9]/', '', $params['page_size'])) {
            $jsonResponseStructure['error'] = $errorDictionary[4];
            return false;
        }

        /*
         * verifico se il valore di page_num e page_size sono
         * coerenti con i dati sul database
         */
        if ((array_key_exists('page_num', $params) && $params['page_num'] != '') &&
            (array_key_exists('page_size', $params) && $params['page_size'] != '')) {
            $idNode             = $params["node_id"];
            $pageNum            = $params["page_num"];
            $pageSize           = $params["page_size"];

            /*
             * il metodo checkPaginationData calcola se i valori page_num e page_size
             * sono coerenti con il numero di figli di idNode; ritorna false oppure
             * il numero totale di pagine in base al page_size
             */
            $paginationCheck    = $this->checkPaginationData($idNode, $pageNum, $pageSize);
            if ($paginationCheck === false) {
                return false;
            }

            /*
             * se non c'è errore, inserisco nella struttura il
             * numero totale di pagine calcolato e la pagina corrente
             */
            $jsonResponseStructure['totalPage'] = $paginationCheck;
            $jsonResponseStructure['currentPage'] = $pageNum;
        }
        return true;
    }

    /** Metodo usato per impostare le variabili
     * globali con i dati forniti via GET
     * @param array $params
     */
    public function setParams(array $params) {
        global $idNode;
        global $language;
        global $searchKeyword;
        global $pageNum;
        global $pageSize;

        /*
         * inizializzo le variabili globali definite in config.php
         * con i valori passati tramite get precedentemente validati
         */
        $idNode         = intval($params['node_id']);
        $language       = $params['language'];
        $searchKeyword  = $params['search_keyword'];

        /*
         * valore di default
         */
        $pageNum        = 1;
        if (array_key_exists('page_num', $params) && $params['page_num'] != "" && preg_replace( '/[^0-9]/', '', $params['page_num'])) {
            $pageNum    = intval($params['page_num']);
        }

        /*
         * valore di default
         */
        $pageSize       = 100;
        if (array_key_exists('page_size', $params) && $params['page_size'] != "" && preg_replace( '/[^0-9]/', '', $params['page_size'])) {
            $pageSize   = intval($params['page_size']);
        }
        return;
    }

    /** Metodo usato per testare la coerenza dei parametri page_num e page_size
     * in relazione al node_id passato
     * @param int $idNode
     * @param int $pageNum
     * @param int $pageSize
     */
    private function checkPaginationData(int $idNode, int $pageNum, int $pageSize)
    {
        global $errorDictionary;
        global $jsonResponseStructure;

        /*
         * calcolo il numero di figli del nodo passato tramite la richiesta
         */
        $nodeChildrenNumber = $this->childrenCount($idNode);

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
                $jsonResponseStructure['error'] = $errorDictionary[3];
                return false;
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
                $jsonResponseStructure['error'] = $errorDictionary[3];
                return false;
            }
        }
        return $maxPageNumber;
    }

}