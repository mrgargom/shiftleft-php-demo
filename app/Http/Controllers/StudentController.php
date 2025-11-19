<?php
/**
 * Student Controller
 * Handles student dashboard, advisor search, and appointment booking
 */

require_once __DIR__ . '/../../Models/Student.php';
require_once __DIR__ . '/../../Models/Advisor.php';
require_once __DIR__ . '/../../Models/Appointment.php';
require_once __DIR__ . '/../../Models/Availability.php';
require_once __DIR__ . '/../../Services/NotificationService.php';
require_once __DIR__ . '/../Middleware/Auth.php';

class StudentController {
    private $studentModel;
    private $advisorModel;
    private $appointmentModel;
    private $availabilityModel;
    private $notificationService;
    private $auth;
    
    public function __construct() {
        $this->studentModel = new Student();
        $this->advisorModel = new Advisor();
        $this->appointmentModel = new Appointment();
        $this->availabilityModel = new Availability();
        $this->notificationService = new NotificationService();
        $this->auth = auth();
    }
    
    /**
     * Show student dashboard
     */
    public function dashboard() {
        $this->auth->requireRole('student');
        
        $userId = $this->auth->userId();
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            die('Student profile not found');
        }
        
        // Get upcoming appointments
        $appointments = $this->appointmentModel->getByStudentId($student['id']);
        
        // Get statistics
        $stats = $this->appointmentModel->getStatistics(null, $student['id']);
        
        // Get notifications
        $notifications = $this->notificationService->getUserNotifications($userId, true);
        
        $pageTitle = 'Student Dashboard';
        require_once __DIR__ . '/../../../resources/views/student/dashboard.php';
    }
    
    /**
     * Show advisors list (search)
     */
    public function advisors() {
        $this->auth->requireRole('student');
        
        $searchTerm = $_GET['search'] ?? '';
        $department = $_GET['department'] ?? null;
        
        $advisors = $this->studentModel->searchAdvisors($searchTerm, $department);
        
        $pageTitle = 'Find Advisors';
        require_once __DIR__ . '/../../../resources/views/student/advisors.php';
    }
    
    /**
     * Show student appointments
     */
    public function appointments() {
        $this->auth->requireRole('student');
        
        $userId = $this->auth->userId();
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            die('Student profile not found');
        }
        
        $status = $_GET['status'] ?? null;
        $appointments = $this->appointmentModel->getByStudentId($student['id'], $status);
        
        $pageTitle = 'My Appointments';
        require_once __DIR__ . '/../../../resources/views/student/appointments.php';
    }
    
    /**
     * Show create appointment form
     */
    public function createAppointment() {
        $this->auth->requireRole('student');
        
        $advisorId = $_GET['advisor_id'] ?? null;
        
        if (!$advisorId) {
            header('Location: /student/advisors');
            return;
        }
        
        $advisor = $this->advisorModel->getById($advisorId);
        if (!$advisor) {
            $_SESSION['error'] = 'Advisor not found';
            header('Location: /student/advisors');
            return;
        }
        
        // Get advisor availabilities
        $availabilities = $this->availabilityModel->getAll($advisorId);
        
        $pageTitle = 'Book Appointment';
        require_once __DIR__ . '/../../../resources/views/student/create_appointment.php';
    }
    
    /**
     * Store new appointment
     */
    public function storeAppointment() {
        $this->auth->requireRole('student');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /student/advisors');
            return;
        }
        
        $userId = $this->auth->userId();
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            $_SESSION['error'] = 'Student profile not found';
            header('Location: /student/dashboard');
            return;
        }
        
        $data = [
            'student_id' => $student['id'],
            'advisor_id' => $_POST['advisor_id'] ?? '',
            'date' => $_POST['date'] ?? '',
            'time' => $_POST['time'] ?? '',
            'duration' => $_POST['duration'] ?? 30,
            'purpose' => $_POST['purpose'] ?? '',
            'notes' => $_POST['notes'] ?? '',
            'status' => 'pending'
        ];
        
        // Validate required fields
        if (empty($data['advisor_id']) || empty($data['date']) || empty($data['time']) || empty($data['purpose'])) {
            $_SESSION['error'] = 'Please fill all required fields';
            header('Location: /student/appointments/create?advisor_id=' . $data['advisor_id']);
            return;
        }
        
        // Create appointment
        $result = $this->appointmentModel->create($data);
        
        if ($result['success']) {
            // Get advisor user ID for notification
            $advisor = $this->advisorModel->getById($data['advisor_id']);
            
            // Send notifications
            $this->notificationService->appointmentCreated(
                $result['appointment_id'],
                $userId,
                $advisor['user_id']
            );
            
            $_SESSION['success'] = 'Appointment request submitted successfully! Waiting for advisor confirmation.';
            header('Location: /student/appointments');
        } else {
            $_SESSION['error'] = $result['message'] ?? 'Failed to create appointment';
            header('Location: /student/appointments/create?advisor_id=' . $data['advisor_id']);
        }
    }
    
    /**
     * Cancel appointment
     */
    public function cancelAppointment() {
        $this->auth->requireRole('student');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /student/appointments');
            return;
        }
        
        $appointmentId = $_POST['appointment_id'] ?? '';
        
        if (empty($appointmentId)) {
            $_SESSION['error'] = 'Invalid appointment';
            header('Location: /student/appointments');
            return;
        }
        
        // Get appointment to verify ownership
        $appointment = $this->appointmentModel->getById($appointmentId);
        $userId = $this->auth->userId();
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$appointment || $appointment['student_id'] != $student['id']) {
            $_SESSION['error'] = 'Appointment not found or access denied';
            header('Location: /student/appointments');
            return;
        }
        
        // Cancel appointment
        if ($this->appointmentModel->cancel($appointmentId)) {
            // Get advisor from appointment
            $advisor = $this->advisorModel->getById($appointment['advisor_id']);
            
            // Send notifications
            $this->notificationService->appointmentCancelled(
                $appointmentId,
                $userId,
                $advisor['user_id'],
                'student'
            );
            
            $_SESSION['success'] = 'Appointment cancelled successfully';
        } else {
            $_SESSION['error'] = 'Failed to cancel appointment';
        }
        
        header('Location: /student/appointments');
    }
}
