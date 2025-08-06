<?php
session_start();
header('Content-Type: application/json');

// 清除所有会话数据
$_SESSION = array();

// 销毁会话 cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// 销毁会话
session_destroy();

echo json_encode(['success' => true, 'msg' => '已成功退出登录']); 
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Logged out.']); 