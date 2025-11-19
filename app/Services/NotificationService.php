<?php
/**
 * Notification Service
 * Handles creation and management of notifications
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Notification.php';
require_once __DIR__ . '/../Models/User.php';

class NotificationService {
    private $notificationModel;
    
    public function __construct() {
        $this->notificationModel = new Notification();
    }
    
    /**
     * Create notification for appointment creation
     */
    public function appointmentCreated($appointmentId, $studentUserId, $advisorUserId) {
        // Notify student
        $this->notificationModel->create([
            'user_id' => $studentUserId,
            'appointment_id' => $appointmentId,
            'type' => 'appointment_created',
            'message' => 'Your appointment request has been submitted and is pending advisor approval.'
        ]);
        
        // Notify advisor
        $this->notificationModel->create([
            'user_id' => $advisorUserId,
            'appointment_id' => $appointmentId,
            'type' => 'appointment_request',
            'message' => 'You have a new appointment request from a student.'
        ]);
        
        // TODO: Queue email jobs
        $this->queueEmail($advisorUserId, 'New Appointment Request', 'You have a new appointment request.');
    }
    
    /**
     * Create notification for appointment confirmation
     */
    public function appointmentConfirmed($appointmentId, $studentUserId, $advisorUserId) {
        // Notify student
        $this->notificationModel->create([
            'user_id' => $studentUserId,
            'appointment_id' => $appointmentId,
            'type' => 'appointment_confirmed',
            'message' => 'Your appointment has been confirmed by the advisor.'
        ]);
        
        // TODO: Queue email job
        $this->queueEmail($studentUserId, 'Appointment Confirmed', 'Your appointment has been confirmed.');
    }
    
    /**
     * Create notification for appointment decline
     */
    public function appointmentDeclined($appointmentId, $studentUserId, $advisorUserId, $reason = null) {
        $message = 'Your appointment request has been declined by the advisor.';
        if ($reason) {
            $message .= ' Reason: ' . $reason;
        }
        
        // Notify student
        $this->notificationModel->create([
            'user_id' => $studentUserId,
            'appointment_id' => $appointmentId,
            'type' => 'appointment_declined',
            'message' => $message
        ]);
        
        // TODO: Queue email job
        $this->queueEmail($studentUserId, 'Appointment Declined', $message);
    }
    
    /**
     * Create notification for appointment cancellation
     */
    public function appointmentCancelled($appointmentId, $studentUserId, $advisorUserId, $cancelledBy) {
        if ($cancelledBy === 'student') {
            // Notify advisor
            $this->notificationModel->create([
                'user_id' => $advisorUserId,
                'appointment_id' => $appointmentId,
                'type' => 'appointment_cancelled',
                'message' => 'A student has cancelled their appointment with you.'
            ]);
            
            // Confirm to student
            $this->notificationModel->create([
                'user_id' => $studentUserId,
                'appointment_id' => $appointmentId,
                'type' => 'appointment_cancelled',
                'message' => 'You have cancelled your appointment.'
            ]);
            
            // TODO: Queue email job
            $this->queueEmail($advisorUserId, 'Appointment Cancelled', 'A student has cancelled their appointment.');
        } else {
            // Notify student
            $this->notificationModel->create([
                'user_id' => $studentUserId,
                'appointment_id' => $appointmentId,
                'type' => 'appointment_cancelled',
                'message' => 'Your appointment has been cancelled by the advisor.'
            ]);
            
            // TODO: Queue email job
            $this->queueEmail($studentUserId, 'Appointment Cancelled', 'Your appointment has been cancelled.');
        }
    }
    
    /**
     * Create notification for appointment reminder
     */
    public function appointmentReminder($appointmentId, $userId, $hoursUntil) {
        $this->notificationModel->create([
            'user_id' => $userId,
            'appointment_id' => $appointmentId,
            'type' => 'appointment_reminder',
            'message' => "Reminder: You have an appointment in {$hoursUntil} hours."
        ]);
        
        // TODO: Queue email job
        $this->queueEmail($userId, 'Appointment Reminder', "You have an appointment in {$hoursUntil} hours.");
    }
    
    /**
     * Queue email job (stub for now)
     */
    private function queueEmail($userId, $subject, $body) {
        // TODO: Implement email queueing
        // For now, we'll just log it
        error_log("Email queued for user {$userId}: {$subject}");
    }
    
    /**
     * Get notifications for user
     */
    public function getUserNotifications($userId, $unreadOnly = false) {
        return $this->notificationModel->getByUserId($userId, $unreadOnly);
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId) {
        return $this->notificationModel->markAsRead($notificationId);
    }
    
    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead($userId) {
        return $this->notificationModel->markAllAsRead($userId);
    }
    
    /**
     * Get unread count
     */
    public function getUnreadCount($userId) {
        return $this->notificationModel->getUnreadCount($userId);
    }
}
