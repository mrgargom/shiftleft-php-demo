<?php
/**
 * Application Router
 * Handles all HTTP requests and routes them to appropriate controllers
 */

// Session management
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
require_once __DIR__ . '/../config/database.php';

// Autoload helpers
require_once __DIR__ . '/../app/Http/Middleware/Auth.php';
require_once __DIR__ . '/../app/Services/NotificationService.php';

// Helper function to load controllers
function loadController($controllerName) {
    $file = __DIR__ . "/../app/Http/Controllers/{$controllerName}.php";
    if (file_exists($file)) {
        require_once $file;
        return new $controllerName();
    }
    return null;
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$query = $_GET;

// Route definitions
$routes = [
    'GET' => [
        '/' => ['controller' => 'HomeController', 'action' => 'index'],
        '/login' => ['controller' => 'AuthController', 'action' => 'showLogin'],
        '/logout' => ['controller' => 'AuthController', 'action' => 'logout'],
        
        // Student routes
        '/student/dashboard' => ['controller' => 'StudentController', 'action' => 'dashboard', 'middleware' => 'student'],
        '/student/advisors' => ['controller' => 'StudentController', 'action' => 'advisors', 'middleware' => 'student'],
        '/student/appointments' => ['controller' => 'StudentController', 'action' => 'appointments', 'middleware' => 'student'],
        '/student/appointments/create' => ['controller' => 'StudentController', 'action' => 'createAppointment', 'middleware' => 'student'],
        
        // Advisor routes
        '/advisor/dashboard' => ['controller' => 'AdvisorController', 'action' => 'dashboard', 'middleware' => 'advisor'],
        '/advisor/appointments' => ['controller' => 'AdvisorController', 'action' => 'appointments', 'middleware' => 'advisor'],
        '/advisor/availability' => ['controller' => 'AdvisorController', 'action' => 'availability', 'middleware' => 'advisor'],
        '/advisor/availability/create' => ['controller' => 'AdvisorController', 'action' => 'createAvailability', 'middleware' => 'advisor'],
        
        // Admin routes
        '/admin/dashboard' => ['controller' => 'AdminController', 'action' => 'dashboard', 'middleware' => 'admin'],
        '/admin/users' => ['controller' => 'AdminController', 'action' => 'users', 'middleware' => 'admin'],
        '/admin/users/create' => ['controller' => 'AdminController', 'action' => 'createUser', 'middleware' => 'admin'],
        '/admin/appointments' => ['controller' => 'AdminController', 'action' => 'appointments', 'middleware' => 'admin'],
        '/admin/reports' => ['controller' => 'AdminController', 'action' => 'reports', 'middleware' => 'admin'],
    ],
    'POST' => [
        '/login' => ['controller' => 'AuthController', 'action' => 'login'],
        
        // Student routes
        '/student/appointments/store' => ['controller' => 'StudentController', 'action' => 'storeAppointment', 'middleware' => 'student'],
        '/student/appointments/cancel' => ['controller' => 'StudentController', 'action' => 'cancelAppointment', 'middleware' => 'student'],
        
        // Advisor routes
        '/advisor/appointments/confirm' => ['controller' => 'AdvisorController', 'action' => 'confirmAppointment', 'middleware' => 'advisor'],
        '/advisor/appointments/decline' => ['controller' => 'AdvisorController', 'action' => 'declineAppointment', 'middleware' => 'advisor'],
        '/advisor/availability/store' => ['controller' => 'AdvisorController', 'action' => 'storeAvailability', 'middleware' => 'advisor'],
        '/advisor/availability/delete' => ['controller' => 'AdvisorController', 'action' => 'deleteAvailability', 'middleware' => 'advisor'],
        
        // Admin routes
        '/admin/users/store' => ['controller' => 'AdminController', 'action' => 'storeUser', 'middleware' => 'admin'],
        '/admin/users/delete' => ['controller' => 'AdminController', 'action' => 'deleteUser', 'middleware' => 'admin'],
        '/admin/users/import' => ['controller' => 'AdminController', 'action' => 'importUsers', 'middleware' => 'admin'],
    ]
];

// Match route
$route = null;
if (isset($routes[$method][$path])) {
    $route = $routes[$method][$path];
} else {
    // Try legacy query param routing for backward compatibility
    if (isset($query['page'])) {
        $page = $query['page'];
        $legacyRoutes = [
            'home' => ['controller' => 'HomeController', 'action' => 'index'],
            'login' => ['controller' => 'AuthController', 'action' => 'showLogin'],
            'students' => ['controller' => 'StudentController', 'action' => 'dashboard', 'middleware' => 'student'],
            'advisors' => ['controller' => 'AdvisorController', 'action' => 'dashboard', 'middleware' => 'advisor'],
            'admin' => ['controller' => 'AdminController', 'action' => 'dashboard', 'middleware' => 'admin'],
        ];
        
        if (isset($legacyRoutes[$page])) {
            $route = $legacyRoutes[$page];
        }
    }
}

// Execute route
if ($route) {
    // Check middleware
    if (isset($route['middleware'])) {
        $auth = new Auth();
        if (!$auth->checkRole($route['middleware'])) {
            header('Location: /login');
            exit;
        }
    }
    
    // Load and execute controller
    $controller = loadController($route['controller']);
    if ($controller && method_exists($controller, $route['action'])) {
        $controller->{$route['action']}();
        exit;
    }
}

// 404 Not Found
http_response_code(404);
echo "404 - Page Not Found";
