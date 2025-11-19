<?php
/**
 * Admin Controller
 * Handles admin dashboard, user management, CSV import, and reports
 */

require_once __DIR__ . '/../../Models/User.php';
require_once __DIR__ . '/../../Models/Student.php';
require_once __DIR__ . '/../../Models/Advisor.php';
require_once __DIR__ . '/../../Models/Administrator.php';
require_once __DIR__ . '/../../Models/Appointment.php';
require_once __DIR__ . '/../Middleware/Auth.php';

class AdminController {
    private $userModel;
    private $studentModel;
    private $advisorModel;
    private $adminModel;
    private $appointmentModel;
    private $auth;
    
    public function __construct() {
        $this->userModel = new User();
        $this->studentModel = new Student();
        $this->advisorModel = new Advisor();
        $this->adminModel = new Administrator();
        $this->appointmentModel = new Appointment();
        $this->auth = auth();
    }
    
    /**
     * Show admin dashboard
     */
    public function dashboard() {
        $this->auth->requireRole('administrator');
        
        // Get statistics
        $totalStudents = count($this->studentModel->getAll());
        $totalAdvisors = count($this->advisorModel->getAll());
        $totalAppointments = count($this->appointmentModel->getAll());
        $pendingAppointments = count($this->appointmentModel->getAll('pending'));
        
        $stats = [
            'students' => $totalStudents,
            'advisors' => $totalAdvisors,
            'appointments' => $totalAppointments,
            'pending' => $pendingAppointments
        ];
        
        // Recent appointments
        $recentAppointments = array_slice($this->appointmentModel->getAll(), 0, 10);
        
        $pageTitle = 'Admin Dashboard';
        require_once __DIR__ . '/../../../resources/views/admin/dashboard.php';
    }
    
    /**
     * Show users management
     */
    public function users() {
        $this->auth->requireRole('administrator');
        
        $role = $_GET['role'] ?? null;
        $users = $this->userModel->getAll($role);
        
        $pageTitle = 'Manage Users';
        require_once __DIR__ . '/../../../resources/views/admin/users.php';
    }
    
    /**
     * Show create user form
     */
    public function createUser() {
        $this->auth->requireRole('administrator');
        
        $pageTitle = 'Create User';
        require_once __DIR__ . '/../../../resources/views/admin/create_user.php';
    }
    
    /**
     * Store new user
     */
    public function storeUser() {
        $this->auth->requireRole('administrator');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/users');
            return;
        }
        
        $role = $_POST['role'] ?? '';
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate
        if (empty($role) || empty($name) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Please fill all required fields';
            header('Location: /admin/users/create');
            return;
        }
        
        // Check if email exists
        if ($this->userModel->getByEmail($email)) {
            $_SESSION['error'] = 'Email already exists';
            header('Location: /admin/users/create');
            return;
        }
        
        try {
            // Create based on role
            switch ($role) {
                case 'student':
                    $studentData = [
                        'name' => $name,
                        'email' => $email,
                        'password' => $password,
                        'student_id' => $_POST['student_id'] ?? 'STU' . time(),
                        'major' => $_POST['major'] ?? '',
                        'year_level' => $_POST['year_level'] ?? '',
                        'gpa' => $_POST['gpa'] ?? 0.00,
                        'phone' => $_POST['phone'] ?? ''
                    ];
                    
                    $result = $this->studentModel->create($studentData);
                    break;
                    
                case 'advisor':
                    $advisorData = [
                        'name' => $name,
                        'email' => $email,
                        'password' => $password,
                        'advisor_id' => $_POST['advisor_id'] ?? 'ADV' . time(),
                        'department' => $_POST['department'] ?? '',
                        'office_location' => $_POST['office_location'] ?? '',
                        'phone_number' => $_POST['phone'] ?? ''
                    ];
                    
                    $result = $this->advisorModel->create($advisorData);
                    break;
                    
                case 'administrator':
                    $adminData = [
                        'name' => $name,
                        'email' => $email,
                        'password' => $password,
                        'admin_id' => $_POST['admin_id'] ?? 'ADM' . time()
                    ];
                    
                    $result = $this->adminModel->create($adminData);
                    break;
                    
                default:
                    $_SESSION['error'] = 'Invalid role';
                    header('Location: /admin/users/create');
                    return;
            }
            
            if ($result) {
                // Log admin action
                $this->adminModel->logAction(
                    $this->auth->userId(),
                    'create_user',
                    'user',
                    $result,
                    "Created {$role}: {$name} ({$email})"
                );
                
                $_SESSION['success'] = ucfirst($role) . ' created successfully';
            } else {
                $_SESSION['error'] = 'Failed to create user';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }
        
        header('Location: /admin/users');
    }
    
    /**
     * Delete user
     */
    public function deleteUser() {
        $this->auth->requireRole('administrator');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/users');
            return;
        }
        
        $userId = $_POST['user_id'] ?? '';
        
        if (empty($userId)) {
            $_SESSION['error'] = 'Invalid user';
            header('Location: /admin/users');
            return;
        }
        
