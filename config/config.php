<?php
/* Database connection data */
$user = "root";
$password = "1234qwer";
$dbname = "nestedset";
$host = "localhost";

/**
 * jsonResponseStructure
 */
$jsonResponseStructure = array(
    'rootNodesNumber' => 0,
    'nodes' => array(),
    'totalPage' => 1,
    'currentPage' => 1,
    'error' => ''
);

/**
 * variabili che saranno riempite con i parametri della richiesta
 */
$idNode         = 0;
$language       = "";
$searchKeyword  = "";
$pageNum        = 0;
$pageSize       = 100;

/**
 * Error dictionary
 */
$errorDictionary = array(
    1 => 'Invalid node id',
    2 => 'Missing mandatory params',
    3 => 'Invalid page number request',
    4 => 'Invalid page size requested',
    5 => 'Database connection error'
);



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

/** Questo metodo ritorna il livello di un dato nodo
 * @param $idNode
 * @return mixed|string
 */
/*private function getNodeLevel($idNode)
{
    $query = "SELECT level FROM node_tree WHERE idNode = ".intval($idNode);
    $resLevel = mysqli_query($this->dbconn, $query);
    $resLevel = mysqli_fetch_assoc($resLevel);
    return $resLevel['level'];
}*/

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