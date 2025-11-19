<?php
/**
 * Notification Model
 * Manages in-system notifications
 */

require_once __DIR__ . '/../../config/database.php';

class Notification {
    private $db;
    private $table = 'notifications';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all notifications for a user
     */
    public function getByUserId($userId, $unreadOnly = false) {
        $query = "SELECT n.*, a.id as appointment_id, a.date, a.time
                  FROM {$this->table} n
                  LEFT JOIN appointments a ON n.appointment_id = a.id
                  WHERE n.user_id = :user_id";
        
        if ($unreadOnly) {
            $query .= " AND n.is_read = 0";
        }
        
        $query .= " ORDER BY n.timestamp DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get notification by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Create notification
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (user_id, appointment_id, type, message) 
                  VALUES (:user_id, :appointment_id, :type, :message)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $appointmentId = $data['appointment_id'] ?? null;
        $stmt->bindParam(':appointment_id', $appointmentId, PDO::PARAM_INT);
        $stmt->bindParam(':type', $data['type']);
        $stmt->bindParam(':message', $data['message']);
        
        return $stmt->execute();
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($id) {
        $query = "UPDATE {$this->table} 
                  SET is_read = 1, 
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId) {
        $query = "UPDATE {$this->table} 
                  SET is_read = 1, 
                      updated_at = CURRENT_TIMESTAMP
                  WHERE user_id = :user_id AND is_read = 0";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Delete notification
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Get unread count for user
     */
    public function getUnreadCount($userId) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} 
                  WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }
}
