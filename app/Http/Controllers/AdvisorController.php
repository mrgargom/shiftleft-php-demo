<?php
/**
 * Advisor Controller
 * Handles advisor dashboard, availability management, and appointment responses
 */

require_once __DIR__ . '/../../Models/Advisor.php';
require_once __DIR__ . '/../../Models/Appointment.php';
require_once __DIR__ . '/../../Models/Availability.php';
require_once __DIR__ . '/../../Models/Student.php';
require_once __DIR__ . '/../../Services/NotificationService.php';
require_once __DIR__ . '/../Middleware/Auth.php';

class AdvisorController {
    private $advisorModel;
    private $appointmentModel;
    private $availabilityModel;
    private $studentModel;
    private $notificationService;
    private $auth;
    
    public function __construct() {
        $this->advisorModel = new Advisor();
        $this->appointmentModel = new Appointment();
        $this->availabilityModel = new Availability();
        $this->studentModel = new Student();
        $this->notificationService = new NotificationService();
        $this->auth = auth();
    }
    
    /**
     * Show advisor dashboard
     */
    public function dashboard() {
        $this->auth->requireRole('advisor');
        
        $userId = $this->auth->userId();
        $advisor = $this->advisorModel->getByUserId($userId);
        
        if (!$advisor) {
            die('Advisor profile not found');
        }
        
        // Get pending appointments
        $pendingAppointments = $this->appointmentModel->getByAdvisorId($advisor['id'], 'pending');
        
        // Get upcoming confirmed appointments
        $confirmedAppointments = $this->appointmentModel->getByAdvisorId($advisor['id'], 'confirmed');
        
        // Get statistics
        $stats = $this->appointmentModel->getStatistics($advisor['id'], null);
        
        // Get notifications
        $notifications = $this->notificationService->getUserNotifications($userId, true);
        
        $pageTitle = 'Advisor Dashboard';
        require_once __DIR__ . '/../../../resources/views/advisor/dashboard.php';
    }
    
    /**
     * Show all appointments
     */
    public function appointments() {
        $this->auth->requireRole('advisor');
        
        $userId = $this->auth->userId();
        $advisor = $this->advisorModel->getByUserId($userId);
        
        if (!$advisor) {
            die('Advisor profile not found');
        }
        
        $status = $_GET['status'] ?? null;
        $appointments = $this->appointmentModel->getByAdvisorId($advisor['id'], $status);
        
        $pageTitle = 'My Appointments';
        require_once __DIR__ . '/../../../resources/views/advisor/appointments.php';
    }
    
    /**
     * Show availability management
     */
    public function availability() {
        $this->auth->requireRole('advisor');
        
        $userId = $this->auth->userId();
        $advisor = $this->advisorModel->getByUserId($userId);
        
        if (!$advisor) {
            die('Advisor profile not found');
        }
        
        $availabilities = $this->availabilityModel->getAll($advisor['id']);
        
        $pageTitle = 'Manage Availability';
        require_once __DIR__ . '/../../../resources/views/advisor/availability.php';
    }
    
    /**
     * Show create availability form
     */
    public function createAvailability() {
        $this->auth->requireRole('advisor');
        
        $pageTitle = 'Set Availability';
        require_once __DIR__ . '/../../../resources/views/advisor/create_availability.php';
    }
    
    /**
     * Store new availability
     */
    public function storeAvailability() {
        $this->auth->requireRole('advisor');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /advisor/availability');
            return;
        }
        
        $userId = $this->auth->userId();
        $advisor = $this->advisorModel->getByUserId($userId);
        
        if (!$advisor) {
            $_SESSION['error'] = 'Advisor profile not found';
            header('Location: /advisor/dashboard');
            return;
        }
        
        $data = [
            'advisor_id' => $advisor['id'],
            'date' => $_POST['date'] ?? '',
            'start_time' => $_POST['start_time'] ?? '',
            'end_time' => $_POST['end_time'] ?? '',
            'is_available' => 1
        ];
        
        // Validate
        if (empty($data['date']) || empty($data['start_time']) || empty($data['end_time'])) {
            $_SESSION['error'] = 'Please fill all required fields';
            header('Location: /advisor/availability/create');
            return;
        }
        
