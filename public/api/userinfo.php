<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../../config/database.php';
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('SELECT id, username, email, avatar FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
echo json_encode(['user' => $user]); 