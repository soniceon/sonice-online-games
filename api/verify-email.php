<?php
require_once __DIR__ . '/../includes/Database.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    
    $email = $_GET['email'] ?? '';
    $token = $_GET['token'] ?? '';
    
    if (!$email || !$token) {
        throw new Exception('无效的验证链接');
    }
    
    // 验证令牌
    $stmt = $db->executeQuery(
        'SELECT id, is_active FROM users WHERE email = ? AND activation_code = ?',
        [$email, $token]
    );
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception('无效或已过期的验证链接');
    }
    
    if ($user['is_active']) {
        throw new Exception('账号已经激活');
    }
    
    // 激活账号
    $db->executeQuery(
        'UPDATE users SET is_active = 1, activation_code = NULL WHERE id = ?',
        [$user['id']]
    );
    
    echo json_encode([
        'success' => true,
        'message' => '账号激活成功！您现在可以登录了。'
    ]);
    
} catch (Exception $e) {
    error_log('Verification error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 
require_once __DIR__ . '/db.php';
header('Content-Type: text/html; charset=utf-8');
$email = trim($_GET['email'] ?? '');
$token = trim($_GET['token'] ?? '');
if (!$email || !$token) {
    echo '激活链接无效。';
    exit;
}
$stmt = $conn->prepare('SELECT id, is_active FROM users WHERE email=? AND verify_token=?');
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) {
    echo '激活链接无效或已过期。';
    exit;
}
if ($user['is_active']) {
    echo '账号已激活，请直接登录。';
    exit;
}
$stmt = $conn->prepare('UPDATE users SET is_active=1, verify_token=NULL WHERE id=?');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
echo '激活成功！你现在可以登录了。'; 
    if ($user['is_active']) {
        throw new Exception('账号已经激活');
    }
    
    // 激活账号
    $db->executeQuery(
        'UPDATE users SET is_active = 1, activation_code = NULL WHERE id = ?',
        [$user['id']]
    );
    
    echo json_encode([
        'success' => true,
        'message' => '账号激活成功！您现在可以登录了。'
    ]);
    
} catch (Exception $e) {
    error_log('Verification error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 
require_once __DIR__ . '/db.php';
header('Content-Type: text/html; charset=utf-8');
$email = trim($_GET['email'] ?? '');
$token = trim($_GET['token'] ?? '');
if (!$email || !$token) {
    echo '激活链接无效。';
    exit;
}
$stmt = $conn->prepare('SELECT id, is_active FROM users WHERE email=? AND verify_token=?');
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) {
    echo '激活链接无效或已过期。';
    exit;
}
if ($user['is_active']) {
    echo '账号已激活，请直接登录。';
    exit;
}
$stmt = $conn->prepare('UPDATE users SET is_active=1, verify_token=NULL WHERE id=?');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
echo '激活成功！你现在可以登录了。'; 
    if ($user['is_active']) {
        throw new Exception('账号已经激活');
    }
    
    // 激活账号
    $db->executeQuery(
        'UPDATE users SET is_active = 1, activation_code = NULL WHERE id = ?',
        [$user['id']]
    );
    
    echo json_encode([
        'success' => true,
        'message' => '账号激活成功！您现在可以登录了。'
    ]);
    
} catch (Exception $e) {
    error_log('Verification error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 
require_once __DIR__ . '/db.php';
header('Content-Type: text/html; charset=utf-8');
$email = trim($_GET['email'] ?? '');
$token = trim($_GET['token'] ?? '');
if (!$email || !$token) {
    echo '激活链接无效。';
    exit;
}
$stmt = $conn->prepare('SELECT id, is_active FROM users WHERE email=? AND verify_token=?');
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) {
    echo '激活链接无效或已过期。';
    exit;
}
if ($user['is_active']) {
    echo '账号已激活，请直接登录。';
    exit;
}
$stmt = $conn->prepare('UPDATE users SET is_active=1, verify_token=NULL WHERE id=?');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
echo '激活成功！你现在可以登录了。'; 
    if ($user['is_active']) {
        throw new Exception('账号已经激活');
    }
    
    // 激活账号
    $db->executeQuery(
        'UPDATE users SET is_active = 1, activation_code = NULL WHERE id = ?',
        [$user['id']]
    );
    
    echo json_encode([
        'success' => true,
        'message' => '账号激活成功！您现在可以登录了。'
    ]);
    
} catch (Exception $e) {
    error_log('Verification error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 
require_once __DIR__ . '/db.php';
header('Content-Type: text/html; charset=utf-8');
$email = trim($_GET['email'] ?? '');
$token = trim($_GET['token'] ?? '');
if (!$email || !$token) {
    echo '激活链接无效。';
    exit;
}
$stmt = $conn->prepare('SELECT id, is_active FROM users WHERE email=? AND verify_token=?');
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) {
    echo '激活链接无效或已过期。';
    exit;
}
if ($user['is_active']) {
    echo '账号已激活，请直接登录。';
    exit;
}
$stmt = $conn->prepare('UPDATE users SET is_active=1, verify_token=NULL WHERE id=?');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
echo '激活成功！你现在可以登录了。'; 
    if ($user['is_active']) {
        throw new Exception('账号已经激活');
    }
    
    // 激活账号
    $db->executeQuery(
        'UPDATE users SET is_active = 1, activation_code = NULL WHERE id = ?',
        [$user['id']]
    );
    
    echo json_encode([
        'success' => true,
        'message' => '账号激活成功！您现在可以登录了。'
    ]);
    
} catch (Exception $e) {
    error_log('Verification error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 
require_once __DIR__ . '/db.php';
header('Content-Type: text/html; charset=utf-8');
$email = trim($_GET['email'] ?? '');
$token = trim($_GET['token'] ?? '');
if (!$email || !$token) {
    echo '激活链接无效。';
    exit;
}
$stmt = $conn->prepare('SELECT id, is_active FROM users WHERE email=? AND verify_token=?');
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) {
    echo '激活链接无效或已过期。';
    exit;
}
if ($user['is_active']) {
    echo '账号已激活，请直接登录。';
    exit;
}
$stmt = $conn->prepare('UPDATE users SET is_active=1, verify_token=NULL WHERE id=?');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
echo '激活成功！你现在可以登录了。'; 
    if ($user['is_active']) {
        throw new Exception('账号已经激活');
    }
    
    // 激活账号
    $db->executeQuery(
        'UPDATE users SET is_active = 1, activation_code = NULL WHERE id = ?',
        [$user['id']]
    );
    
    echo json_encode([
        'success' => true,
        'message' => '账号激活成功！您现在可以登录了。'
    ]);
    
} catch (Exception $e) {
    error_log('Verification error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 
require_once __DIR__ . '/db.php';
header('Content-Type: text/html; charset=utf-8');
$email = trim($_GET['email'] ?? '');
$token = trim($_GET['token'] ?? '');
if (!$email || !$token) {
    echo '激活链接无效。';
    exit;
}
$stmt = $conn->prepare('SELECT id, is_active FROM users WHERE email=? AND verify_token=?');
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) {
    echo '激活链接无效或已过期。';
    exit;
}
if ($user['is_active']) {
    echo '账号已激活，请直接登录。';
    exit;
}
$stmt = $conn->prepare('UPDATE users SET is_active=1, verify_token=NULL WHERE id=?');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
echo '激活成功！你现在可以登录了。'; 
    if ($user['is_active']) {
        throw new Exception('账号已经激活');
    }
    
    // 激活账号
    $db->executeQuery(
        'UPDATE users SET is_active = 1, activation_code = NULL WHERE id = ?',
        [$user['id']]
    );
    
    echo json_encode([
        'success' => true,
        'message' => '账号激活成功！您现在可以登录了。'
    ]);
    
} catch (Exception $e) {
    error_log('Verification error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 
require_once __DIR__ . '/db.php';
header('Content-Type: text/html; charset=utf-8');
$email = trim($_GET['email'] ?? '');
$token = trim($_GET['token'] ?? '');
if (!$email || !$token) {
    echo '激活链接无效。';
    exit;
}
$stmt = $conn->prepare('SELECT id, is_active FROM users WHERE email=? AND verify_token=?');
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) {
    echo '激活链接无效或已过期。';
    exit;
}
if ($user['is_active']) {
    echo '账号已激活，请直接登录。';
    exit;
}
$stmt = $conn->prepare('UPDATE users SET is_active=1, verify_token=NULL WHERE id=?');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
echo '激活成功！你现在可以登录了。'; 
    if ($user['is_active']) {
        throw new Exception('账号已经激活');
    }
    
    // 激活账号
    $db->executeQuery(
        'UPDATE users SET is_active = 1, activation_code = NULL WHERE id = ?',
        [$user['id']]
    );
    
    echo json_encode([
        'success' => true,
        'message' => '账号激活成功！您现在可以登录了。'
    ]);
    
} catch (Exception $e) {
    error_log('Verification error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 
require_once __DIR__ . '/db.php';
header('Content-Type: text/html; charset=utf-8');
$email = trim($_GET['email'] ?? '');
$token = trim($_GET['token'] ?? '');
if (!$email || !$token) {
    echo '激活链接无效。';
    exit;
}
$stmt = $conn->prepare('SELECT id, is_active FROM users WHERE email=? AND verify_token=?');
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) {
    echo '激活链接无效或已过期。';
    exit;
}
if ($user['is_active']) {
    echo '账号已激活，请直接登录。';
    exit;
}
$stmt = $conn->prepare('UPDATE users SET is_active=1, verify_token=NULL WHERE id=?');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
echo '激活成功！你现在可以登录了。'; 