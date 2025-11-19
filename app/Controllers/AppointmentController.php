<?php
/**
 * Appointment Controller
 */

require_once __DIR__ . '/../Models/Appointment.php';
require_once __DIR__ . '/../Models/Student.php';
require_once __DIR__ . '/../Models/Advisor.php';

class AppointmentController {
    private $appointmentModel;
    private $studentModel;
    private $advisorModel;
    
    public function __construct() {
        $this->appointmentModel = new Appointment();
        $this->studentModel = new Student();
        $this->advisorModel = new Advisor();
    }
    
    /**
     * List all appointments
     */
    public function index() {
        $appointments = $this->appointmentModel->getAll();
        require_once __DIR__ . '/../Views/appointments/index.php';
    }
    
    /**
     * Show create form
     */
    public function create() {
        $students = $this->studentModel->getAll();
        $advisors = $this->advisorModel->getAll();
        require_once __DIR__ . '/../Views/appointments/create.php';
    }
    
    /**
     * Store new appointment
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'student_id' => $_POST['student_id'] ?? '',
                'advisor_id' => $_POST['advisor_id'] ?? '',
                'appointment_date' => $_POST['appointment_date'] ?? '',
                'appointment_time' => $_POST['appointment_time'] ?? '',
                'duration' => $_POST['duration'] ?? 30,
                'purpose' => $_POST['purpose'] ?? '',
                'status' => $_POST['status'] ?? 'scheduled',
                'notes' => $_POST['notes'] ?? ''
            ];
            
            if ($this->appointmentModel->create($data)) {
                header('Location: ?page=appointments&success=Appointment created successfully');
                exit;
            } else {
                header('Location: ?page=appointments&error=Failed to create appointment');
                exit;
            }
        }
    }
    
    /**
     * Show appointment details
     */
    public function show($id) {
        $appointment = $this->appointmentModel->getById($id);
        if (!$appointment) {
            header('Location: ?page=appointments&error=Appointment not found');
            exit;
        }
        require_once __DIR__ . '/../Views/appointments/show.php';
    }
    
    /**
     * Show edit form
     */
    public function edit($id) {
        $appointment = $this->appointmentModel->getById($id);
        if (!$appointment) {
            header('Location: ?page=appointments&error=Appointment not found');
            exit;
        }
        $students = $this->studentModel->getAll();
        $advisors = $this->advisorModel->getAll();
        require_once __DIR__ . '/../Views/appointments/edit.php';
    }
    
    /**
     * Update appointment
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'appointment_date' => $_POST['appointment_date'] ?? '',
                'appointment_time' => $_POST['appointment_time'] ?? '',
                'duration' => $_POST['duration'] ?? 30,
                'purpose' => $_POST['purpose'] ?? '',
                'status' => $_POST['status'] ?? 'scheduled',
                'notes' => $_POST['notes'] ?? ''
            ];
            
            if ($this->appointmentModel->update($id, $data)) {
                header('Location: ?page=appointments&success=Appointment updated successfully');
                exit;
            } else {
                header('Location: ?page=appointments&error=Failed to update appointment');
                exit;
            }
        }
    }
    
    /**
     * Delete appointment
     */
    public function destroy($id) {
        if ($this->appointmentModel->delete($id)) {
            header('Location: ?page=appointments&success=Appointment deleted successfully');
            exit;
        } else {
            header('Location: ?page=appointments&error=Failed to delete appointment');
            exit;
        }
    }
}
