<?php
// Ensure reporting is setup correctly

class Database
{
    private $user;
    private $password;
    private $dbname;
    private $host;

    /**
     * Database constructor.
     * @param $user
     * @param $password
     * @param $dbname
     * @param $host
     */
    public function __construct($user, $password, $dbname, $host)
    {
        $this->user = $user;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->host = $host;

    }

    public function databaseConnection()
    {
        mysqli_report(MYSQLI_REPORT_STRICT);

        try {
            //$conn = mysqli_connect($this->host, $this->user, $this->password, $this->dbname);
            $conn = new mysqli($this->host, $this->user, $this->password, $this->dbname);
        } catch (Exception $e) {
            throw $e;
            echo "Problem connecting to database: ".$e->getMessage();
            return false;
        }
        return $conn;
    }
}