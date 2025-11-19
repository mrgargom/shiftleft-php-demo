<?php
/**
 * Database Configuration
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $dbPath = __DIR__ . '/../database/academic_advisor.db';
        
        try {
            $this->connection = new PDO('sqlite:' . $dbPath);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Initialize database tables if they don't exist
            $this->initializeTables();
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    private function initializeTables() {
        $sqlFile = __DIR__ . '/../database/migrations/create_tables.sql';
        
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $this->connection->exec($sql);
        }
    }
}
