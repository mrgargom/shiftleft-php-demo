<?php
/**
 * Database Configuration
 * Laravel-style database connection management
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $dbPath = __DIR__ . '/../database/academic_advisor.db';
        $dbDir = dirname($dbPath);
        
        // Create database directory if it doesn't exist
        if (!file_exists($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        
        try {
            $this->connection = new PDO('sqlite:' . $dbPath);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->connection->exec('PRAGMA foreign_keys = ON');
            
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
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->connection->rollBack();
    }
}

/**
 * Helper function to get database instance
 */
function db() {
    return Database::getInstance()->getConnection();
}
