<?php
/**
 * Student Model
 * Extends User functionality for student-specific operations
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/User.php';

class Student {
    private $db;
    private $table = 'students';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all students with user information
     */
    public function getAll() {
        $query = "SELECT s.*, u.name, u.email 
                  FROM {$this->table} s
                  INNER JOIN users u ON s.user_id = u.id
                  ORDER BY u.name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get student by ID
     */
    public function getById($id) {
        $query = "SELECT s.*, u.name, u.email 
                  FROM {$this->table} s
                  INNER JOIN users u ON s.user_id = u.id
                  WHERE s.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Get student by user ID
     */
    public function getByUserId($userId) {
        $query = "SELECT s.*, u.name, u.email 
                  FROM {$this->table} s
                  INNER JOIN users u ON s.user_id = u.id
                  WHERE s.user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Get student by student ID
     */
    public function getByStudentId($studentId) {
        $query = "SELECT s.*, u.name, u.email 
                  FROM {$this->table} s
                  INNER JOIN users u ON s.user_id = u.id
                  WHERE s.student_id = :student_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Create a new student (with user)
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
                'role' => 'student'
            ]);
            
            if (!$userId) {
                $this->db->rollBack();
                return false;
            }
            
            // Create student profile
            $query = "INSERT INTO {$this->table} 
                      (user_id, student_id, major, year_level, gpa, phone) 
                      VALUES (:user_id, :student_id, :major, :year_level, :gpa, :phone)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $data['student_id']);
            $stmt->bindParam(':major', $data['major']);
            $stmt->bindParam(':year_level', $data['year_level']);
            $stmt->bindParam(':gpa', $data['gpa']);
            $stmt->bindParam(':phone', $data['phone']);
            
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
     * Update student
     */
    public function update($id, $data) {
        try {
            $this->db->beginTransaction();
            
            // Get student to find user_id
            $student = $this->getById($id);
            if (!$student) {
                $this->db->rollBack();
                return false;
            }
            
            // Update user if name or email changed
            if (isset($data['name']) || isset($data['email'])) {
                $userModel = new User();
                $userModel->update($student['user_id'], [
                    'name' => $data['name'] ?? $student['name'],
                    'email' => $data['email'] ?? $student['email']
                ]);
            }
            
            // Update student profile
            $query = "UPDATE {$this->table} 
                      SET major = :major, 
                          year_level = :year_level, 
                          gpa = :gpa, 
                          phone = :phone,
                          updated_at = CURRENT_TIMESTAMP
                      WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':major', $data['major']);
            $stmt->bindParam(':year_level', $data['year_level']);
            $stmt->bindParam(':gpa', $data['gpa']);
            $stmt->bindParam(':phone', $data['phone']);
            
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
     * Delete student
     */
    public function delete($id) {
        $student = $this->getById($id);
        if (!$student) {
            return false;
        }
        
        // Deleting user will cascade delete student due to foreign key
        $userModel = new User();
        return $userModel->delete($student['user_id']);
    }
    
    /**
     * Search advisors by department or name
     */
    public function searchAdvisors($searchTerm = '', $department = null) {
        $query = "SELECT a.*, u.name, u.email 
                  FROM advisors a
                  INNER JOIN users u ON a.user_id = u.id
                  WHERE 1=1";
        
        if ($searchTerm) {
            $query .= " AND (u.name LIKE :search OR a.department LIKE :search)";
        }
        
        if ($department) {
            $query .= " AND a.department = :department";
        }
        
        $query .= " ORDER BY u.name";
        
        $stmt = $this->db->prepare($query);
        
        if ($searchTerm) {
            $searchParam = "%{$searchTerm}%";
            $stmt->bindParam(':search', $searchParam);
        }
        
        if ($department) {
            $stmt->bindParam(':department', $department);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
