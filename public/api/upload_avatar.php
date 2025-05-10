<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$file = $_FILES['avatar'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
if (!in_array($ext, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
    exit;
}
if ($file['size'] > 2 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'File too large (max 2MB)']);
    exit;
}
$dir = __DIR__ . '/../uploads/avatars/';
if (!is_dir($dir)) mkdir($dir, 0777, true);
$filename = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
$path = $dir . $filename;
if (!move_uploaded_file($file['tmp_name'], $path)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save file']);
    exit;
}
$url = '/sonice-online-games-new/public/uploads/avatars/' . $filename;
$stmt = $pdo->prepare('UPDATE users SET avatar = ? WHERE id = ?');
$success = $stmt->execute([$url, $_SESSION['user_id']]);
if ($success) {
    echo json_encode(['success' => true, 'avatar' => $url]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update avatar']);
} 