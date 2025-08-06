<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$avatar = trim($input['avatar'] ?? '');
if (!$avatar) {
    echo json_encode(['success' => false, 'message' => 'No avatar provided']);
    exit;
}

$stmt = $pdo->prepare('UPDATE users SET avatar = ? WHERE id = ?');
$success = $stmt->execute([$avatar, $_SESSION['user_id']]);
if ($success) {
    echo json_encode(['success' => true, 'avatar' => $avatar]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update avatar']);
} 