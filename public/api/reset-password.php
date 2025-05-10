<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';
$input = json_decode(file_get_contents('php://input'), true);
$token = $input['token'] ?? '';
$password = $input['password'] ?? '';
if (!$token || !$password) {
    echo json_encode(['success' => false, 'message' => '参数不完整']);
    exit;
}
$stmt = $pdo->prepare('SELECT id FROM users WHERE reset_token=? AND reset_expires > NOW() LIMIT 1');
$stmt->execute([$token]);
$user = $stmt->fetch();
if (!$user) {
    echo json_encode(['success' => false, 'message' => '重置链接无效或已过期']);
    exit;
}
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?');
$stmt->execute([$hash, $user['id']]);
echo json_encode(['success' => true, 'message' => '密码已重置']); 