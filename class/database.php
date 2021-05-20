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
    }

    /**
     * @return false|mysqli
     */
    public function databaseConnection()
    {
        try {
            $conn = new mysqli($this->host, $this->user, $this->password, $this->dbname);
        } catch (mysqli_sql_exception $e) {
            echo $e->getMessage();
            return false;
        }
        return $conn;
    }
}