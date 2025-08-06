<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../../config/database.php';
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => '未登录']);
    exit;
}
$input = json_decode(file_get_contents('php://input'), true);
$avatar = $input['avatar'] ?? '';
if (!$avatar) {
    echo json_encode(['success' => false, 'message' => '头像URL不能为空']);
    exit;
}
$stmt = $pdo->prepare('UPDATE users SET avatar = ? WHERE id = ?');
$stmt->execute([$avatar, $_SESSION['user_id']]);
echo json_encode(['success' => true]); 