<?php
header('Content-Type: application/json');
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => '未登录']);
    exit;
}
if (!isset($_FILES['avatar'])) {
    echo json_encode(['success' => false, 'message' => '未上传文件']);
    exit;
}
$ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
$filename = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
$targetDir = __DIR__ . '/../../assets/avatars/';
if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
$target = $targetDir . $filename;
if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
    require_once __DIR__ . '/../../config/database.php';
    $url = '/assets/avatars/' . $filename;
    $stmt = $pdo->prepare('UPDATE users SET avatar = ? WHERE id = ?');
    $stmt->execute([$url, $_SESSION['user_id']]);
    echo json_encode(['success' => true, 'avatar' => $url]);
} else {
    echo json_encode(['success' => false, 'message' => '上传失败']);
} 