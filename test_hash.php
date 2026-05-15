<?php
require_once 'config.php';      // ← Add this line
require_once 'includes/db.php';

$email = 'admin@gmail.com';
$password = 'admin123';

$pdo = getDB();
$stmt = $pdo->prepare("SELECT password FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    echo "❌ User not found with email: $email";
} else {
    echo "Stored hash: " . $user['password'] . "<br>";
    if (password_verify($password, $user['password'])) {
        echo "✅ Password is correct!";
    } else {
        echo "❌ Password does NOT match.";
    }
}
?>