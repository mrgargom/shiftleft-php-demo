<?php
/**
 * Availability Model
 * Manages advisor availability slots
 */

require_once __DIR__ . '/../../config/database.php';

class Availability {
    private $db;
    private $table = 'availabilities';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all availabilities
     */
    public function getAll($advisorId = null) {
        if ($advisorId) {
            $query = "SELECT av.*, a.advisor_id, u.name as advisor_name
                      FROM {$this->table} av
                      INNER JOIN advisors a ON av.advisor_id = a.id
                      INNER JOIN users u ON a.user_id = u.id
                      WHERE av.advisor_id = :advisor_id
                      ORDER BY av.date, av.start_time";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':advisor_id', $advisorId, PDO::PARAM_INT);
        } else {
            $query = "SELECT av.*, a.advisor_id, u.name as advisor_name
                      FROM {$this->table} av
                      INNER JOIN advisors a ON av.advisor_id = a.id
                      INNER JOIN users u ON a.user_id = u.id
                      ORDER BY av.date, av.start_time";
            $stmt = $this->db->prepare($query);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get availability by ID
     */
    public function getById($id) {
        $query = "SELECT av.*, a.advisor_id, u.name as advisor_name
                  FROM {$this->table} av
                  INNER JOIN advisors a ON av.advisor_id = a.id
                  INNER JOIN users u ON a.user_id = u.id
                  WHERE av.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Get available slots for an advisor on a specific date
     */
    public function getAvailableSlots($advisorId, $date) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE advisor_id = :advisor_id 
                  AND date = :date 
                  AND is_available = 1
                  ORDER BY start_time";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':advisor_id', $advisorId, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Set availability for advisor
     */
    public function create($data) {
        // Check for overlapping availability
        if ($this->hasOverlap($data['advisor_id'], $data['date'], $data['start_time'], $data['end_time'])) {
            return false;
        }
        
        $query = "INSERT INTO {$this->table} 
                  (advisor_id, date, start_time, end_time, is_available) 
                  VALUES (:advisor_id, :date, :start_time, :end_time, :is_available)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':advisor_id', $data['advisor_id'], PDO::PARAM_INT);
        $stmt->bindParam(':date', $data['date']);
        $stmt->bindParam(':start_time', $data['start_time']);
        $stmt->bindParam(':end_time', $data['end_time']);
        $isAvailable = $data['is_available'] ?? 1;
        $stmt->bindParam(':is_available', $isAvailable, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Update availability
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET date = :date, 
                      start_time = :start_time, 
                      end_time = :end_time,
                      is_available = :is_available,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':date', $data['date']);
        $stmt->bindParam(':start_time', $data['start_time']);
        $stmt->bindParam(':end_time', $data['end_time']);
        $stmt->bindParam(':is_available', $data['is_available'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Delete availability
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Check if time slot has overlap
     */
    private function hasOverlap($advisorId, $date, $startTime, $endTime, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} 
                  WHERE advisor_id = :advisor_id 
                  AND date = :date 
                  AND (
                      (start_time < :end_time AND end_time > :start_time)
                  )";
        
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':advisor_id', $advisorId, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':start_time', $startTime);
        $stmt->bindParam(':end_time', $endTime);
        
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
    
    /**
     * Check if advisor is available at specific time
     */
    public function isAvailable($advisorId, $date, $time, $duration = 30) {
        // Calculate end time
        $endTime = date('H:i:s', strtotime($time) + ($duration * 60));
        
        $query = "SELECT COUNT(*) as count FROM {$this->table} 
                  WHERE advisor_id = :advisor_id 
                  AND date = :date 
                  AND start_time <= :time
                  AND end_time >= :end_time
                  AND is_available = 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':advisor_id', $advisorId, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':end_time', $endTime);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}
