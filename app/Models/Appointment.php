<?php
/**
 * Appointment Model
 */

require_once __DIR__ . '/../../config/database.php';

class Appointment {
    private $db;
    private $table = 'appointments';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all appointments
     */
    public function getAll() {
        $query = "SELECT a.*, 
                         s.first_name as student_first_name, 
                         s.last_name as student_last_name,
                         adv.first_name as advisor_first_name, 
                         adv.last_name as advisor_last_name
                  FROM {$this->table} a
                  LEFT JOIN students s ON a.student_id = s.id
                  LEFT JOIN advisors adv ON a.advisor_id = adv.id
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get appointment by ID
     */
    public function getById($id) {
        $query = "SELECT a.*, 
                         s.first_name as student_first_name, 
                         s.last_name as student_last_name,
                         s.email as student_email,
                         adv.first_name as advisor_first_name, 
                         adv.last_name as advisor_last_name,
                         adv.email as advisor_email
                  FROM {$this->table} a
                  LEFT JOIN students s ON a.student_id = s.id
                  LEFT JOIN advisors adv ON a.advisor_id = adv.id
                  WHERE a.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Get appointments by student ID
     */
    public function getByStudentId($studentId) {
        $query = "SELECT a.*, 
                         adv.first_name as advisor_first_name, 
                         adv.last_name as advisor_last_name
                  FROM {$this->table} a
                  LEFT JOIN advisors adv ON a.advisor_id = adv.id
                  WHERE a.student_id = :student_id
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get appointments by advisor ID
     */
    public function getByAdvisorId($advisorId) {
        $query = "SELECT a.*, 
                         s.first_name as student_first_name, 
                         s.last_name as student_last_name
                  FROM {$this->table} a
                  LEFT JOIN students s ON a.student_id = s.id
                  WHERE a.advisor_id = :advisor_id
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':advisor_id', $advisorId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Create a new appointment
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (student_id, advisor_id, appointment_date, appointment_time, duration, purpose, status, notes) 
                  VALUES (:student_id, :advisor_id, :appointment_date, :appointment_time, :duration, :purpose, :status, :notes)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $data['student_id'], PDO::PARAM_INT);
        $stmt->bindParam(':advisor_id', $data['advisor_id'], PDO::PARAM_INT);
        $stmt->bindParam(':appointment_date', $data['appointment_date']);
        $stmt->bindParam(':appointment_time', $data['appointment_time']);
        $stmt->bindParam(':duration', $data['duration'], PDO::PARAM_INT);
        $stmt->bindParam(':purpose', $data['purpose']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':notes', $data['notes']);
        
        return $stmt->execute();
    }
    
    /**
     * Update appointment
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET appointment_date = :appointment_date, 
                      appointment_time = :appointment_time, 
                      duration = :duration, 
                      purpose = :purpose, 
                      status = :status, 
                      notes = :notes,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':appointment_date', $data['appointment_date']);
        $stmt->bindParam(':appointment_time', $data['appointment_time']);
        $stmt->bindParam(':duration', $data['duration'], PDO::PARAM_INT);
        $stmt->bindParam(':purpose', $data['purpose']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':notes', $data['notes']);
        
        return $stmt->execute();
    }
    
    /**
     * Update appointment status
     */
    public function updateStatus($id, $status) {
        $query = "UPDATE {$this->table} 
                  SET status = :status, 
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }
    
    /**
     * Delete appointment
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
