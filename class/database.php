<?php
mysqli_report(MYSQLI_REPORT_STRICT);

/**
 * Class Database
 */
class Database
{
    private $user;
    private $password;
    private $dbname;
    private $host;
    private $dbconn;

    /**
     * Database constructor.
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @param string $host
     */
    public function __construct(string $user, string $password, string $dbname, string $host)
    {
        $this->user = $user;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->host = $host;
        try {
            $conn = new mysqli($this->host, $this->user, $this->password, $this->dbname);
            $this->dbconn = $conn;
        } catch (mysqli_sql_exception $e) {
            throw new requestException($e->getMessage());
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

        /* se il parametro search_keyword non è vuoto allora filtro */
        if ($searchKeyword != '') {
            $searchKeyword = '%'.$searchKeyword.'%';
            $query .= ' AND LOWER(ntn.nodeName) LIKE ?';
        }

        /* calcolo i valori per LIMIT e OFFSET relativi alla paginazione */
        $startingNode = $pageNum;
        if ($pageNum != 0) {
            $startingNode = (($pageNum - 1) * $pageSize + 1)-1;
        }
        $query .= ' ORDER BY ntn.nodeName ASC LIMIT ?, ?';

        /* faccio il bind dei parametri per la query */
        $stmt = $this->dbconn->prepare($query);
        if ($searchKeyword != '') {
            $stmt->bind_param("issii", $idNode, $language, $searchKeyword, $startingNode, $pageSize);
        } else {
            $stmt->bind_param("isii", $idNode, $language, $startingNode, $pageSize);
        }
        $stmt->execute();
        return $stmt->get_result();
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
                    WHERE p.idNode = ?
                    GROUP BY Parent';

        $stmt = $this->dbconn->prepare($query);
        $stmt->bind_param("i", $idNode);
        $stmt->execute();
        $childrenCount = $stmt->get_result();
        $childrenCount = $childrenCount->fetch_assoc();
        if (isset($childrenCount["Children"])) {
            $childrenCount = $childrenCount["Children"];
        } else {
            $childrenCount = 0;
        }
        return $childrenCount;
    }
}