        // Prevent self-deletion
        if ($userId == $this->auth->userId()) {
            $_SESSION['error'] = 'Cannot delete your own account';
            header('Location: /admin/users');
            return;
        }
        
        $user = $this->userModel->getById($userId);
        
        if ($this->userModel->delete($userId)) {
            // Log admin action
            $this->adminModel->logAction(
                $this->auth->userId(),
                'delete_user',
                'user',
                $userId,
                "Deleted user: {$user['name']} ({$user['email']})"
            );
            
            $_SESSION['success'] = 'User deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete user';
        }
        
        header('Location: /admin/users');
    }
    
    /**
     * Import users from CSV
     */
    public function importUsers() {
        $this->auth->requireRole('administrator');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/users');
            return;
        }
        
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Please upload a valid CSV file';
            header('Location: /admin/users');
            return;
        }
        
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, 'r');
        
        if (!$handle) {
            $_SESSION['error'] = 'Failed to read CSV file';
            header('Location: /admin/users');
            return;
        }
        
        $imported = 0;
        $errors = [];
        $row = 0;
        
        // Skip header row
        fgetcsv($handle);
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            $row++;
            
            // Expected format: role, name, email, password, extra_field1, extra_field2, ...
            if (count($data) < 4) {
                $errors[] = "Row {$row}: Insufficient data";
                continue;
            }
            
            $role = trim($data[0]);
            $name = trim($data[1]);
            $email = trim($data[2]);
            $password = trim($data[3]);
            
            // Validate
            if (empty($role) || empty($name) || empty($email) || empty($password)) {
                $errors[] = "Row {$row}: Missing required fields";
                continue;
            }
            
            // Check if email exists
            if ($this->userModel->getByEmail($email)) {
                $errors[] = "Row {$row}: Email {$email} already exists";
                continue;
            }
            
            try {
                switch ($role) {
                    case 'student':
                        $studentData = [
                            'name' => $name,
                            'email' => $email,
                            'password' => $password,
                            'student_id' => $data[4] ?? 'STU' . time() . $row,
                            'major' => $data[5] ?? '',
                            'year_level' => $data[6] ?? '',
                            'gpa' => $data[7] ?? 0.00,
                            'phone' => $data[8] ?? ''
                        ];
                        
                        if ($this->studentModel->create($studentData)) {
                            $imported++;
                        }
                        break;
                        
                    case 'advisor':
                        $advisorData = [
                            'name' => $name,
                            'email' => $email,
                            'password' => $password,
                            'advisor_id' => $data[4] ?? 'ADV' . time() . $row,
                            'department' => $data[5] ?? '',
                            'office_location' => $data[6] ?? '',
                            'phone_number' => $data[7] ?? ''
                        ];
                        
                        if ($this->advisorModel->create($advisorData)) {
                            $imported++;
                        }
                        break;
                        
                    default:
                        $errors[] = "Row {$row}: Invalid role '{$role}'";
                        continue 2;
                }
            } catch (Exception $e) {
                $errors[] = "Row {$row}: " . $e->getMessage();
            }
        }
        
        fclose($handle);
        
        // Log admin action
        $this->adminModel->logAction(
            $this->auth->userId(),
            'import_users',
            'csv',
            null,
            "Imported {$imported} users from CSV"
        );
        
        if ($imported > 0) {
            $_SESSION['success'] = "Successfully imported {$imported} users";
            if (!empty($errors)) {
                $_SESSION['warning'] = "Errors: " . implode(', ', array_slice($errors, 0, 5));
            }
        } else {
            $_SESSION['error'] = "Failed to import users. Errors: " . implode(', ', array_slice($errors, 0, 5));
        }
        
        header('Location: /admin/users');
    }
    
    /**
     * Show all appointments
     */
    public function appointments() {
        $this->auth->requireRole('administrator');
        
        $status = $_GET['status'] ?? null;
        $appointments = $this->appointmentModel->getAll($status);
        
        $pageTitle = 'All Appointments';
        require_once __DIR__ . '/../../../resources/views/admin/appointments.php';
    }
    
    /**
     * Show reports
     */
    public function reports() {
        $this->auth->requireRole('administrator');
        
        // Generate statistics
        $students = $this->studentModel->getAll();
        $advisors = $this->advisorModel->getAll();
        $appointments = $this->appointmentModel->getAll();
        
        // Department-wise statistics
        $departmentStats = [];
        foreach ($advisors as $advisor) {
            $dept = $advisor['department'];
            if (!isset($departmentStats[$dept])) {
                $departmentStats[$dept] = [
                    'advisors' => 0,
                    'appointments' => 0
                ];
            }
            $departmentStats[$dept]['advisors']++;
            
            $deptAppointments = $this->appointmentModel->getByAdvisorId($advisor['id']);
            $departmentStats[$dept]['appointments'] += count($deptAppointments);
        }
        
        // Get audit logs
        $auditLogs = $this->adminModel->getAuditLogs(50, 0);
        
        $pageTitle = 'Reports & Analytics';
        require_once __DIR__ . '/../../../resources/views/admin/reports.php';
    }
}
