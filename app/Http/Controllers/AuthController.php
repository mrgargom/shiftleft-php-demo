<?php
/**
 * Authentication Controller
 * Handles user login, logout, and authentication
 */

require_once __DIR__ . '/../../Models/User.php';
require_once __DIR__ . '/../../Models/Student.php';
require_once __DIR__ . '/../../Models/Advisor.php';
require_once __DIR__ . '/../../Models/Administrator.php';
require_once __DIR__ . '/../Middleware/Auth.php';

class AuthController {
    private $userModel;
    private $auth;
    
    public function __construct() {
        $this->userModel = new User();
        $this->auth = auth();
    }
    
    /**
     * Show login form
     */
    public function showLogin() {
        // Redirect if already logged in
        if ($this->auth->check()) {
            $this->redirectToDashboard();
            return;
        }
        
        $pageTitle = 'Login - Academic Advisor System';
        require_once __DIR__ . '/../../../resources/views/auth/login.php';
    }
    
    /**
     * Handle login request
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            return;
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate input
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required';
            header('Location: /login');
            return;
        }
        
        // Attempt login
        $result = $this->userModel->login($email, $password);
        
        if ($result['success']) {
            // Set session
            $this->auth->login([
                'id' => $result['user']['id'],
                'name' => $result['user']['name'],
                'email' => $result['user']['email'],
                'role' => $result['user']['role'],
                'token' => $result['token']
            ]);
            
            // Redirect to appropriate dashboard
            $this->redirectToDashboard();
        } else {
            $_SESSION['error'] = $result['message'] ?? 'Invalid credentials';
            header('Location: /login');
        }
    }
    
    /**
     * Handle logout request
     */
    public function logout() {
        if ($this->auth->check()) {
            $this->userModel->logout($this->auth->userId());
            $this->auth->logout();
        }
        
        header('Location: /login');
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
                header('Location: /');
        }
        
        exit;
    }
}
