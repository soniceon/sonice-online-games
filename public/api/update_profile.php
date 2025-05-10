<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => '未登录']);
    exit;
}
$input = json_decode(file_get_contents('php://input'), true);
$username = trim($input['username'] ?? '');
if (!$username) {
    echo json_encode(['success' => false, 'message' => '用户名不能为空']);
    exit;
}
// 检查用户名是否重复（可选）
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
$stmt->execute([$username, $_SESSION['user_id']]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => '用户名已被占用']);
    exit;
}
// 更新用户名
$stmt = $pdo->prepare('UPDATE users SET username = ? WHERE id = ?');
$success = $stmt->execute([$username, $_SESSION['user_id']]);
if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => '保存失败']);
} 