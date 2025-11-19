<?php
/**
 * Home Controller
 * Handles home page and public pages
 */

require_once __DIR__ . '/../Middleware/Auth.php';

class HomeController {
    private $auth;
    
    public function __construct() {
        $this->auth = auth();
    }
    
    /**
     * Show home page
     */
    public function index() {
        // Redirect to appropriate dashboard if logged in
        if ($this->auth->check()) {
            $this->redirectToDashboard();
            return;
        }
        
        // Show public home page
        $pageTitle = 'Academic Advisor System';
        require_once __DIR__ . '/../../../resources/views/home.php';
    }
    
    /**
     * Redirect to appropriate dashboard based on role
     */
    private function redirectToDashboard() {
        $role = $this->auth->role();
        
        switch ($role) {
            case 'student':
                header('Location: /student/dashboard');
                break;
            case 'advisor':
                header('Location: /advisor/dashboard');
                break;
            case 'administrator':
                header('Location: /admin/dashboard');
                break;
            default:
                header('Location: /login');
        }
        
        exit;
    }
}
