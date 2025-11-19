<?php
/**
 * Administrator Model
 * Extends User functionality for administrator-specific operations
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/User.php';

class Administrator {
    private $db;
    private $table = 'administrators';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all administrators
     */
    public function getAll() {
        $query = "SELECT adm.*, u.name, u.email 
                  FROM {$this->table} adm
                  INNER JOIN users u ON adm.user_id = u.id
                  ORDER BY u.name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get administrator by ID
     */
    public function getById($id) {
        $query = "SELECT adm.*, u.name, u.email 
                  FROM {$this->table} adm
                  INNER JOIN users u ON adm.user_id = u.id
                  WHERE adm.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Get administrator by user ID
     */
    public function getByUserId($userId) {
        $query = "SELECT adm.*, u.name, u.email 
                  FROM {$this->table} adm
                  INNER JOIN users u ON adm.user_id = u.id
                  WHERE adm.user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Create a new administrator (with user)
     */
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            // Create user first
            $userModel = new User();
            $userId = $userModel->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'administrator'
            ]);
            
            if (!$userId) {
                $this->db->rollBack();
                return false;
            }
            
            // Create administrator profile
            $query = "INSERT INTO {$this->table} 
                      (user_id, admin_id) 
                      VALUES (:user_id, :admin_id)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':admin_id', $data['admin_id']);
            
            if ($stmt->execute()) {
                $this->db->commit();
                return $this->db->lastInsertId();
            }
            
            $this->db->rollBack();
            return false;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Delete administrator
     */
    public function delete($id) {
        $admin = $this->getById($id);
        if (!$admin) {
            return false;
        }
        
        // Deleting user will cascade delete administrator due to foreign key
        $userModel = new User();
        return $userModel->delete($admin['user_id']);
    }
    
    /**
     * Log admin action
     */
    public function logAction($userId, $action, $entityType = null, $entityId = null, $description = null) {
        $query = "INSERT INTO audit_logs 
                  (user_id, action, entity_type, entity_id, description, ip_address, user_agent) 
                  VALUES (:user_id, :action, :entity_type, :entity_id, :description, :ip_address, :user_agent)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':entity_type', $entityType);
        $stmt->bindParam(':entity_id', $entityId, PDO::PARAM_INT);
        $stmt->bindParam(':description', $description);
        
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $stmt->bindParam(':ip_address', $ipAddress);
        $stmt->bindParam(':user_agent', $userAgent);
        
        return $stmt->execute();
    }
    
    /**
     * Get audit logs
     */
    public function getAuditLogs($limit = 100, $offset = 0) {
        $query = "SELECT al.*, u.name as user_name, u.email as user_email
                  FROM audit_logs al
                  LEFT JOIN users u ON al.user_id = u.id
                  ORDER BY al.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
