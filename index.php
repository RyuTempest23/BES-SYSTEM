<?php
// index.php - RESTful API Router (using query parameter 'route')

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/jwt_helper.php';

// Get route from query string (e.g., ?route=auth)
$route = $_GET['route'] ?? '';
$action = $_GET['action'] ?? '';

// Serve the login page when no API route is specified.
if (!$route && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once __DIR__ . '/views/auth/login.php';
    exit;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


// Add action to $_GET for modules that expect it
if ($action) {
    $_GET['action'] = $action;
}

switch ($route) {
    case 'auth':
        require_once __DIR__ . '/modules/m1_auth.php';
        break;
    case 'residents':
        require_once __DIR__ . '/modules/m2_residents.php';
        break;
    case 'requests':
        require_once __DIR__ . '/modules/m3_requests.php';
        break;
    case 'admin':
        require_once __DIR__ . '/modules/m4_admin.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'API endpoint not found']);
}
?>