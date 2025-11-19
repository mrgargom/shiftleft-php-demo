<?php
/**
 * Advisor Model
 * Extends User functionality for advisor-specific operations
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/User.php';

class Advisor {
    private $db;
    private $table = 'advisors';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all advisors with user information
     */
    public function getAll() {
        $query = "SELECT a.*, u.name, u.email 
                  FROM {$this->table} a
                  INNER JOIN users u ON a.user_id = u.id
                  ORDER BY u.name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get advisor by ID
     */
    public function getById($id) {
        $query = "SELECT a.*, u.name, u.email 
                  FROM {$this->table} a
                  INNER JOIN users u ON a.user_id = u.id
                  WHERE a.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Get advisor by user ID
     */
    public function getByUserId($userId) {
        $query = "SELECT a.*, u.name, u.email 
                  FROM {$this->table} a
                  INNER JOIN users u ON a.user_id = u.id
                  WHERE a.user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Get advisor by advisor ID
     */
    public function getByAdvisorId($advisorId) {
        $query = "SELECT a.*, u.name, u.email 
                  FROM {$this->table} a
                  INNER JOIN users u ON a.user_id = u.id
                  WHERE a.advisor_id = :advisor_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':advisor_id', $advisorId);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Create a new advisor (with user)
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
                'role' => 'advisor'
            ]);
            
            if (!$userId) {
                $this->db->rollBack();
                return false;
            }
            
            // Create advisor profile
            $query = "INSERT INTO {$this->table} 
                      (user_id, advisor_id, department, office_location, phone_number) 
                      VALUES (:user_id, :advisor_id, :department, :office_location, :phone_number)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':advisor_id', $data['advisor_id']);
            $stmt->bindParam(':department', $data['department']);
            $stmt->bindParam(':office_location', $data['office_location']);
            $stmt->bindParam(':phone_number', $data['phone_number']);
            
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
     * Update advisor
     */
    public function update($id, $data) {
        try {
            $this->db->beginTransaction();
            
            // Get advisor to find user_id
            $advisor = $this->getById($id);
            if (!$advisor) {
                $this->db->rollBack();
                return false;
            }
            
            // Update user if name or email changed
            if (isset($data['name']) || isset($data['email'])) {
                $userModel = new User();
                $userModel->update($advisor['user_id'], [
                    'name' => $data['name'] ?? $advisor['name'],
                    'email' => $data['email'] ?? $advisor['email']
                ]);
            }
            
            // Update advisor profile
            $query = "UPDATE {$this->table} 
                      SET department = :department, 
                          office_location = :office_location, 
                          phone_number = :phone_number,
                          updated_at = CURRENT_TIMESTAMP
                      WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':department', $data['department']);
            $stmt->bindParam(':office_location', $data['office_location']);
            $stmt->bindParam(':phone_number', $data['phone_number']);
            
            if ($stmt->execute()) {
                $this->db->commit();
                return true;
            }
            
            $this->db->rollBack();
            return false;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Delete advisor
     */
    public function delete($id) {
        $advisor = $this->getById($id);
        if (!$advisor) {
            return false;
        }
        
        // Deleting user will cascade delete advisor due to foreign key
        $userModel = new User();
        return $userModel->delete($advisor['user_id']);
    }
    
    /**
     * Get advisors by department
     */
    public function getByDepartment($department) {
        $query = "SELECT a.*, u.name, u.email 
                  FROM {$this->table} a
                  INNER JOIN users u ON a.user_id = u.id
                  WHERE a.department = :department
                  ORDER BY u.name";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':department', $department);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
