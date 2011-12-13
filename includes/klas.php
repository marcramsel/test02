<?php
require_once(LIB_PATH.DS."config.php");

class Klas
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
    public function get_klas()
    {
        $query = "";
        $sth = null;

        $query = "
            SELECT KK1, KK2, KK3
            FROM kls_klas
            ";
        $sth = $this->dbh->prepare($query);
//        $sth->bindParam(":id_bv", $wisherID, PDO::PARAM_INT);
        $sth->execute();
        return $sth;
    }

    /**
     * Stores user record
     *
     * @param string $name
     * @param string $password
     */
    public function create_wisher($name, $password)
    {
        $query = "";
        $sth = null;

        $query = "
            INSERT INTO wishers (name, password)
            VALUES (:name_bv, :pwd_bv)
            ";

        $sth = $this->dbh->prepare($query);
        $sth->bindParam(":name_bv", $name, PDO::PARAM_STR);
        $sth->bindParam(":pwd_bv", $password, PDO::PARAM_STR);
        $sth->execute();
    }

    /**
     *
     * @param string $name
     * @param string $password
     * @return boolean
     */
    public function verify_wisher_credentials($name, $password)
    {
        $query = "";
        $sth = null;
        $row = array();

        $query = "
            SELECT 1
            from wishers
            where name = :name_bv
            and	password = :pwd_bv
       ";
        $sth = $this->dbh->prepare($query);
        $sth->bindParam(":name_bv", $name, PDO::PARAM_STR);
        $sth->bindParam(":pwd_bv", $password, PDO::PARAM_STR);
        $sth->execute();

        //Because name is a unique value I only expect one row
        $row = $sth->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Stores wish record
     *
     * @param integer $wisherID
     * @param string $description
     * @param string $duedate
     */
    function insert_wish($wisherID, $description, $duedate)
    {
        $query = "";
        $sth = null;

        $date = $this->format_date_for_sql($duedate);

        $query = "
            INSERT INTO wishes (wisher_id, description, due_date)
            VALUES (
                :wisher_id_bv,
                :desc_bv,
                set_due_date(:due_date_bv)
                )
            ";

        $sth = $this->dbh->prepare($query);
        $sth->bindParam(":wisher_id_bv", $wisherID, PDO::PARAM_INT);
        $sth->bindParam(':desc_bv', $description, PDO::PARAM_STR);
        $sth->bindParam(':due_date_bv', $date, PDO::PARAM_STR);
        $sth->execute();
    }

    /**
     * Converts date string to timestamp
     *
     * @param string $date
     * @return string
     */
    function format_date_for_sql($date)
    {
        if ($date == "") {
            return null;
        } else {
            $dateTime = new DateTime($date, new DateTimeZone("UTC"));
            return $dateTime->format("Y-n-j H:i:s e");
        }
    }

    public function update_wish($wishID, $description, $duedate)
    {
        $query = "";
        $sth = null;

        $date = $this->format_date_for_sql($duedate);
        var_dump($date, $wishID);

        $query = "
            UPDATE wishes
            SET description = :desc_bv,
            due_date = set_due_date(:due_date_bv)
            WHERE id = :wish_id_bv
            ";
        $sth = $this->dbh->prepare($query);
        $sth->bindParam(":wish_id_bv", $wishID, PDO::PARAM_INT);
        $sth->bindParam(':desc_bv', $description, PDO::PARAM_STR);
        $sth->bindParam(':due_date_bv', $date, PDO::PARAM_STR);
        $result = $sth->execute();
    }

    /**
     * Gets wish record with given #id
     *
     * @param integer $wishID
     * @return array
     */
    public function get_wish_by_wish_id($wishID)
    {
        $query = "";
        $sth = null;
        $row = array();

        $query = "
            SELECT id ID, description DESCRIPTION,
            format_due_date(due_date) DUE_DATE
            FROM wishes
            WHERE id = :wish_id_bv
            ";
        $sth = $this->dbh->prepare($query);
        $sth->bindValue(":wish_id_bv", (int) $wishID, PDO::PARAM_INT);
        $sth->execute();

        //Because name is a unique value I only expect one row
        $row = $sth->fetch(PDO::FETCH_ASSOC);

        $sth = null;

        return $row;
    }

    public function delete_wish($wishID)
    {
        $query = "";
        $sth = null;

        $query = "
            DELETE FROM wishes
            WHERE id = :wish_id_bv
            ";

        $sth = $this->dbh->prepare($query);
        $sth->bindValue(":wish_id_bv", (int) $wishID, PDO::PARAM_INT);
        $sth->execute();
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
