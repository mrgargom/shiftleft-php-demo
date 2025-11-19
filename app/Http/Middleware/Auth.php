<?php
/**
 * Authentication Middleware
 * Handles session-based authentication and authorization
 */

class Auth {
    /**
     * Check if user is authenticated
     */
    public function check() {
        return isset($_SESSION['user_id']) && isset($_SESSION['role']);
    }
    
    /**
     * Get current user ID
     */
    public function userId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get current user role
     */
    public function role() {
        return $_SESSION['role'] ?? null;
    }
    
    /**
     * Get current user data
     */
    public function user() {
        if (!$this->check()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'role' => $_SESSION['role']
        ];
    }
    
    /**
     * Check if user has specific role
     */
    public function checkRole($role) {
        if (!$this->check()) {
            return false;
        }
        
        return $_SESSION['role'] === $role;
    }
    
    /**
     * Check if user has any of the specified roles
     */
    public function hasAnyRole($roles) {
        if (!$this->check()) {
            return false;
        }
        
        return in_array($_SESSION['role'], $roles);
    }
    
    /**
     * Login user
     */
    public function login($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['remember_token'] = $user['token'] ?? null;
        
        return true;
    }
    
    /**
     * Logout user
     */
    public function logout() {
        session_destroy();
        return true;
    }
    
    /**
     * Require authentication
     */
    public function requireAuth() {
        if (!$this->check()) {
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Require specific role
     */
    public function requireRole($role) {
        $this->requireAuth();
        
        if (!$this->checkRole($role)) {
            http_response_code(403);
            die('Forbidden: Insufficient permissions');
        }
    }
}

/**
 * Global auth helper
 */
function auth() {
    static $auth = null;
    if ($auth === null) {
        $auth = new Auth();
    }
    return $auth;
}
