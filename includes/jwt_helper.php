<?php
// includes/jwt_helper.php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Generate JWT token
function generateJWT($userData) {
    $payload = [
        'user_id' => $userData['user_id'],
        'resident_id' => $userData['resident_id'],
        'email' => $userData['email'],
        'role' => $userData['role'],
        'verification_status' => $userData['verification_status'],
        'iat' => time(),
        'exp' => time() + (86400 * 7) // 7 days expiration
    ];
    
    try {
        return JWT::encode($payload, JWT_SECRET, 'HS256');
    } catch (Exception $e) {
        error_log('JWT generation failed: ' . $e->getMessage());
        return null;
    }
}

// Validate JWT token and return payload
function validateJWT($token) {
    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
        return (array) $decoded;
    } catch (Exception $e) {
        return null;
    }
}

// Get token from Authorization header
function getBearerToken() {
    $headers = getallheaders();
    
    // Check for Authorization header
    if (isset($headers['Authorization'])) {
        $matches = [];
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    
    // Also check for lowercase version
    if (isset($headers['authorization'])) {
        $matches = [];
        if (preg_match('/Bearer\s(\S+)/', $headers['authorization'], $matches)) {
            return $matches[1];
        }
    }
    
    return null;
}

// Require authentication for API endpoints
function requireAuth() {
    $token = getBearerToken();
    
    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit;
    }
    
    $user = validateJWT($token);
    
    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid or expired token']);
        exit;
    }
    
    return $user;
}
?>