<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';
$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');
if (!$email) {
    echo json_encode(['success' => false, 'message' => '邮箱不能为空']);
    exit;
}
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();
if (!$user) {
    echo json_encode(['success' => false, 'message' => '邮箱未注册']);
    exit;
}
$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', time() + 3600);
$stmt = $pdo->prepare('UPDATE users SET reset_token=?, reset_expires=? WHERE email=?');
$stmt->execute([$token, $expires, $email]);
$link = '{{ base_url }}/reset-password?token=' . $token;
echo json_encode(['success' => true, 'reset_link' => $link, 'message' => '重置链接已生成（实际应发送邮件）']); 