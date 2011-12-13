<?php
require_once(LIB_PATH.DS."config.php");

class Fiche
{
    
    // Holds instance of the class itself
    private static $instance = null;
    // Holds instances of the PDO base class
    private $dbh = null;

    // Get instance of the class itself
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    // The clone and wakeup methods prevents external instantiation of copies of
    // the Singleton class, thus eliminating the possibility of duplicate objects.

    // Clones object
    // @throws RuntimeException always
    public function __clone()
    {
        throw new RuntimeException(
                "Clone of singelton object is not allowed.", 101
        );
    }

    // Reconstructs any resources that the object may have.
    // @throws RuntimeException always
    public function __wakeup()
    {
        throw new RuntimeException('Deserializing is not allowed.', 101);
    }

    // Class constructor method
    // @throws RuntimeException if cannot establish connection with database
    private function __construct()
    {
        $host = DB_SERVER;
        $dbname = DB_NAME;
    
        // To avoid showing database connection details PDO constructor
        // is wrapped in try/catch block and new Exception is thrown        
        try {
            $this->dbh = new PDO("mysql:host=$host;dbname=$dbname", DB_USER, DB_PASS);
            echo "Connected to database <br/>";
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

     /**
     * Gets PDOstatement having executed query
     *
     * @param integer $wisherID
     * @return PDOStatement
     */
    public function get_fiche()
    {
        $query = "";
        $sth = null;

        $query = "
            SELECT FicheCode, FicheNaam
            FROM kls_fiches
            ";
        $sth = $this->dbh->prepare($query);
//        $sth->bindParam(":id_bv", $wisherID, PDO::PARAM_INT);
        $sth->execute();
        return $sth;
    }


    public function close()
    {
        $this->dbh = null;
    }
        
    public function __destruct()
    {
        $this->dbh = null;
    }

}

?>
