<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

session_start(); // 添加 session 启动

$identity = trim($_POST['identity'] ?? $_POST['email'] ?? ''); // 兼容 identity 和 email 字段
$password = $_POST['password'] ?? '';

if (!$identity || !$password) {
    echo json_encode(['success' => false, 'msg' => '请填写所有字段']);
    exit;
}

$stmt = $conn->prepare('SELECT id, username, email, password, is_active, avatar FROM users WHERE username=? OR email=?');
$stmt->bind_param('ss', $identity, $identity);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) {
    echo json_encode(['success' => false, 'msg' => '用户不存在']);
    exit;
}
if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'msg' => '密码错误']);
    exit;
}
if (isset($user['is_active']) && !$user['is_active']) {
    echo json_encode(['success' => false, 'msg' => '请先激活邮箱']);
    exit;
}

// 设置 session
$_SESSION['user'] = [
    'id' => $user['id'],
    'username' => $user['username'],
    'email' => $user['email'],
    'avatar' => $user['avatar']
];

echo json_encode(['success' => true, 'msg' => '登录成功', 'user' => $_SESSION['user']]); 
    'email' => $user['email']
]]); 
    'avatar' => $user['avatar']
];

echo json_encode(['success' => true, 'msg' => '登录成功', 'user' => $_SESSION['user']]); 
    'email' => $user['email']
]]); 
    'avatar' => $user['avatar']
];

echo json_encode(['success' => true, 'msg' => '登录成功', 'user' => $_SESSION['user']]); 
    'email' => $user['email']
]]); 
    'avatar' => $user['avatar']
];

echo json_encode(['success' => true, 'msg' => '登录成功', 'user' => $_SESSION['user']]); 
    'email' => $user['email']
]]); 
    'avatar' => $user['avatar']
];

echo json_encode(['success' => true, 'msg' => '登录成功', 'user' => $_SESSION['user']]); 
    'email' => $user['email']
]]); 
    'avatar' => $user['avatar']
];

echo json_encode(['success' => true, 'msg' => '登录成功', 'user' => $_SESSION['user']]); 
    'email' => $user['email']
]]); 
    'avatar' => $user['avatar']
];

echo json_encode(['success' => true, 'msg' => '登录成功', 'user' => $_SESSION['user']]); 
    'email' => $user['email']
]]); 
    'avatar' => $user['avatar']
];

echo json_encode(['success' => true, 'msg' => '登录成功', 'user' => $_SESSION['user']]); 
    'email' => $user['email']
]]); 