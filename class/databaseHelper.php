
<?php
// Ensure reporting is setup correctly

class DatabaseHelper
{
    /**
     * Database constructor.
     * @param $dbconn
     */
    private $dbconn;

    public function __construct($dbconn) {
        $this->dbconn = $dbconn;
    }

    public function getChildren($idNode, $nodeLevel, $language, $searchKeyword)
    {
        $query = 'SELECT Child.idNode, Child.iLeft, Child.iRight, ntn.nodeName, Child.level level FROM node_tree Child LEFT JOIN node_tree_names ntn ON ntn.idNode = Child.idNode,  node_tree Parent WHERE Child.level = Parent.level + 1 AND Child.iLeft > Parent.iLeft AND Child.iRight < Parent.iRight AND Parent.idNode = '.$idNode.' AND ntn.language = "'.$language.'"';

        if ($searchKeyword != '') {
            $query .= ' AND LOWER(ntn.nodeName) LIKE "%'.$searchKeyword.'%"';
        }

        $resNodes = mysqli_query($this->dbconn, $query);
        return $resNodes;
    }

    public function getAllChildrenByIdNode()
    {
        $query = "SELECT child.idNode, COUNT(*) AS Generation, ntn.nodeName, child.level FROM node_tree parent JOIN node_tree child ON child.ileft BETWEEN parent.ileft AND parent.ileft LEFT JOIN node_tree_names ntn ON ntn.idNode = parent.idNode WHERE parent.ileft > 1 AND parent.iRight < 24 AND ntn.language = 'english' GROUP BY child.idNode";
        $resNodes = mysqli_query($this->dbconn, $query);
        return $resNodes;
    }

    public function childrenCount($idNode)
    {
        $query = "SELECT COUNT(t.idNode) AS Descendant
                    FROM node_tree AS s
                      JOIN node_tree AS t ON s.iLeft < t.iLeft AND s.iRight > t.iRight
                      LEFT JOIN node_tree_names ntn ON s.idNode = ntn.idNode
                    WHERE ntn.idNode = ".intval($idNode);
        $childrenCount = mysqli_query($this->dbconn, $query);
        $childrenCount = mysqli_fetch_assoc($childrenCount);
        $childrenCount = $childrenCount["Descendant"]/2;
        return $childrenCount;
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


