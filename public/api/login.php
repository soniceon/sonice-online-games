<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../../config/database.php';
$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';
if (!$username || !$password) {
    echo json_encode(['success' => false, 'message' => '用户名和密码不能为空']);
    exit;
}
$stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = :username OR email = :username LIMIT 1');
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();
if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => '用户名或密码错误']);
} 