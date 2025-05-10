<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');
$email = trim($_GET['email'] ?? '');
if (!$email) {
    echo json_encode(['exists' => false]);
    exit;
}
$stmt = $conn->prepare('SELECT id FROM users WHERE email=?');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
echo json_encode(['exists' => $stmt->num_rows > 0]); 