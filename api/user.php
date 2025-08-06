<?php
session_start();
header('Content-Type: application/json');

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

// 处理更新头像请求
if ($_POST['action'] === 'update_avatar') {
    try {
        if (isset($_POST['avatar_url'])) {
            // 使用DiceBear API生成的头像URL
            $avatarUrl = $_POST['avatar_url'];
            
            // TODO: 在实际应用中，这里应该更新数据库中的用户头像URL
            $_SESSION['avatar'] = $avatarUrl;
            
            echo json_encode([
                'success' => true,
                'message' => 'Avatar updated successfully',
                'avatar_url' => $avatarUrl
            ]);
        } else if (isset($_FILES['avatar_file'])) {
            // 处理上传的头像文件
            $file = $_FILES['avatar_file'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception('Only JPG, PNG or GIF images are allowed');
            }
            
            if ($file['size'] > 5 * 1024 * 1024) { // 5MB
                throw new Exception('Image size cannot exceed 5MB');
            }
            
            // 生成唯一的文件名
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            
            // 确保上传目录存在
            $uploadDir = __DIR__ . '/../uploads/avatars/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // 移动上传的文件
            $targetPath = $uploadDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $avatarUrl = '/uploads/avatars/' . $filename;
                
                // TODO: 在实际应用中，这里应该更新数据库中的用户头像URL
                $_SESSION['avatar'] = $avatarUrl;
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Avatar uploaded successfully',
                    'avatar_url' => $avatarUrl
                ]);
            } else {
                throw new Exception('Failed to upload avatar');
            }
        } else {
            throw new Exception('No avatar data received');
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// 如果没有匹配的action，返回错误
echo json_encode([
    'success' => false,
    'message' => 'Invalid request'
]); 
session_start();
header('Content-Type: application/json');

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

// 处理更新头像请求
if ($_POST['action'] === 'update_avatar') {
    try {
        if (isset($_POST['avatar_url'])) {
            // 使用DiceBear API生成的头像URL
            $avatarUrl = $_POST['avatar_url'];
            
            // TODO: 在实际应用中，这里应该更新数据库中的用户头像URL
            $_SESSION['avatar'] = $avatarUrl;
            
            echo json_encode([
                'success' => true,
                'message' => 'Avatar updated successfully',
                'avatar_url' => $avatarUrl
            ]);
        } else if (isset($_FILES['avatar_file'])) {
            // 处理上传的头像文件
            $file = $_FILES['avatar_file'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception('Only JPG, PNG or GIF images are allowed');
            }
            
            if ($file['size'] > 5 * 1024 * 1024) { // 5MB
                throw new Exception('Image size cannot exceed 5MB');
            }
            
            // 生成唯一的文件名
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            
            // 确保上传目录存在
            $uploadDir = __DIR__ . '/../uploads/avatars/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // 移动上传的文件
            $targetPath = $uploadDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $avatarUrl = '/uploads/avatars/' . $filename;
                
                // TODO: 在实际应用中，这里应该更新数据库中的用户头像URL
                $_SESSION['avatar'] = $avatarUrl;
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Avatar uploaded successfully',
                    'avatar_url' => $avatarUrl
                ]);
            } else {
                throw new Exception('Failed to upload avatar');
            }
        } else {
            throw new Exception('No avatar data received');
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// 如果没有匹配的action，返回错误
echo json_encode([
    'success' => false,
    'message' => 'Invalid request'
]); 