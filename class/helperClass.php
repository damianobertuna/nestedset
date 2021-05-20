
<?php

/**
 * Class helperClass - metodi helper per fare query al database
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
     * @param $dbconn
     */
    public function __construct($dbconn) {
        $this->dbconn = $dbconn;
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

        if ($searchKeyword != '') {
            $searchKeyword = '%'.$searchKeyword.'%';
            $query .= ' AND LOWER(ntn.nodeName) LIKE ?';
        }
        $startingNode = $pageNum;
        if ($pageNum != 0) {
            $startingNode = (($pageNum - 1) * $pageSize + 1)-1;
        }
        $query .= ' ORDER BY ntn.nodeName ASC LIMIT ?, ?';

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

    /** Questo metodo ritorna il numero di nodi a partire dal dato idNode parent
     * @param $idNode
     * @return float|int
     */
    public function childrenCount($idNode)
    {
        $query = 'SELECT p.idNode AS Parent, COUNT(c.idNode) AS Children
                    FROM node_tree AS p
                    JOIN node_tree AS c
                      ON p.iLeft = (SELECT MAX(s.iLeft) FROM node_tree AS s
                                       WHERE c.iLeft > s.iLeft AND c.iLeft < s.iRight)
                    WHERE p.idNode = '.$idNode.'
                    GROUP BY Parent';

        /*$query = "SELECT COUNT(t.idNode) AS Descendant
                    FROM node_tree AS s
                      JOIN node_tree AS t ON s.iLeft < t.iLeft AND s.iRight > t.iRight
                      LEFT JOIN node_tree_names ntn ON s.idNode = ntn.idNode
                    WHERE ntn.idNode = ".intval($idNode);*/
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
     * @param $params
     * @return bool
     */
    public function validateParams($params)
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
        if (array_key_exists('page_num', $params) && $params['page_num'] != '' && !preg_replace( '/[^0-9]/', '', $params['page_num'])) {
            $jsonResponseStructure['error'] = $errorDictionary[3];
            return false;
        }

        /*
         * page_size può solo essere un numero, uso una regex per verificarlo
         */
        if (array_key_exists('page_size', $params) && $params['page_size'] != '' && !preg_replace( '/[^0-9]/', '', $params['page_size'])) {
            $jsonResponseStructure['error'] = $errorDictionary[4];
            return false;
        }

        /*
         * verifico se i dati page_num e page_size sono
         * coerenti con i dati sul database
         */
        if ((array_key_exists('page_num', $params) && $params['page_num'] != '') &&
            (array_key_exists('page_size', $params) && $params['page_size'] != '')) {
            $idNode             = $params["node_id"];
            $pageNum            = $params["page_num"];
            $pageSize           = $params["page_size"];
            $paginationCheck    = $this->checkPaginationData($idNode, $pageNum, $pageSize);
            if ($paginationCheck === false) {
                return false;
            }
            $jsonResponseStructure['totalPage'] = $paginationCheck;
            $jsonResponseStructure['currentPage'] = $pageNum;
        }
        return true;
    }

    /** Metodo usato per impostare le variabili
     * globali con i dati forniti via GET
     * @param $params
     */
    public function setParams($params) {
        global $idNode;
        global $language;
        global $searchKeyword;
        global $pageNum;
        global $pageSize;

        $idNode         = intval($params['node_id']);
        $language       = $params['language'];
        $searchKeyword  = $params['search_keyword'];
        $pageNum        = 0;
        if (array_key_exists('page_num', $params) && $params['page_num'] != "" && preg_replace( '/[^0-9]/', '', $params['page_num'])) {
            $pageNum    = intval($params['page_num']);
        }
        $pageSize       = 100;
        if (array_key_exists('page_size', $params) && $params['page_size'] != "" && preg_replace( '/[^0-9]/', '', $params['page_size'])) {
            $pageSize   = intval($params['page_size']);
        }
        return;
    }

    /** Metodo usato per testare la coerenza dei parametri page_num e page_size
     * in relazione al node_id passato
     * @param $idNode
     * @param $pageNum
     * @param $pageSize
     */
    private function checkPaginationData($idNode, $pageNum, $pageSize)
    {
        global $errorDictionary;
        global $jsonResponseStructure;

        /*
         * cerco il numero di figli del nodo passato tramite la richiesta
         */
        $nodeChildrenNumber = $this->childrenCount($idNode);

        /*
         * se il numero di figli del nodo è minore o uguale
         * a pageSize, allora potrò avere al più 1 pagina
         */
        $maxPageNumber = 1;
        if ($nodeChildrenNumber <= $pageSize) {
            /*
             * se pageNum è > 1 allora non è valido
             */
            if ($pageNum > 1) {
                $jsonResponseStructure['error'] = $errorDictionary[3];
                return false;
            }
        } else {
            /*
             * se invece il numero di figli è maggiore di pageSize
             * il numero di pagine non può essere superiore alla
             * parte intera della divisione tra numero di figli e pageSize
             */
            //$maxPageNumber = intdiv($nodeChildrenNumber, $pageSize);
            $maxPageNumber = ceil($nodeChildrenNumber/$pageSize);
            if ($pageNum > $maxPageNumber) {
                $jsonResponseStructure['error'] = $errorDictionary[3];
                return false;
            }
        }
        return $maxPageNumber;
    }

}

