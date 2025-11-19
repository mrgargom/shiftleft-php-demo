<?php
/**
 * Appointment Model
 * Manages appointment bookings and lifecycle
 */

require_once __DIR__ . '/../../config/database.php';

class Appointment {
    private $db;
    private $table = 'appointments';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all appointments with details
     */
    public function getAll($status = null) {
        $query = "SELECT a.*, 
                         s.student_id, su.name as student_name, su.email as student_email,
                         ad.advisor_id, au.name as advisor_name, au.email as advisor_email
                  FROM {$this->table} a
                  INNER JOIN students s ON a.student_id = s.id
                  INNER JOIN users su ON s.user_id = su.id
                  INNER JOIN advisors ad ON a.advisor_id = ad.id
                  INNER JOIN users au ON ad.user_id = au.id";
        
        if ($status) {
            $query .= " WHERE a.status = :status";
        }
        
        $query .= " ORDER BY a.date DESC, a.time DESC";
        
        $stmt = $this->db->prepare($query);
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get appointment by ID
     */
    public function getById($id) {
        $query = "SELECT a.*, 
                         s.student_id, su.name as student_name, su.email as student_email,
                         ad.advisor_id, au.name as advisor_name, au.email as advisor_email,
                         ad.department, ad.office_location
                  FROM {$this->table} a
                  INNER JOIN students s ON a.student_id = s.id
                  INNER JOIN users su ON s.user_id = su.id
                  INNER JOIN advisors ad ON a.advisor_id = ad.id
                  INNER JOIN users au ON ad.user_id = au.id
                  WHERE a.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Get appointments by student ID
     */
    public function getByStudentId($studentId, $status = null) {
        $query = "SELECT a.*, 
                         ad.advisor_id, au.name as advisor_name,
                         ad.department, ad.office_location
                  FROM {$this->table} a
                  INNER JOIN advisors ad ON a.advisor_id = ad.id
                  INNER JOIN users au ON ad.user_id = au.id
                  WHERE a.student_id = :student_id";
        
        if ($status) {
            $query .= " AND a.status = :status";
        }
        
        $query .= " ORDER BY a.date DESC, a.time DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get appointments by advisor ID
     */
    public function getByAdvisorId($advisorId, $status = null) {
        $query = "SELECT a.*, 
                         s.student_id, su.name as student_name, su.email as student_email,
                         s.major, s.year_level
                  FROM {$this->table} a
                  INNER JOIN students s ON a.student_id = s.id
                  INNER JOIN users su ON s.user_id = su.id
                  WHERE a.advisor_id = :advisor_id";
        
        if ($status) {
            $query .= " AND a.status = :status";
        }
        
        $query .= " ORDER BY a.date DESC, a.time DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':advisor_id', $advisorId, PDO::PARAM_INT);
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Book appointment (Student creates appointment)
     */
    public function create($data) {
        // Check if time slot is available
        if (!$this->isTimeSlotAvailable($data['advisor_id'], $data['date'], $data['time'], $data['duration'])) {
            return ['success' => false, 'message' => 'Time slot not available'];
        }
        
        try {
            $this->db->beginTransaction();
            
            $query = "INSERT INTO {$this->table} 
                      (student_id, advisor_id, date, time, duration, purpose, status, notes) 
                      VALUES (:student_id, :advisor_id, :date, :time, :duration, :purpose, :status, :notes)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':student_id', $data['student_id'], PDO::PARAM_INT);
            $stmt->bindParam(':advisor_id', $data['advisor_id'], PDO::PARAM_INT);
            $stmt->bindParam(':date', $data['date']);
            $stmt->bindParam(':time', $data['time']);
            $duration = $data['duration'] ?? 30;
            $stmt->bindParam(':duration', $duration, PDO::PARAM_INT);
            $stmt->bindParam(':purpose', $data['purpose']);
            $status = $data['status'] ?? 'pending';
            $stmt->bindParam(':status', $status);
            $notes = $data['notes'] ?? null;
            $stmt->bindParam(':notes', $notes);
            
            if ($stmt->execute()) {
                $appointmentId = $this->db->lastInsertId();
                $this->db->commit();
                
                return ['success' => true, 'appointment_id' => $appointmentId];
            }
            
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Failed to create appointment'];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Update appointment
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET date = :date, 
                      time = :time, 
                      duration = :duration, 
                      purpose = :purpose, 
                      status = :status, 
                      notes = :notes,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':date', $data['date']);
        $stmt->bindParam(':time', $data['time']);
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
     * Cancel appointment
     */
    public function cancel($id) {
        return $this->updateStatus($id, 'cancelled');
    }
    
    /**
     * Confirm appointment (Advisor accepts)
     */
    public function confirm($id) {
        return $this->updateStatus($id, 'confirmed');
    }
    
    /**
     * Decline appointment (Advisor declines)
     */
    public function decline($id) {
        return $this->updateStatus($id, 'declined');
    }
    
    /**
     * Complete appointment
     */
    public function complete($id) {
        return $this->updateStatus($id, 'completed');
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
    
    /**
     * Check if time slot is available
     */
    private function isTimeSlotAvailable($advisorId, $date, $time, $duration) {
        // First check if advisor has availability
        require_once __DIR__ . '/Availability.php';
        $availabilityModel = new Availability();
        
        if (!$availabilityModel->isAvailable($advisorId, $date, $time, $duration)) {
            return false;
        }
        
        // Check for conflicting appointments
        $endTime = date('H:i:s', strtotime($time) + ($duration * 60));
        
        $query = "SELECT COUNT(*) as count FROM {$this->table} 
                  WHERE advisor_id = :advisor_id 
                  AND date = :date 
                  AND status NOT IN ('cancelled', 'declined')
                  AND (
                      (time < :end_time AND 
                       DATETIME(date || ' ' || time, '+' || duration || ' minutes') > :time)
                  )";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':advisor_id', $advisorId, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':end_time', $endTime);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'] == 0;
    }
    
    /**
     * Get appointment statistics
     */
    public function getStatistics($advisorId = null, $studentId = null) {
        $where = [];
        $params = [];
        
        if ($advisorId) {
            $where[] = "advisor_id = :advisor_id";
            $params[':advisor_id'] = $advisorId;
        }
        
        if ($studentId) {
            $where[] = "student_id = :student_id";
            $params[':student_id'] = $studentId;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $query = "SELECT 
                      COUNT(*) as total,
                      SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                      SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                      SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                      SUM(CASE WHEN status = 'declined' THEN 1 ELSE 0 END) as declined,
                      SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                  FROM {$this->table}
                  {$whereClause}";
        
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->execute();
        
        return $stmt->fetch();
    }
}
