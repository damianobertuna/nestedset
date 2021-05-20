<?php

/**
 * Class nestedSet
 */
class nestedSet
{
    private $dbconn;
    private $helperClass;

    /**
     * nestedSet constructor.
     * @param mysqli $dbconn
     * @param helperClass $helperClass
     */
    public function __construct(mysqli $dbconn, helperClass $helperClass)
    {
        $this->dbconn = $dbconn;
        $this->helperClass = $helperClass;
    }

    /** Questo metodo dato un idNode parent ed i parametri per filtrare la
     *  ricerca, ritorna un json con i dati dei nodi da restituire
     * @param int $idNode
     * @param string $language
     * @param string $searchKeyword
     * @param int $pageNum
     * @param int $pageSize
     * @return false|string
     */
    public function Children(int $idNode, string $language, string $searchKeyword, int $pageNum, int $pageSize)
    {
        global $jsonResponseStructure;
        /*
        * l'array verrà rimpito con i dati relativi ai nodi figli trovati
        */
        $jsonChildrenStructure = array();

        /*
         * recupero i figli del nodo passato tramite GET
         */
        $resNodes = $this->helperClass->getChildren($idNode, $language, $searchKeyword, $pageNum, $pageSize);

        /*
         * calcolo il numero di figli del nodo passato tramite GET
         */
        $rootChildNumber = $this->helperClass->childrenCount($idNode);

        /*
         * salvo nella struttura da ritornare il numero di figli del nodo root passato
         */
        $jsonResponseStructure['rootNodesNumber'] = $rootChildNumber;

        if ($resNodes) {
            if ($resNodes->num_rows) {
                /*
                 * metodo che ritorna i dati dei nodi figli
                 */
                $jsonChildrenStructure = $this->fillJsonChildrenStructure($resNodes);
            }
        }

        $jsonResponseStructure['nodes'] = $jsonChildrenStructure;
        return json_encode($jsonResponseStructure);
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
            $childrenCount = $this->helperClass->childrenCount($idNode);
            $jsonChildrenStructure[] = array(
                'node_id'           => $idNode,
                'name'              => utf8_encode($nodeName),
                'children_count'    => $childrenCount,
            );
        }
        return $jsonChildrenStructure;
    }
}