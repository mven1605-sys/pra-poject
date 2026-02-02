<?php
/**
 * Database Configuration
 * File: config/database.php
 * Konfigurasi koneksi database MySQL
 */

// Definisi konstanta database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_piketin');

// Class Database Connection
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    
    public $conn;
    public $error;
    
    /**
     * Constructor - Membuat koneksi database
     */
    public function __construct() {
        $this->conn = null;
        
        try {
            // Membuat koneksi mysqli
            $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
            
            // Set charset ke utf8mb4
            $this->conn->set_charset("utf8mb4");
            
            // Check koneksi
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            die("Database Connection Error: " . $this->error);
        }
    }
    
    /**
     * Get Connection
     * @return mysqli connection object
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Close Connection
     */
    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
    
    /**
     * Escape String untuk mencegah SQL Injection
     * @param string $string
     * @return string
     */
    public function escapeString($string) {
        return $this->conn->real_escape_string($string);
    }
    
    /**
     * Execute Query
     * @param string $query
     * @return mysqli_result or boolean
     */
    public function query($query) {
        try {
            $result = $this->conn->query($query);
            
            if (!$result) {
                throw new Exception("Query Error: " . $this->conn->error);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Fetch All Results
     * @param mysqli_result $result
     * @return array
     */
    public function fetchAll($result) {
        $data = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        
        return $data;
    }
    
    /**
     * Fetch Single Row
     * @param mysqli_result $result
     * @return array or null
     */
    public function fetchOne($result) {
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    /**
     * Get Last Insert ID
     * @return int
     */
    public function lastInsertId() {
        return $this->conn->insert_id;
    }
    
    /**
     * Get Affected Rows
     * @return int
     */
    public function affectedRows() {
        return $this->conn->affected_rows;
    }
    
    /**
     * Begin Transaction
     */
    public function beginTransaction() {
        $this->conn->begin_transaction();
    }
    
    /**
     * Commit Transaction
     */
    public function commit() {
        $this->conn->commit();
    }
    
    /**
     * Rollback Transaction
     */
    public function rollback() {
        $this->conn->rollback();
    }
    
    /**
     * Destructor - Close connection automatically
     */
    public function __destruct() {
        $this->closeConnection();
    }
}

/**
 * Function untuk mendapatkan instance database
 * @return Database object
 */
function getDB() {
    static $database = null;
    
    if ($database === null) {
        $database = new Database();
    }
    
    return $database;
}

/**
 * Function helper untuk execute query cepat
 * @param string $query
 * @return mysqli_result or boolean
 */
function db_query($query) {
    $db = getDB();
    return $db->query($query);
}

/**
 * Function helper untuk fetch all data
 * @param string $query
 * @return array
 */
function db_fetch_all($query) {
    $db = getDB();
    $result = $db->query($query);
    return $db->fetchAll($result);
}

/**
 * Function helper untuk fetch single data
 * @param string $query
 * @return array or null
 */
function db_fetch_one($query) {
    $db = getDB();
    $result = $db->query($query);
    return $db->fetchOne($result);
}

/**
 * Function helper untuk escape string
 * @param string $string
 * @return string
 */
function db_escape($string) {
    $db = getDB();
    return $db->escapeString($string);
}

// Test koneksi (optional - comment setelah berhasil)
// $db = new Database();
// if ($db->conn) {
//     echo "Database connected successfully!";
// }
?>