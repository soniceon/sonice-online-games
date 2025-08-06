<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';
$input = json_decode(file_get_contents('php://input'), true);
$username = trim($input['username'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
if (!$username || !$email || !$password) {
    echo json_encode(['success' => false, 'message' => '所有字段均为必填']);
    exit;
}
// 检查用户名或邮箱是否已存在
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1');
$stmt->execute(['username' => $username, 'email' => $email]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => '用户名或邮箱已存在']);
    exit;
}
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (:username, :email, :password)');
$ok = $stmt->execute(['username' => $username, 'email' => $email, 'password' => $hash]);
if ($ok) {
    session_start();
    $_SESSION['user_id'] = $pdo->lastInsertId();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => '注册失败']);
} 