/*
LISTA DI TUTTI I NODI FIGLI DI UN NODO
SELECT child.idNode, COUNT(*) AS Generation, ntn.nodeName, child.level
FROM node_tree parent
JOIN node_tree child ON child.ileft BETWEEN parent.ileft AND parent.ileft
LEFT JOIN node_tree_names ntn ON ntn.idNode = parent.idNode
WHERE parent.ileft > 1 AND parent.iRight < 24 AND ntn.language = 'english'
GROUP BY child.idNode;
*/


/*
TUTTI I FIGLI DIRETTI DI UN NODO
SELECT Parent, Group_Concat(Child ORDER BY Child) AS Children
FROM (
  SELECT master.idNode AS Parent, child.idNode AS Child
  FROM node_tree master
  JOIN node_tree parent
  JOIN node_tree child ON child.iLeft BETWEEN parent.iLeft AND parent.iRight  
  WHERE parent.iLeft > master.iLeft AND parent.iRight < master.iRight
  GROUP BY master.idNode, child.idNode
  HAVING COUNT(*)=1
) AS tmp
WHERE parent in(7)
GROUP BY Parent;
*/


/* ELENCO DI TUTTI I PADRI CON I PROPRI FIGLI
SELECT p.idNode AS Parent, Group_Concat(c.idNode) AS Children
FROM node_tree AS p
JOIN node_tree AS c
  ON p.iLeft = (SELECT MAX(s.iLeft) FROM node_tree AS s
                   WHERE c.iLeft > s.iLeft AND c.iLeft < s.iRight)
WHERE p.idNode = 7 -- se si elimina questo where ritorna tutti i genitori con i propri figli
GROUP BY Parent;
*/

/* DATO UN idNode torna il doppio dei figli
SELECT COUNT(t.idNode) AS Descendant, ntn.nodeName
FROM node_tree AS s
  JOIN node_tree AS t ON s.iLeft < t.iLeft AND s.iRight > t.iRight
  LEFT JOIN node_tree_names ntn ON s.idNode = ntn.idNode
WHERE ntn.idNode = 5;
*/

/* RAPPRESENTAZIONE VISIVA DELL'ALBERO 
SELECT
  CONCAT( SPACE(2*COUNT(parent.idNode)-2), ntn.nodeName )
  AS 'Organizational chart'
FROM node_tree AS parent
  INNER JOIN node_tree AS child
  ON child.iLeft BETWEEN parent.iLeft AND parent.iRight
LEFT JOIN node_tree_names ntn ON ntn.idNode = child.idNode
GROUP BY child.idNode
ORDER BY child.iLeft;
*/

/*
ritorna vuoto
SELECT child.idNode, ntn.nodeName, child.level
FROM node_tree AS parent
JOIN node_tree AS child ON child.iLeft BETWEEN parent.iLeft AND parent.iRight
LEFT JOIN node_tree_names ntn ON ntn.idNode = child.idNode
WHERE parent.ileft > 1 AND parent.iRight < 24
GROUP BY child.idNode
HAVING COUNT(child.idNode)=1
*/


