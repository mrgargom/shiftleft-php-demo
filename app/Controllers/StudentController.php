<?php
/**
 * Student Controller
 */

require_once __DIR__ . '/../Models/Student.php';

class StudentController {
    private $studentModel;
    
    public function __construct() {
        $this->studentModel = new Student();
    }
    
    /**
     * List all students
     */
    public function index() {
        $students = $this->studentModel->getAll();
        require_once __DIR__ . '/../Views/students/index.php';
    }
    
    /**
     * Show create form
     */
    public function create() {
        require_once __DIR__ . '/../Views/students/create.php';
    }
    
    /**
     * Store new student
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'student_id' => $_POST['student_id'] ?? '',
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'major' => $_POST['major'] ?? '',
                'year_level' => $_POST['year_level'] ?? '',
                'gpa' => $_POST['gpa'] ?? 0.00
            ];
            
            if ($this->studentModel->create($data)) {
                header('Location: ?page=students&success=Student created successfully');
                exit;
            } else {
                header('Location: ?page=students&error=Failed to create student');
                exit;
            }
        }
    }
    
    /**
     * Show student details
     */
    public function show($id) {
        $student = $this->studentModel->getById($id);
        if (!$student) {
            header('Location: ?page=students&error=Student not found');
            exit;
        }
        require_once __DIR__ . '/../Views/students/show.php';
    }
    
    /**
     * Show edit form
     */
    public function edit($id) {
        $student = $this->studentModel->getById($id);
        if (!$student) {
            header('Location: ?page=students&error=Student not found');
            exit;
        }
        require_once __DIR__ . '/../Views/students/edit.php';
    }
    
    /**
     * Update student
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'major' => $_POST['major'] ?? '',
                'year_level' => $_POST['year_level'] ?? '',
                'gpa' => $_POST['gpa'] ?? 0.00
            ];
            
            if ($this->studentModel->update($id, $data)) {
                header('Location: ?page=students&success=Student updated successfully');
                exit;
            } else {
                header('Location: ?page=students&error=Failed to update student');
                exit;
            }
        }
    }
    
    /**
     * Delete student
     */
    public function destroy($id) {
        if ($this->studentModel->delete($id)) {
            header('Location: ?page=students&success=Student deleted successfully');
            exit;
        } else {
            header('Location: ?page=students&error=Failed to delete student');
            exit;
        }
    }
}
