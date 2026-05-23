<?php
// modules/m1_auth.php - Authentication Module (RESTful API)

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $pdo = getDB();
    
    // ========== SIGNUP ==========
    if ($action === 'signup' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $full_name = trim($data['full_name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $confirm_password = $data['confirm_password'] ?? '';
        
        // Validation
        if (!$full_name || !$email || !$password) {
            jsonResponse(['error' => 'All fields are required'], 400);
        }
        
        if ($password !== $confirm_password) {
            jsonResponse(['error' => 'Passwords do not match'], 400);
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(['error' => 'Invalid email format'], 400);
        }
        
        if (strlen($password) < 6) {
            jsonResponse(['error' => 'Password must be at least 6 characters'], 400);
        }
        
        // Check if resident exists in barangay master list
        $stmt = $pdo->prepare("SELECT id FROM residents WHERE full_name = ?");
        $stmt->execute([$full_name]);
        $resident = $stmt->fetch();
        
        if (!$resident) {
            jsonResponse(['error' => 'Name not found in barangay records. Please visit the barangay hall to register first.'], 404);
        }
        
        // Check if user account already exists for this resident
        $stmt = $pdo->prepare("SELECT id FROM users WHERE resident_id = ?");
        $stmt->execute([$resident['id']]);
        if ($stmt->fetch()) {
            jsonResponse(['error' => 'An account already exists for this resident'], 409);
        }
        
        // Check if email is already used
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            jsonResponse(['error' => 'Email already registered'], 409);
        }
        
        // Create user account
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO users (resident_id, email, password, role, verification_status) 
            VALUES (?, ?, ?, 'resident', 'pending')
        ");
        
        if ($stmt->execute([$resident['id'], $email, $hashed_password])) {
            jsonResponse([
                'success' => true, 
                'message' => 'Account created successfully! Please login.',
                'user_id' => $pdo->lastInsertId()
            ], 201);
        } else {
            jsonResponse(['error' => 'Failed to create account'], 500);
        }
    }
    
    // ========== LOGIN ==========
    if ($action === 'login' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        
        if (!$email || !$password) {
            jsonResponse(['error' => 'Email and password are required'], 400);
        }
        
        // Get user with resident details
        $stmt = $pdo->prepare("
            SELECT u.*, r.full_name, r.address, r.birthdate, r.contact_number 
            FROM users u 
            JOIN residents r ON u.resident_id = r.id 
            WHERE u.email = ?
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            jsonResponse(['error' => 'Invalid email or password'], 401);
        }

        $storedHash = $user['password'];
        error_log("Login attempt - Email: $email");
        error_log("Stored hash: " . $storedHash);
        error_log("Password entered: $password");

        $isValidPassword = password_verify($password, $storedHash);
        error_log("password_verify() returned " . ($isValidPassword ? 'TRUE' : 'FALSE'));

        if (!$isValidPassword) {
            jsonResponse(['error' => 'Invalid email or password'], 401);
        }
        
        // Generate JWT token
        $token = generateJWT([
            'user_id' => $user['id'],
            'resident_id' => $user['resident_id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'verification_status' => $user['verification_status']
        ]);

        if (!$token) {
            jsonResponse(['error' => 'Failed to generate authentication token'], 500);
        }
        
        // Remove password from response
        unset($user['password']);
        
        jsonResponse([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'resident_id' => $user['resident_id'],
                'name' => $user['full_name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'verification_status' => $user['verification_status'],
                'address' => $user['address']
            ]
        ]);
    }
    
    // ========== GET PROFILE (Protected) ==========
    if ($action === 'profile' && $method === 'GET') {
        $user = requireAuth();
        
        $stmt = $pdo->prepare("
            SELECT u.*, r.full_name, r.address, r.birthdate, r.contact_number, r.registered_voter
            FROM users u 
            JOIN residents r ON u.resident_id = r.id 
            WHERE u.id = ?
        ");
        $stmt->execute([$user['user_id']]);
        $profile = $stmt->fetch();
        
        if (!$profile) {
            jsonResponse(['error' => 'User not found'], 404);
        }
        
        unset($profile['password']);
        
        jsonResponse([
            'success' => true,
            'profile' => $profile
        ]);
    }
    
    // ========== UPLOAD VERIFICATION ID (Protected) ==========
    if ($action === 'upload_id' && $method === 'POST') {
        $user = requireAuth();
        
        if (!isset($_FILES['verification_doc']) || $_FILES['verification_doc']['error'] !== UPLOAD_ERR_OK) {
            jsonResponse(['error' => 'Please upload a valid ID document'], 400);
        }
        
        $file = $_FILES['verification_doc'];
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            jsonResponse(['error' => 'Only JPG, PNG, and PDF files are allowed'], 400);
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            jsonResponse(['error' => 'File size must be less than 5MB'], 400);
        }
        
        // Create uploads directory if not exists
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }
        
        $filename = 'id_' . $user['user_id'] . '_' . time() . '.' . $ext;
        $filepath = UPLOAD_DIR . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $stmt = $pdo->prepare("UPDATE users SET verification_doc = ?, verification_status = 'pending' WHERE id = ?");
            $stmt->execute([$filename, $user['user_id']]);
            
            jsonResponse([
                'success' => true, 
                'message' => 'ID uploaded successfully. Please wait for admin verification.'
            ]);
        } else {
            jsonResponse(['error' => 'Failed to upload file'], 500);
        }
    }
    
    // ========== CHANGE PASSWORD (Protected) ==========
    if ($action === 'change_password' && $method === 'POST') {
        $user = requireAuth();
        $data = json_decode(file_get_contents('php://input'), true);
        
        $current_password = $data['current_password'] ?? '';
        $new_password = $data['new_password'] ?? '';
        
        if (!$current_password || !$new_password) {
            jsonResponse(['error' => 'Current and new password are required'], 400);
        }
        
        if (strlen($new_password) < 6) {
            jsonResponse(['error' => 'New password must be at least 6 characters'], 400);
        }
        
        // Get user's current password hash
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user['user_id']]);
        $userRecord = $stmt->fetch();
        
        if (!$userRecord) {
            jsonResponse(['error' => 'User not found'], 404);
        }
        
        // Verify current password
        if (!password_verify($current_password, $userRecord['password'])) {
            jsonResponse(['error' => 'Current password is incorrect'], 401);
        }
        
        // Hash new password and update
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        
        if ($stmt->execute([$hashed_new_password, $user['user_id']])) {
            jsonResponse([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);
        } else {
            jsonResponse(['error' => 'Failed to change password'], 500);
        }
    }
    
    // If no action matched
    jsonResponse(['error' => 'Invalid action'], 400);
    
} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>