        // Validate time range
        if (strtotime($data['start_time']) >= strtotime($data['end_time'])) {
            $_SESSION['error'] = 'End time must be after start time';
            header('Location: /advisor/availability/create');
            return;
        }
        
        if ($this->availabilityModel->create($data)) {
            $_SESSION['success'] = 'Availability set successfully';
        } else {
            $_SESSION['error'] = 'Failed to set availability. Time slot may overlap with existing availability.';
        }
        
        header('Location: /advisor/availability');
    }
    
    /**
     * Delete availability
     */
    public function deleteAvailability() {
        $this->auth->requireRole('advisor');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /advisor/availability');
            return;
        }
        
        $availabilityId = $_POST['availability_id'] ?? '';
        
        if (empty($availabilityId)) {
            $_SESSION['error'] = 'Invalid availability';
            header('Location: /advisor/availability');
            return;
        }
        
        // Verify ownership
        $availability = $this->availabilityModel->getById($availabilityId);
        $userId = $this->auth->userId();
        $advisor = $this->advisorModel->getByUserId($userId);
        
        if (!$availability || $availability['advisor_id'] != $advisor['id']) {
            $_SESSION['error'] = 'Availability not found or access denied';
            header('Location: /advisor/availability');
            return;
        }
        
        if ($this->availabilityModel->delete($availabilityId)) {
            $_SESSION['success'] = 'Availability deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete availability';
        }
        
        header('Location: /advisor/availability');
    }
    
    /**
     * Confirm appointment
     */
    public function confirmAppointment() {
        $this->auth->requireRole('advisor');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /advisor/appointments');
            return;
        }
        
        $appointmentId = $_POST['appointment_id'] ?? '';
        
        if (empty($appointmentId)) {
            $_SESSION['error'] = 'Invalid appointment';
            header('Location: /advisor/appointments');
            return;
        }
        
        // Get appointment to verify ownership
        $appointment = $this->appointmentModel->getById($appointmentId);
        $userId = $this->auth->userId();
        $advisor = $this->advisorModel->getByUserId($userId);
        
        if (!$appointment || $appointment['advisor_id'] != $advisor['id']) {
            $_SESSION['error'] = 'Appointment not found or access denied';
            header('Location: /advisor/appointments');
            return;
        }
        
        // Confirm appointment
        if ($this->appointmentModel->confirm($appointmentId)) {
            // Get student from appointment
            $student = $this->studentModel->getById($appointment['student_id']);
            
            // Send notifications
            $this->notificationService->appointmentConfirmed(
                $appointmentId,
                $student['user_id'],
                $userId
            );
            
            $_SESSION['success'] = 'Appointment confirmed successfully';
        } else {
            $_SESSION['error'] = 'Failed to confirm appointment';
        }
        
        header('Location: /advisor/appointments');
    }
    
    /**
     * Decline appointment
     */
    public function declineAppointment() {
        $this->auth->requireRole('advisor');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /advisor/appointments');
            return;
        }
        
        $appointmentId = $_POST['appointment_id'] ?? '';
        $reason = $_POST['reason'] ?? null;
        
        if (empty($appointmentId)) {
            $_SESSION['error'] = 'Invalid appointment';
            header('Location: /advisor/appointments');
            return;
        }
        
        // Get appointment to verify ownership
        $appointment = $this->appointmentModel->getById($appointmentId);
        $userId = $this->auth->userId();
        $advisor = $this->advisorModel->getByUserId($userId);
        
        if (!$appointment || $appointment['advisor_id'] != $advisor['id']) {
            $_SESSION['error'] = 'Appointment not found or access denied';
            header('Location: /advisor/appointments');
            return;
        }
        
        // Decline appointment
        if ($this->appointmentModel->decline($appointmentId)) {
            // Get student from appointment
            $student = $this->studentModel->getById($appointment['student_id']);
            
            // Send notifications
            $this->notificationService->appointmentDeclined(
                $appointmentId,
                $student['user_id'],
                $userId,
                $reason
            );
            
            $_SESSION['success'] = 'Appointment declined';
        } else {
            $_SESSION['error'] = 'Failed to decline appointment';
        }
        
        header('Location: /advisor/appointments');
    }
}
