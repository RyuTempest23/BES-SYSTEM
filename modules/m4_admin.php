<?php
// modules/m4_admin.php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/jwt_helper.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $pdo = getDB();
    $user = requireAuth();

    if ($user['role'] !== 'admin') {
        jsonResponse(['error' => 'Admin access required'], 403);
    }

    // ---------- PENDING REQUESTS COUNT & LIST ----------
    if ($action === 'pending_requests' && $method === 'GET') {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM certificate_requests WHERE status = 'pending'");
        $count = $stmt->fetch()['count'];

        $stmt = $pdo->prepare("
            SELECT cr.*, u.email, r.full_name 
            FROM certificate_requests cr
            JOIN users u ON cr.user_id = u.id
            JOIN residents r ON u.resident_id = r.id
            WHERE cr.status = 'pending'
            ORDER BY cr.requested_at ASC
        ");
        $stmt->execute();
        $requests = $stmt->fetchAll();

        jsonResponse(['success' => true, 'count' => $count, 'data' => $requests]);
    }

    // ---------- APPROVED REQUESTS LIST (GET) ----------
    if ($action === 'approved_requests' && $method === 'GET') {
        $stmt = $pdo->prepare("SELECT cr.*, u.email, r.full_name
            FROM certificate_requests cr
            JOIN users u ON cr.user_id = u.id
            JOIN residents r ON u.resident_id = r.id
            WHERE cr.status = 'approved'
            ORDER BY cr.approved_at ASC");
        $stmt->execute();
        $requests = $stmt->fetchAll();
        jsonResponse(['success' => true, 'count' => count($requests), 'data' => $requests]);
    }

    // ---------- PENDING VERIFICATIONS (for account verification) ----------
    if ($action === 'pending_verifications' && $method === 'GET') {
        $statusFilter = $_GET['status'] ?? 'pending';
        $validStatuses = ['pending', 'approved', 'rejected'];

        if (!in_array($statusFilter, $validStatuses)) {
            $statusFilter = 'pending';
        }

        // For 'pending', only show those with uploaded documents
        // For 'approved' and 'rejected', show all
        $whereClause = ($statusFilter === 'pending')
            ? "u.verification_status = ? AND u.verification_doc IS NOT NULL"
            : "u.verification_status = ?";

        $stmt = $pdo->prepare("
            SELECT u.*, r.full_name, r.address 
            FROM users u
            JOIN residents r ON u.resident_id = r.id
            WHERE $whereClause
            ORDER BY u.updated_at DESC
        ");
        $stmt->execute([$statusFilter]);
        $users = $stmt->fetchAll();
        jsonResponse(['success' => true, 'count' => count($users), 'data' => $users]);
    }

    // ---------- APPROVE OR REJECT ACCOUNT VERIFICATION ----------
    if ($action === 'verify_account' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $data['user_id'] ?? 0;
        $decision = $data['decision'] ?? ''; // 'approve' or 'reject'
        if (!$userId || !in_array($decision, ['approve', 'reject'])) {
            jsonResponse(['error' => 'Invalid request'], 400);
        }

        $newStatus = ($decision === 'approve') ? 'approved' : 'rejected';
        $stmt = $pdo->prepare("UPDATE users SET verification_status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $userId]);
        jsonResponse(['success' => true, 'message' => "Account {$decision}d"]);
    }

    // ---------- APPROVE CERTIFICATE REQUEST ----------
    if ($action === 'approve_request' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $requestId = $data['request_id'] ?? 0;
        $adminNotes = $data['admin_notes'] ?? null;

        $stmt = $pdo->prepare("UPDATE certificate_requests SET status = 'approved', approved_at = NOW(), admin_notes = ? WHERE id = ? AND status = 'pending'");
        $stmt->execute([$adminNotes, $requestId]);
        if ($stmt->rowCount() === 0) {
            jsonResponse(['error' => 'Request not found or not pending'], 404);
        }
        jsonResponse(['success' => true, 'message' => 'Request approved']);
    }

    // ---------- REJECT / DISAPPROVE REQUEST ----------
    if ($action === 'reject_request' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $requestId = $data['request_id'] ?? 0;
        $adminNotes = $data['admin_notes'] ?? null;

        $stmt = $pdo->prepare("UPDATE certificate_requests SET status = 'rejected', rejected_at = NOW(), admin_notes = ? WHERE id = ? AND status = 'pending'");
        $stmt->execute([$adminNotes, $requestId]);
        jsonResponse(['success' => true, 'message' => 'Request rejected']);
    }

    // ---------- MARK AS COMPLETED ----------
    if ($action === 'mark_completed' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $requestId = $data['request_id'] ?? 0;
        $stmt = $pdo->prepare("UPDATE certificate_requests SET status = 'completed', completed_at = NOW() WHERE id = ? AND status = 'approved'");
        $stmt->execute([$requestId]);
        jsonResponse(['success' => true, 'message' => 'Request marked as completed']);
    }

    // ---------- YEARLY REPORT ----------
    if ($action === 'yearly_report' && $method === 'GET') {
        $year = (int)($_GET['year'] ?? date('Y'));
        $stmt = $pdo->prepare("
            SELECT cr.*, u.email, r.full_name 
            FROM certificate_requests cr
            JOIN users u ON cr.user_id = u.id
            JOIN residents r ON u.resident_id = r.id
            WHERE cr.status = 'completed' AND YEAR(cr.completed_at) = ?
            ORDER BY cr.completed_at DESC
        ");
        $stmt->execute([$year]);
        $reports = $stmt->fetchAll();
        jsonResponse(['success' => true, 'year' => $year, 'data' => $reports]);
    }

    // ---------- GET USER'S ALL REQUESTS ----------
    if ($action === 'user_requests' && $method === 'GET') {
        $userId = (int)($_GET['user_id'] ?? 0);
        if (!$userId) {
            jsonResponse(['error' => 'User ID required'], 400);
        }
        $stmt = $pdo->prepare("
            SELECT cr.* 
            FROM certificate_requests cr
            WHERE cr.user_id = ?
            ORDER BY cr.requested_at DESC
        ");
        $stmt->execute([$userId]);
        $requests = $stmt->fetchAll();
        jsonResponse(['success' => true, 'data' => $requests]);
    }

    // ---------- GET SINGLE REQUEST (for printing) ----------
    if ($action === 'get_request' && $method === 'GET') {
        $requestId = (int)($_GET['id'] ?? 0);
        if (!$requestId) {
            jsonResponse(['error' => 'Request ID required'], 400);
        }
        $stmt = $pdo->prepare("
            SELECT cr.*, u.email, r.full_name, r.address 
            FROM certificate_requests cr
            JOIN users u ON cr.user_id = u.id
            JOIN residents r ON u.resident_id = r.id
            WHERE cr.id = ?
        ");
        $stmt->execute([$requestId]);
        $request = $stmt->fetch();
        if (!$request) {
            jsonResponse(['error' => 'Request not found'], 404);
        }
        jsonResponse(['success' => true, 'data' => $request]);
    }

    jsonResponse(['error' => 'Invalid action'], 400);
} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
