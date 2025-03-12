<?php
// Prevent multiple inclusions
if (!defined('DATABASE_CONNECT_INCLUDED')) {
    define('DATABASE_CONNECT_INCLUDED', true);

    class Database {
        private $host = '127.0.0.1';
        private $username = 'root';
        private $password = '';
        private $database = 'ecomp1';
        public $conn;

        public function __construct() {
            try {
                $this->conn = new PDO("mysql:host=$this->host;dbname=$this->database", $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }

        // Optional: Singleton pattern
        private static $instance = null;

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new Database();
            }
            return self::$instance;
        }
    }

    // Create a global connection variable
    $db = Database::getInstance();
    $conn = $db->conn; // PDO connection object for use elsewhere
}
?>