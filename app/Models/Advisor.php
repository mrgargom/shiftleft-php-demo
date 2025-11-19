<?php
/**
 * Advisor Model
 */

require_once __DIR__ . '/../../config/database.php';

class Advisor {
    private $db;
    private $table = 'advisors';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all advisors
     */
    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY last_name, first_name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get advisor by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Get advisor by advisor ID
     */
    public function getByAdvisorId($advisorId) {
        $query = "SELECT * FROM {$this->table} WHERE advisor_id = :advisor_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':advisor_id', $advisorId);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Create a new advisor
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (advisor_id, first_name, last_name, email, phone, department, office_location) 
                  VALUES (:advisor_id, :first_name, :last_name, :email, :phone, :department, :office_location)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':advisor_id', $data['advisor_id']);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':department', $data['department']);
        $stmt->bindParam(':office_location', $data['office_location']);
        
        return $stmt->execute();
    }
    
    /**
     * Update advisor
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET first_name = :first_name, 
                      last_name = :last_name, 
                      email = :email, 
                      phone = :phone, 
                      department = :department, 
                      office_location = :office_location,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':department', $data['department']);
        $stmt->bindParam(':office_location', $data['office_location']);
        
        return $stmt->execute();
    }
    
    /**
     * Delete advisor
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
