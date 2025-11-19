<?php
/**
 * Advisor Controller
 */

require_once __DIR__ . '/../Models/Advisor.php';

class AdvisorController {
    private $advisorModel;
    
    public function __construct() {
        $this->advisorModel = new Advisor();
    }
    
    /**
     * List all advisors
     */
    public function index() {
        $advisors = $this->advisorModel->getAll();
        require_once __DIR__ . '/../Views/advisors/index.php';
    }
    
    /**
     * Show create form
     */
    public function create() {
        require_once __DIR__ . '/../Views/advisors/create.php';
    }
    
    /**
     * Store new advisor
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'advisor_id' => $_POST['advisor_id'] ?? '',
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'department' => $_POST['department'] ?? '',
                'office_location' => $_POST['office_location'] ?? ''
            ];
            
            if ($this->advisorModel->create($data)) {
                header('Location: ?page=advisors&success=Advisor created successfully');
                exit;
            } else {
                header('Location: ?page=advisors&error=Failed to create advisor');
                exit;
            }
        }
    }
    
    /**
     * Show advisor details
     */
    public function show($id) {
        $advisor = $this->advisorModel->getById($id);
        if (!$advisor) {
            header('Location: ?page=advisors&error=Advisor not found');
            exit;
        }
        require_once __DIR__ . '/../Views/advisors/show.php';
    }
    
    /**
     * Show edit form
     */
    public function edit($id) {
        $advisor = $this->advisorModel->getById($id);
        if (!$advisor) {
            header('Location: ?page=advisors&error=Advisor not found');
            exit;
        }
        require_once __DIR__ . '/../Views/advisors/edit.php';
    }
    
    /**
     * Update advisor
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'department' => $_POST['department'] ?? '',
                'office_location' => $_POST['office_location'] ?? ''
            ];
            
            if ($this->advisorModel->update($id, $data)) {
                header('Location: ?page=advisors&success=Advisor updated successfully');
                exit;
            } else {
                header('Location: ?page=advisors&error=Failed to update advisor');
                exit;
            }
        }
    }
    
    /**
     * Delete advisor
     */
    public function destroy($id) {
        if ($this->advisorModel->delete($id)) {
            header('Location: ?page=advisors&success=Advisor deleted successfully');
            exit;
        } else {
            header('Location: ?page=advisors&error=Failed to delete advisor');
            exit;
        }
    }
}
