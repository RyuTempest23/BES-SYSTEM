<?php
// modules/m2_residents.php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/jwt_helper.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $pdo = getDB();
    $user = requireAuth(); // only authenticated users can access

    // ---------- PERMISSION SETUP ----------
    // Admin and BHW have full access to all resident actions.
    // Residents can only access the 'edit' action (to update their own profile).
    $isAdminOrBhw = in_array($user['role'], ['admin', 'bhw']);
    $isResident = ($user['role'] === 'resident');

    if (!$isAdminOrBhw && !$isResident) {
        jsonResponse(['error' => 'Forbidden: insufficient privileges'], 403);
    }

    // ---------- LIST RESIDENTS (GET) ----------
    if ($action === 'list' && $method === 'GET') {
        // Only admin/BHW can list residents
        if (!$isAdminOrBhw) {
            jsonResponse(['error' => 'Forbidden'], 403);
        }

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = max(1, (int)($_GET['limit'] ?? 10));
        $offset = ($page - 1) * $limit;
        $search = trim($_GET['search'] ?? '');

        $query = "SELECT * FROM residents WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $query .= " AND full_name LIKE ?";
            $params[] = "%$search%";
        }

        $countQuery = str_replace("SELECT *", "SELECT COUNT(*) as total", $query);
        $countStmt = $pdo->prepare($countQuery);
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];

        $query .= " ORDER BY id DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $pdo->prepare($query);
        foreach ($params as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key + 1, $value, $paramType);
        }
        $stmt->execute();
        $residents = $stmt->fetchAll();

        jsonResponse([
            'success' => true,
            'data' => $residents,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    // ---------- ADD RESIDENT (POST) ----------
    if ($action === 'add' && $method === 'POST') {
        // Only admin/BHW can add residents
        if (!$isAdminOrBhw) {
            jsonResponse(['error' => 'Forbidden'], 403);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $required = ['full_name'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                jsonResponse(['error' => "Missing field: $field"], 400);
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO residents (full_name, birthdate, address, contact_number, registered_voter, added_by)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['full_name'],
            $data['birthdate'] ?? null,
            $data['address'] ?? null,
            $data['contact_number'] ?? null,
            $data['registered_voter'] ?? 'no',
            $user['email']
        ]);

        jsonResponse(['success' => true, 'message' => 'Resident added', 'id' => $pdo->lastInsertId()], 201);
    }

    // ---------- EDIT RESIDENT (PUT) - also allows residents to edit themselves ----------
    if ($action === 'edit' && $method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id'])) {
            jsonResponse(['error' => 'Resident ID required'], 400);
        }

        $residentId = (int)$data['id'];

        // Permission check: residents can only edit their own record
        if ($isResident) {
            if ($user['resident_id'] != $residentId) {
                jsonResponse(['error' => 'You can only update your own profile'], 403);
            }
        } elseif (!$isAdminOrBhw) {
            jsonResponse(['error' => 'Forbidden'], 403);
        }

        // Build updatable fields (same as before)
        $fields = [];
        $params = [];
        $updatable = ['full_name', 'birthdate', 'address', 'contact_number', 'registered_voter'];
        
        foreach ($updatable as $field) {
            if (array_key_exists($field, $data)) {
                $value = $data[$field];
                if ($value === '' && in_array($field, ['birthdate', 'address', 'contact_number'])) {
                    $value = null;
                }
                $fields[] = "$field = ?";
                $params[] = $value;
            }
        }
        
        if (empty($fields)) {
            jsonResponse(['error' => 'No fields to update'], 400);
        }
        
        // Add last_updated_by (who made the change)
        $params[] = $user['email'];
        $params[] = $residentId;

        $sql = "UPDATE residents SET " . implode(', ', $fields) . ", last_updated_by = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        jsonResponse(['success' => true, 'message' => 'Resident updated']);
    }

    // ---------- DELETE RESIDENT (DELETE) ----------
    if ($action === 'delete' && $method === 'DELETE') {
        // Only admin/BHW can delete
        if (!$isAdminOrBhw) {
            jsonResponse(['error' => 'Forbidden'], 403);
        }

        $id = $_GET['id'] ?? 0;
        if (!$id) {
            jsonResponse(['error' => 'Resident ID required'], 400);
        }

        $stmt = $pdo->prepare("SELECT id FROM users WHERE resident_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetch()) {
            jsonResponse(['error' => 'Cannot delete resident with an existing user account'], 409);
        }

        $stmt = $pdo->prepare("DELETE FROM residents WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse(['success' => true, 'message' => 'Resident deleted']);
    }

    // ---------- EXPORT RESIDENTS (GET) ----------
    if ($action === 'export' && $method === 'GET') {
        // Only admin/BHW can export
        if (!$isAdminOrBhw) {
            jsonResponse(['error' => 'Forbidden'], 403);
        }

        $format = $_GET['format'] ?? 'csv';
        $stmt = $pdo->query("SELECT id, full_name, birthdate, address, contact_number, registered_voter, added_by, created_at FROM residents ORDER BY id");
        $residents = $stmt->fetchAll();

        if ($format === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="residents.csv"');
            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID', 'Full Name', 'Birthdate', 'Address', 'Contact', 'Voter', 'Added By', 'Created At']);
            foreach ($residents as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
            exit;
        } else {
            jsonResponse(['success' => true, 'data' => $residents]);
        }
    }

    // If no valid action matched
    jsonResponse(['error' => 'Invalid action'], 400);
    
} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>