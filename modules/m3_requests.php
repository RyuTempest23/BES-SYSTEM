<?php
// modules/m3_requests.php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/jwt_helper.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $pdo = getDB();
    $user = requireAuth();

    // Only residents (verified) can use this module
    if ($user['role'] !== 'resident') {
        jsonResponse(['error' => 'Only residents can request certificates'], 403);
    }
    if ($user['verification_status'] !== 'approved') {
        jsonResponse(['error' => 'Your account is not yet verified. Please upload an ID and wait for admin approval.'], 403);
    }

    // ---------- SUBMIT REQUEST (POST) ----------
    if ($action === 'submit' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $required = ['certificate_type', 'purpose'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                jsonResponse(['error' => "Missing field: $field"], 400);
            }
        }

        $quantity = (int)($data['quantity'] ?? 1);
        if ($quantity < 1) $quantity = 1;

        $stmt = $pdo->prepare("
            INSERT INTO certificate_requests (user_id, certificate_type, purpose, quantity, status)
            VALUES (?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([$user['user_id'], $data['certificate_type'], $data['purpose'], $quantity]);

        jsonResponse(['success' => true, 'message' => 'Request submitted', 'request_id' => $pdo->lastInsertId()], 201);
    }

    // ---------- MY REQUESTS (GET) ----------
    if ($action === 'my_requests' && $method === 'GET') {
        $status = $_GET['status'] ?? 'all';
        $sql = "SELECT * FROM certificate_requests WHERE user_id = ?";
        $params = [$user['user_id']];
        if ($status !== 'all') {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY requested_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $requests = $stmt->fetchAll();

        jsonResponse(['success' => true, 'data' => $requests]);
    }

    // ---------- CANCEL REQUEST (POST) ----------
    if ($action === 'cancel' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $requestId = $data['request_id'] ?? 0;
        if (!$requestId) {
            jsonResponse(['error' => 'Request ID required'], 400);
        }

        // Verify ownership and status 'pending'
        $stmt = $pdo->prepare("SELECT status FROM certificate_requests WHERE id = ? AND user_id = ?");
        $stmt->execute([$requestId, $user['user_id']]);
        $req = $stmt->fetch();
        if (!$req) {
            jsonResponse(['error' => 'Request not found'], 404);
        }
        if ($req['status'] !== 'pending') {
            jsonResponse(['error' => 'Only pending requests can be cancelled'], 400);
        }

        $stmt = $pdo->prepare("UPDATE certificate_requests SET status = 'cancelled', cancelled_at = NOW() WHERE id = ?");
        $stmt->execute([$requestId]);
        jsonResponse(['success' => true, 'message' => 'Request cancelled']);
    }

    // ---------- RESUBMIT / UPDATE QUANTITY (POST) ----------
    if ($action === 'resubmit' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $requestId = $data['request_id'] ?? 0;
        $newQuantity = (int)($data['quantity'] ?? 1);
        if (!$requestId || $newQuantity < 1) {
            jsonResponse(['error' => 'Valid request ID and quantity required'], 400);
        }

        $stmt = $pdo->prepare("SELECT status FROM certificate_requests WHERE id = ? AND user_id = ?");
        $stmt->execute([$requestId, $user['user_id']]);
        $req = $stmt->fetch();
        if (!$req) {
            jsonResponse(['error' => 'Request not found'], 404);
        }
        if (!in_array($req['status'], ['pending', 'cancelled'])) {
            jsonResponse(['error' => 'Only pending or cancelled requests can be resubmitted'], 400);
        }

        $stmt = $pdo->prepare("
            UPDATE certificate_requests 
            SET quantity = ?, status = 'pending', cancelled_at = NULL, requested_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$newQuantity, $requestId]);
        jsonResponse(['success' => true, 'message' => 'Request resubmitted']);
    }

    jsonResponse(['error' => 'Invalid action'], 400);
} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>