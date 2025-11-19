<?php
/**
 * User Model (Base Model)
 * Handles authentication and user management
 */

require_once __DIR__ . '/../../config/database.php';

class User {
    private $db;
    private $table = 'users';
    
    public $id;
    public $name;
    public $email;
    public $role;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Login user
     */
    public function login($email, $password) {
        $query = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->name = $user['name'];
            $this->email = $user['email'];
            $this->role = $user['role'];
            
            // Update remember token
            $token = bin2hex(random_bytes(32));
            $this->updateRememberToken($user['id'], $token);
            
            return [
                'success' => true,
                'user' => $user,
                'token' => $token
            ];
        }
        
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    /**
     * Logout user
     */
    public function logout($userId) {
        $this->updateRememberToken($userId, null);
        return true;
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($userId, $data) {
        $query = "UPDATE {$this->table} 
                  SET name = :name, 
                      email = :email,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        
        return $stmt->execute();
    }
    
    /**
     * Update password
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $query = "UPDATE {$this->table} 
                  SET password = :password,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':password', $hashedPassword);
        
        return $stmt->execute();
    }
    
    /**
     * Get user by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Get user by email
     */
    public function getByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Get all users
     */
    public function getAll($role = null) {
        if ($role) {
            $query = "SELECT * FROM {$this->table} WHERE role = :role ORDER BY name";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':role', $role);
        } else {
            $query = "SELECT * FROM {$this->table} ORDER BY name";
            $stmt = $this->db->prepare($query);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Create new user
     */
    public function create($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $query = "INSERT INTO {$this->table} 
                  (name, email, password, role) 
                  VALUES (:name, :email, :password, :role)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $data['role']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update user
     */
    public function update($id, $data) {
        $fields = [];
        $params = ['id' => $id];
        
        if (isset($data['name'])) {
            $fields[] = "name = :name";
            $params['name'] = $data['name'];
        }
        
        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params['email'] = $data['email'];
        }
        
        if (isset($data['role'])) {
            $fields[] = "role = :role";
            $params['role'] = $data['role'];
        }
        
        if (isset($data['password'])) {
            $fields[] = "password = :password";
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $fields[] = "updated_at = CURRENT_TIMESTAMP";
        
        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        return $stmt->execute();
    }
    
    /**
     * Delete user
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Update remember token
     */
    private function updateRememberToken($userId, $token) {
        $query = "UPDATE {$this->table} SET remember_token = :token WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        return $stmt->execute();
    }
    
    /**
     * Get user by token
     */
    public function getByToken($token) {
        $query = "SELECT * FROM {$this->table} WHERE remember_token = :token";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Check if user has role
     */
    public function hasRole($userId, $role) {
        $user = $this->getById($userId);
        return $user && $user['role'] === $role;
    }
}
