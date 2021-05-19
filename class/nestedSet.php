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
     * @param $dbconn
     * @param helperClass $helperClass
     */
    public function __construct($dbconn, helperClass $helperClass)
    {
        $this->dbconn = $dbconn;
        $this->helperClass = $helperClass;
    }

    /** Questo metodo dato un idNode parent ed i parametri per filtrare la ricerca ritorna un json con i dati necessari da restituire
     * @param int $idNode
     * @param string $language
     * @param string $searchKeyword
     * @param int $pageNum
     * @param int $pageSize
     * @return array
     */
    public function Children(int $idNode, string $language, string $searchKeyword, int $pageNum, int $pageSize)
    {
        $nodeLevel = $this->getNodeLevel($idNode);
        $language = mysqli_real_escape_string($this->dbconn, $language);

        $resNodes = $this->helperClass->getChildren($idNode, $language, $searchKeyword, $pageNum, $pageSize);

        $jsonChildrenStructure = array();
        $rootChildNumber = $this->helperClass->childrenCount($idNode);
        $jsonResponseStructure['rootNodesNumber'] = $rootChildNumber;

        if ($resNodes) {
            if ($resNodes->num_rows) {
                $jsonChildrenStructure = $this->fillJsonChildrenStructure($resNodes);
            }
        }

        $jsonResponseStructure['nodes'] = $jsonChildrenStructure;

        //return json_encode($jsonStructure);
        return $jsonResponseStructure;
    }

    /**
     * @param $resNodes
     * @return array
     */
    private function fillJsonChildrenStructure($resNodes) {
        while($row = mysqli_fetch_assoc($resNodes))
        {
            extract($row);
            $childrenCount = $this->helperClass->childrenCount($idNode);
            $jsonChildrenStructure[] = array(
                'node_id'           => $idNode,
                'name'              => $nodeName,
                'children_count'    => $childrenCount,
            );
        }
        return $jsonChildrenStructure;
    }

    /**
     * @param $idNode
     * @return mixed|string
     */
    private function getNodeLevel($idNode)
    {
        $query = "SELECT level FROM node_tree WHERE idNode = ".intval($idNode);
        $resLevel = mysqli_query($this->dbconn, $query);
        $resLevel = mysqli_fetch_assoc($resLevel);
        return $resLevel['level'];
    }

}


/*$query = "SELECT node.idNode as idNode, ntn.nodeName AS name, (COUNT(parent.idNode) - 1) as indent, node.level
        FROM node_tree node LEFT JOIN node_tree_names ntn ON node.idNode = ntn.idNode, 
        node_tree parent
        WHERE node.iLeft BETWEEN parent.iLeft AND language = 'italian' AND parent.iRight  
        GROUP BY ntn.nodeName
        ORDER BY node.iLeft";*/
        /*$query = 'SELECT ntn.nodeName name, node.idNode idNode, node.iLeft, node.iRight, (COUNT(parent.idNode) - 1) as indent
    FROM node_tree node
    LEFT JOIN node_tree_names ntn ON ntn.idNode = node.idNode,
    node_tree parent
    WHERE node.iLeft BETWEEN parent.iLeft AND parent.iRight
    AND parent.idNode = '.intval($idNode).' AND ntn.language = \'italian\'
    ORDER BY node.iLeft';*/