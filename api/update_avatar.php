<?php
session_start();
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'msg' => '请先登录']);
    exit;
}

$avatar = $_POST['avatar'] ?? '';
$userId = $_SESSION['user']['id'];

if (!$avatar) {
    echo json_encode(['success' => false, 'msg' => '请选择头像']);
    exit;
}

// 如果是 base64 图片数据
if (strpos($avatar, 'data:image/') === 0) {
    $imgData = explode(',', $avatar, 2)[1];
    $imgBinary = base64_decode($imgData);
    $imgName = 'avatar_' . uniqid() . '.png';
    $uploadDir = __DIR__ . '/uploads/avatars/';
    
    // 创建上传目录
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // 保存图片
    if (file_put_contents($uploadDir . $imgName, $imgBinary)) {
        $avatar = '/api/uploads/avatars/' . $imgName;
    } else {
        echo json_encode(['success' => false, 'msg' => '图片上传失败']);
        exit;
    }
}

// 更新数据库
$stmt = $conn->prepare('UPDATE users SET avatar = ? WHERE id = ?');
$stmt->bind_param('si', $avatar, $userId);

if ($stmt->execute()) {
    // 更新 session 中的头像
    $_SESSION['user']['avatar'] = $avatar;
    echo json_encode(['success' => true, 'msg' => '头像更新成功', 'avatar' => $avatar]);
} else {
    echo json_encode(['success' => false, 'msg' => '头像更新失败']);
} 
 
 
session_start();
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'msg' => '请先登录']);
    exit;
}

$avatar = $_POST['avatar'] ?? '';
$userId = $_SESSION['user']['id'];

if (!$avatar) {
    echo json_encode(['success' => false, 'msg' => '请选择头像']);
    exit;
}

// 如果是 base64 图片数据
if (strpos($avatar, 'data:image/') === 0) {
    $imgData = explode(',', $avatar, 2)[1];
    $imgBinary = base64_decode($imgData);
    $imgName = 'avatar_' . uniqid() . '.png';
    $uploadDir = __DIR__ . '/uploads/avatars/';
    
    // 创建上传目录
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // 保存图片
    if (file_put_contents($uploadDir . $imgName, $imgBinary)) {
        $avatar = '/api/uploads/avatars/' . $imgName;
    } else {
        echo json_encode(['success' => false, 'msg' => '图片上传失败']);
        exit;
    }
}

// 更新数据库
$stmt = $conn->prepare('UPDATE users SET avatar = ? WHERE id = ?');
$stmt->bind_param('si', $avatar, $userId);

if ($stmt->execute()) {
    // 更新 session 中的头像
    $_SESSION['user']['avatar'] = $avatar;
    echo json_encode(['success' => true, 'msg' => '头像更新成功', 'avatar' => $avatar]);
} else {
    echo json_encode(['success' => false, 'msg' => '头像更新失败']);
} 
 
session_start();
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'msg' => '请先登录']);
    exit;
}

$avatar = $_POST['avatar'] ?? '';
$userId = $_SESSION['user']['id'];

if (!$avatar) {
    echo json_encode(['success' => false, 'msg' => '请选择头像']);
    exit;
}

// 如果是 base64 图片数据
if (strpos($avatar, 'data:image/') === 0) {
    $imgData = explode(',', $avatar, 2)[1];
    $imgBinary = base64_decode($imgData);
    $imgName = 'avatar_' . uniqid() . '.png';
    $uploadDir = __DIR__ . '/uploads/avatars/';
    
    // 创建上传目录
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // 保存图片
    if (file_put_contents($uploadDir . $imgName, $imgBinary)) {
        $avatar = '/api/uploads/avatars/' . $imgName;
    } else {
        echo json_encode(['success' => false, 'msg' => '图片上传失败']);
        exit;
    }
}

// 更新数据库
$stmt = $conn->prepare('UPDATE users SET avatar = ? WHERE id = ?');
$stmt->bind_param('si', $avatar, $userId);

if ($stmt->execute()) {
    // 更新 session 中的头像
    $_SESSION['user']['avatar'] = $avatar;
    echo json_encode(['success' => true, 'msg' => '头像更新成功', 'avatar' => $avatar]);
} else {
    echo json_encode(['success' => false, 'msg' => '头像更新失败']);
} 
 
 
session_start();
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'msg' => '请先登录']);
    exit;
}

$avatar = $_POST['avatar'] ?? '';
$userId = $_SESSION['user']['id'];

if (!$avatar) {
    echo json_encode(['success' => false, 'msg' => '请选择头像']);
    exit;
}

// 如果是 base64 图片数据
if (strpos($avatar, 'data:image/') === 0) {
    $imgData = explode(',', $avatar, 2)[1];
    $imgBinary = base64_decode($imgData);
    $imgName = 'avatar_' . uniqid() . '.png';
    $uploadDir = __DIR__ . '/uploads/avatars/';
    
    // 创建上传目录
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // 保存图片
    if (file_put_contents($uploadDir . $imgName, $imgBinary)) {
        $avatar = '/api/uploads/avatars/' . $imgName;
    } else {
        echo json_encode(['success' => false, 'msg' => '图片上传失败']);
        exit;
    }
}

// 更新数据库
$stmt = $conn->prepare('UPDATE users SET avatar = ? WHERE id = ?');
$stmt->bind_param('si', $avatar, $userId);

if ($stmt->execute()) {
    // 更新 session 中的头像
    $_SESSION['user']['avatar'] = $avatar;
    echo json_encode(['success' => true, 'msg' => '头像更新成功', 'avatar' => $avatar]);
} else {
    echo json_encode(['success' => false, 'msg' => '头像更新失败']);
} 
 