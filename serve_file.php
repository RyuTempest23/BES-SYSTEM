<?php
// serve_file.php - Secure file serving endpoint for uploaded documents
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/jwt_helper.php';

// Verify user is authenticated and has permission
$user = null;
try {
    $user = requireAuth();
    // Allow admins to view any verification document
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        die('Forbidden: Admin access required to view verification documents');
    }
} catch (Exception $e) {
    http_response_code(401);
    die('Unauthorized: Please login as admin');
}

$filename = $_GET['file'] ?? '';

// Validate filename - prevent directory traversal attacks
if (!$filename || preg_match('/[^a-zA-Z0-9._-]/', $filename)) {
    http_response_code(400);
    die('Invalid filename');
}

$filepath = UPLOAD_DIR . $filename;

// Check if file exists and is within the upload directory
if (!file_exists($filepath) || !is_file($filepath)) {
    http_response_code(404);
    die('File not found');
}

// Verify the file is actually within the upload directory
$realUploadDir = realpath(UPLOAD_DIR);
$realFilePath = realpath($filepath);
if (!$realFilePath || strpos($realFilePath, $realUploadDir) !== 0) {
    http_response_code(403);
    die('Forbidden: Invalid file path');
}

// Get file extension
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

// Set appropriate content type
$mimeTypes = [
    'pdf' => 'application/pdf',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif'
];

$contentType = $mimeTypes[$ext] ?? 'application/octet-stream';
header('Content-Type: ' . $contentType);
header('Content-Disposition: inline; filename="' . basename($filepath) . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Output file
readfile($filepath);
exit;
