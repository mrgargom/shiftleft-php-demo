<?php
/**
 * Student Model
 */

require_once __DIR__ . '/../../config/database.php';

class Student {
    private $db;
    private $table = 'students';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all students
     */
    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY last_name, first_name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get student by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Get student by student ID
     */
    public function getByStudentId($studentId) {
        $query = "SELECT * FROM {$this->table} WHERE student_id = :student_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Create a new student
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (student_id, first_name, last_name, email, phone, major, year_level, gpa) 
                  VALUES (:student_id, :first_name, :last_name, :email, :phone, :major, :year_level, :gpa)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $data['student_id']);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':major', $data['major']);
        $stmt->bindParam(':year_level', $data['year_level']);
        $stmt->bindParam(':gpa', $data['gpa']);
        
        return $stmt->execute();
    }
    
    /**
     * Update student
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET first_name = :first_name, 
                      last_name = :last_name, 
                      email = :email, 
                      phone = :phone, 
                      major = :major, 
                      year_level = :year_level, 
                      gpa = :gpa,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':major', $data['major']);
        $stmt->bindParam(':year_level', $data['year_level']);
        $stmt->bindParam(':gpa', $data['gpa']);
        
        return $stmt->execute();
    }
    
    /**
     * Delete student
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
