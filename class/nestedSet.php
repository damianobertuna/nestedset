<?php

class nestedSet
{
    private $dbconn;
    private $databaseHelper;

    public function __construct($dbconn, DatabaseHelper $databaseHelper)
    {
        $this->dbconn = $dbconn;
        $this->databaseHelper = $databaseHelper;
    }

    public function Children(int $idNode, string $language, string $searchKeyword)
    {
        $nodeLevel = $this->getNodeLevel($idNode);
        $language = mysqli_real_escape_string($this->dbconn, $language);

        $resNodes = $this->databaseHelper->getChildren($idNode, $nodeLevel, $language, $searchKeyword);
        
        $str = "";
        $jsonStructure = array();

        if ($resNodes) {
            $oldRowIndent = -1;
            if ($resNodes->num_rows) {
                while($row = mysqli_fetch_assoc($resNodes))
                {
                    $childrenCount = $this->databaseHelper->childrenCount($idNode);
                    extract($row);
                    $jsonStructure[] = array(
                        'node_id'           => $idNode,
                        'name'              => $nodeName,
                        'children_count'    => $childrenCount,
                    );
                    /*echo "<pre>";
                    var_dump($row);
                    echo "</pre>";
                    exit();*/
                    /*if($row['idNode'] == $idNode){
                        $str .= ("<ul><li>". $row['name']);
                    }
                    elseif($row['level']>$oldRowIndent){
                        $str .= ("<ul><li>".$row['idNode']." - ". $row['name']);
                    }
                    elseif($row['level']==$oldRowIndent){
                        $str .= ("</li><li>".$row['idNode']." - ". $row['name']);
                    }
                    else {
                        $str .= ("</li>");
                        $str .=  str_repeat("</ul></li>", $oldRowIndent-$row['level']);
                        $str .= ("<li>".$row['idNode']." - ". $row['name']);
                    }
                    $oldRowIndent = $row['level'];*/
                }
                /*$str .= ("</li>");
                $str .=  str_repeat("</ul></li>", $oldRowIndent);
                $str .= ("</ul>");*/
            }
        }
        /*var_dump($str);
        exit();*/
        //return json_encode($jsonStructure);
        return $jsonStructure;
    }

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