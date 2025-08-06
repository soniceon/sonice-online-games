<?php
require_once __DIR__ . '/db.php';
$config = require __DIR__ . '/config.php';

header('Content-Type: application/json');

// 开启错误报告
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 检查数据库连接
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'msg' => '数据库连接失败',
        'error' => $conn->connect_error,
        'debug' => [
            'host' => $config['db_host'],
            'user' => $config['db_user'],
            'database' => $config['db_name']
        ]
    ]);
    exit;
}

// 获取POST数据
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// 记录请求数据
error_log("Registration attempt - Username: $username, Email: $email");

// 验证输入
if (!$username || !$email || !$password) {
    echo json_encode([
        'success' => false,
        'msg' => '请填写所有必填字段',
        'debug' => [
            'post_data' => $_POST,
            'username' => $username,
            'email' => $email,
            'password_length' => strlen($password)
        ]
    ]);
    exit;
}

// 检查用户名和邮箱是否已存在
$stmt = $conn->prepare('SELECT id, username, email FROM users WHERE username=? OR email=?');
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'msg' => '数据库查询准备失败',
        'error' => $conn->error
    ]);
    exit;
}

$stmt->bind_param('ss', $username, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $error_msg = '';
    if ($row['username'] === $username) {
        $error_msg = '用户名已被使用';
    }
    if ($row['email'] === $email) {
        $error_msg = $error_msg ? $error_msg . '，邮箱已被注册' : '邮箱已被注册';
    }
    echo json_encode(['success' => false, 'msg' => $error_msg]);
    exit;
}
$stmt->close();

// 密码加密
$hash = password_hash($password, PASSWORD_DEFAULT);
// 生成验证令牌
$token = bin2hex(random_bytes(32));
// 生成默认头像
$avatar = 'https://api.dicebear.com/7.x/adventurer/svg?seed=' . urlencode($username);

// 插入新用户
$stmt = $conn->prepare('INSERT INTO users (username, email, password, is_active, verify_token, avatar) VALUES (?, ?, ?, 0, ?, ?)');
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'msg' => '创建用户失败',
        'error' => $conn->error,
        'debug' => [
            'last_error' => $conn->error,
            'error_no' => $conn->errno,
            'sql_state' => $conn->sqlstate
        ]
    ]);
    exit;
}

$stmt->bind_param('sssss', $username, $email, $hash, $token, $avatar);

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'msg' => '注册失败',
        'error' => $stmt->error,
        'debug' => [
            'last_query' => $conn->info,
            'insert_id' => $conn->insert_id,
            'error_no' => $stmt->errno,
            'sql_state' => $stmt->sqlstate,
            'param_count' => $stmt->param_count
        ]
    ]);
    exit;
}

$user_id = $stmt->insert_id;
$stmt->close();

// 如果成功插入用户，发送验证邮件
if ($user_id) {
    require_once __DIR__ . '/PHPMailer/Exception.php';
    require_once __DIR__ . '/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/SMTP.php';
    
    // 获取基础URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $base_url = $protocol . $_SERVER['HTTP_HOST'];
    if (dirname($_SERVER['PHP_SELF']) !== '/') {
        $base_url .= dirname(dirname($_SERVER['PHP_SELF']));
    }
    
    $verify_link = $base_url . '/pages/verify-email.php?email=' . urlencode($email) . '&token=' . $token;
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_user'];
        $mail->Password = $config['smtp_pass'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $config['smtp_port'];
        $mail->CharSet = 'UTF-8';
        
        // 启用调试
        $mail->SMTPDebug = 3;
        $debug_output = '';
        $mail->Debugoutput = function($str, $level) use (&$debug_output) {
            $debug_output .= date('Y-m-d H:i:s') . " [$level] $str\n";
        };
        
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->Subject = '激活您的Sonice账号';
        $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>欢迎加入 Sonice Games!</h2>
            <p>感谢您的注册。请点击下面的链接激活您的账号：</p>
            <p><a href='$verify_link' style='display: inline-block; padding: 10px 20px; background-color: #4A90E2; color: white; text-decoration: none; border-radius: 5px;'>激活账号</a></p>
            <p>或复制以下链接到浏览器：</p>
            <p>$verify_link</p>
            <p>如果这不是您的操作，请忽略此邮件。</p>
            <p>祝您游戏愉快！<br>Sonice Games 团队</p>
        </body>
        </html>";
        
        $mail_sent = $mail->send();
        
        echo json_encode([
            'success' => true,
            'msg' => '注册成功！请检查邮箱激活账号',
            'mail_sent' => $mail_sent,
            'debug_info' => $debug_output,
            'verify_link' => $verify_link // 仅用于调试
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => true,
            'msg' => '注册成功但邮件发送失败，请联系管理员',
            'mail_sent' => false,
            'error' => $e->getMessage(),
            'debug_info' => $debug_output,
            'verify_link' => $verify_link // 仅用于调试
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'msg' => '注册失败，请稍后重试',
        'error' => $conn->error
    ]);
}

$conn->close(); 
    echo json_encode(['success' => false, 'msg' => '注册失败']);
} 

// 密码加密
$hash = password_hash($password, PASSWORD_DEFAULT);
// 生成验证令牌
$token = bin2hex(random_bytes(32));
// 生成默认头像
$avatar = 'https://api.dicebear.com/7.x/adventurer/svg?seed=' . urlencode($username);

// 插入新用户
$stmt = $conn->prepare('INSERT INTO users (username, email, password, is_active, verify_token, avatar) VALUES (?, ?, ?, 0, ?, ?)');
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'msg' => '创建用户失败',
        'error' => $conn->error,
        'debug' => [
            'last_error' => $conn->error,
            'error_no' => $conn->errno,
            'sql_state' => $conn->sqlstate
        ]
    ]);
    exit;
}

$stmt->bind_param('sssss', $username, $email, $hash, $token, $avatar);

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'msg' => '注册失败',
        'error' => $stmt->error,
        'debug' => [
            'last_query' => $conn->info,
            'insert_id' => $conn->insert_id,
            'error_no' => $stmt->errno,
            'sql_state' => $stmt->sqlstate,
            'param_count' => $stmt->param_count
        ]
    ]);
    exit;
}

$user_id = $stmt->insert_id;
$stmt->close();

// 如果成功插入用户，发送验证邮件
if ($user_id) {
    require_once __DIR__ . '/PHPMailer/Exception.php';
    require_once __DIR__ . '/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/SMTP.php';
    
    // 获取基础URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $base_url = $protocol . $_SERVER['HTTP_HOST'];
    if (dirname($_SERVER['PHP_SELF']) !== '/') {
        $base_url .= dirname(dirname($_SERVER['PHP_SELF']));
    }
    
    $verify_link = $base_url . '/pages/verify-email.php?email=' . urlencode($email) . '&token=' . $token;
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_user'];
        $mail->Password = $config['smtp_pass'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $config['smtp_port'];
        $mail->CharSet = 'UTF-8';
        
        // 启用调试
        $mail->SMTPDebug = 3;
        $debug_output = '';
        $mail->Debugoutput = function($str, $level) use (&$debug_output) {
            $debug_output .= date('Y-m-d H:i:s') . " [$level] $str\n";
        };
        
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->Subject = '激活您的Sonice账号';
        $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>欢迎加入 Sonice Games!</h2>
            <p>感谢您的注册。请点击下面的链接激活您的账号：</p>
            <p><a href='$verify_link' style='display: inline-block; padding: 10px 20px; background-color: #4A90E2; color: white; text-decoration: none; border-radius: 5px;'>激活账号</a></p>
            <p>或复制以下链接到浏览器：</p>
            <p>$verify_link</p>
            <p>如果这不是您的操作，请忽略此邮件。</p>
            <p>祝您游戏愉快！<br>Sonice Games 团队</p>
        </body>
        </html>";
        
        $mail_sent = $mail->send();
        
        echo json_encode([
            'success' => true,
            'msg' => '注册成功！请检查邮箱激活账号',
            'mail_sent' => $mail_sent,
            'debug_info' => $debug_output,
            'verify_link' => $verify_link // 仅用于调试
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => true,
            'msg' => '注册成功但邮件发送失败，请联系管理员',
            'mail_sent' => false,
            'error' => $e->getMessage(),
            'debug_info' => $debug_output,
            'verify_link' => $verify_link // 仅用于调试
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'msg' => '注册失败，请稍后重试',
        'error' => $conn->error
    ]);
}

$conn->close(); 
    echo json_encode(['success' => false, 'msg' => '注册失败']);
} 

// 密码加密
$hash = password_hash($password, PASSWORD_DEFAULT);
// 生成验证令牌
$token = bin2hex(random_bytes(32));
// 生成默认头像
$avatar = 'https://api.dicebear.com/7.x/adventurer/svg?seed=' . urlencode($username);

// 插入新用户
$stmt = $conn->prepare('INSERT INTO users (username, email, password, is_active, verify_token, avatar) VALUES (?, ?, ?, 0, ?, ?)');
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'msg' => '创建用户失败',
        'error' => $conn->error,
        'debug' => [
            'last_error' => $conn->error,
            'error_no' => $conn->errno,
            'sql_state' => $conn->sqlstate
        ]
    ]);
    exit;
}

$stmt->bind_param('sssss', $username, $email, $hash, $token, $avatar);

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'msg' => '注册失败',
        'error' => $stmt->error,
        'debug' => [
            'last_query' => $conn->info,
            'insert_id' => $conn->insert_id,
            'error_no' => $stmt->errno,
            'sql_state' => $stmt->sqlstate,
            'param_count' => $stmt->param_count
        ]
    ]);
    exit;
}

$user_id = $stmt->insert_id;
$stmt->close();

// 如果成功插入用户，发送验证邮件
if ($user_id) {
    require_once __DIR__ . '/PHPMailer/Exception.php';
    require_once __DIR__ . '/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/SMTP.php';
    
    // 获取基础URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $base_url = $protocol . $_SERVER['HTTP_HOST'];
    if (dirname($_SERVER['PHP_SELF']) !== '/') {
        $base_url .= dirname(dirname($_SERVER['PHP_SELF']));
    }
    
    $verify_link = $base_url . '/pages/verify-email.php?email=' . urlencode($email) . '&token=' . $token;
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_user'];
        $mail->Password = $config['smtp_pass'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $config['smtp_port'];
        $mail->CharSet = 'UTF-8';
        
        // 启用调试
        $mail->SMTPDebug = 3;
        $debug_output = '';
        $mail->Debugoutput = function($str, $level) use (&$debug_output) {
            $debug_output .= date('Y-m-d H:i:s') . " [$level] $str\n";
        };
        
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->Subject = '激活您的Sonice账号';
        $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>欢迎加入 Sonice Games!</h2>
            <p>感谢您的注册。请点击下面的链接激活您的账号：</p>
            <p><a href='$verify_link' style='display: inline-block; padding: 10px 20px; background-color: #4A90E2; color: white; text-decoration: none; border-radius: 5px;'>激活账号</a></p>
            <p>或复制以下链接到浏览器：</p>
            <p>$verify_link</p>
            <p>如果这不是您的操作，请忽略此邮件。</p>
            <p>祝您游戏愉快！<br>Sonice Games 团队</p>
        </body>
        </html>";
        
        $mail_sent = $mail->send();
        
        echo json_encode([
            'success' => true,
            'msg' => '注册成功！请检查邮箱激活账号',
            'mail_sent' => $mail_sent,
            'debug_info' => $debug_output,
            'verify_link' => $verify_link // 仅用于调试
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => true,
            'msg' => '注册成功但邮件发送失败，请联系管理员',
            'mail_sent' => false,
            'error' => $e->getMessage(),
            'debug_info' => $debug_output,
            'verify_link' => $verify_link // 仅用于调试
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'msg' => '注册失败，请稍后重试',
        'error' => $conn->error
    ]);
}

$conn->close(); 
    echo json_encode(['success' => false, 'msg' => '注册失败']);
}

// 密码加密
$hash = password_hash($password, PASSWORD_DEFAULT);
// 生成验证令牌
$token = bin2hex(random_bytes(32));
// 生成默认头像
$avatar = 'https://api.dicebear.com/7.x/adventurer/svg?seed=' . urlencode($username);

// 插入新用户
$stmt = $conn->prepare('INSERT INTO users (username, email, password, is_active, verify_token, avatar) VALUES (?, ?, ?, 0, ?, ?)');
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'msg' => '创建用户失败',
        'error' => $conn->error,
        'debug' => [
            'last_error' => $conn->error,
            'error_no' => $conn->errno,
            'sql_state' => $conn->sqlstate
        ]
    ]);
    exit;
}

$stmt->bind_param('sssss', $username, $email, $hash, $token, $avatar);

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'msg' => '注册失败',
        'error' => $stmt->error,
        'debug' => [
            'last_query' => $conn->info,
            'insert_id' => $conn->insert_id,
            'error_no' => $stmt->errno,
            'sql_state' => $stmt->sqlstate,
            'param_count' => $stmt->param_count
        ]
    ]);
    exit;
}

$user_id = $stmt->insert_id;
$stmt->close();

// 如果成功插入用户，发送验证邮件
if ($user_id) {
    require_once __DIR__ . '/PHPMailer/Exception.php';
    require_once __DIR__ . '/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/SMTP.php';
    
    // 获取基础URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $base_url = $protocol . $_SERVER['HTTP_HOST'];
    if (dirname($_SERVER['PHP_SELF']) !== '/') {
        $base_url .= dirname(dirname($_SERVER['PHP_SELF']));
    }
    
    $verify_link = $base_url . '/pages/verify-email.php?email=' . urlencode($email) . '&token=' . $token;
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_user'];
        $mail->Password = $config['smtp_pass'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $config['smtp_port'];
        $mail->CharSet = 'UTF-8';
        
        // 启用调试
        $mail->SMTPDebug = 3;
        $debug_output = '';
        $mail->Debugoutput = function($str, $level) use (&$debug_output) {
            $debug_output .= date('Y-m-d H:i:s') . " [$level] $str\n";
        };
        
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->Subject = '激活您的Sonice账号';
        $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>欢迎加入 Sonice Games!</h2>
            <p>感谢您的注册。请点击下面的链接激活您的账号：</p>
            <p><a href='$verify_link' style='display: inline-block; padding: 10px 20px; background-color: #4A90E2; color: white; text-decoration: none; border-radius: 5px;'>激活账号</a></p>
            <p>或复制以下链接到浏览器：</p>
            <p>$verify_link</p>
            <p>如果这不是您的操作，请忽略此邮件。</p>
            <p>祝您游戏愉快！<br>Sonice Games 团队</p>
        </body>
        </html>";
        
        $mail_sent = $mail->send();
        
        echo json_encode([
            'success' => true,
            'msg' => '注册成功！请检查邮箱激活账号',
            'mail_sent' => $mail_sent,
            'debug_info' => $debug_output,
            'verify_link' => $verify_link // 仅用于调试
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => true,
            'msg' => '注册成功但邮件发送失败，请联系管理员',
            'mail_sent' => false,
            'error' => $e->getMessage(),
            'debug_info' => $debug_output,
            'verify_link' => $verify_link // 仅用于调试
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'msg' => '注册失败，请稍后重试',
        'error' => $conn->error
    ]);
}

$conn->close(); 
    echo json_encode(['success' => false, 'msg' => '注册失败']);
} 

// 密码加密
$hash = password_hash($password, PASSWORD_DEFAULT);
// 生成验证令牌
$token = bin2hex(random_bytes(32));
// 生成默认头像
$avatar = 'https://api.dicebear.com/7.x/adventurer/svg?seed=' . urlencode($username);

// 插入新用户
$stmt = $conn->prepare('INSERT INTO users (username, email, password, is_active, verify_token, avatar) VALUES (?, ?, ?, 0, ?, ?)');
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'msg' => '创建用户失败',
        'error' => $conn->error,
        'debug' => [
            'last_error' => $conn->error,
            'error_no' => $conn->errno,
            'sql_state' => $conn->sqlstate
        ]
    ]);
    exit;
}

$stmt->bind_param('sssss', $username, $email, $hash, $token, $avatar);

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'msg' => '注册失败',
        'error' => $stmt->error,
        'debug' => [
            'last_query' => $conn->info,
            'insert_id' => $conn->insert_id,
            'error_no' => $stmt->errno,
            'sql_state' => $stmt->sqlstate,
            'param_count' => $stmt->param_count
        ]
    ]);
    exit;
}

$user_id = $stmt->insert_id;
$stmt->close();

// 如果成功插入用户，发送验证邮件
if ($user_id) {
    require_once __DIR__ . '/PHPMailer/Exception.php';
    require_once __DIR__ . '/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/SMTP.php';
    
    // 获取基础URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $base_url = $protocol . $_SERVER['HTTP_HOST'];
    if (dirname($_SERVER['PHP_SELF']) !== '/') {
        $base_url .= dirname(dirname($_SERVER['PHP_SELF']));
    }
    
    $verify_link = $base_url . '/pages/verify-email.php?email=' . urlencode($email) . '&token=' . $token;
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_user'];
        $mail->Password = $config['smtp_pass'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $config['smtp_port'];
        $mail->CharSet = 'UTF-8';
        
        // 启用调试
        $mail->SMTPDebug = 3;
        $debug_output = '';
        $mail->Debugoutput = function($str, $level) use (&$debug_output) {
            $debug_output .= date('Y-m-d H:i:s') . " [$level] $str\n";
        };
        
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->Subject = '激活您的Sonice账号';
        $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>欢迎加入 Sonice Games!</h2>
            <p>感谢您的注册。请点击下面的链接激活您的账号：</p>
            <p><a href='$verify_link' style='display: inline-block; padding: 10px 20px; background-color: #4A90E2; color: white; text-decoration: none; border-radius: 5px;'>激活账号</a></p>
            <p>或复制以下链接到浏览器：</p>
            <p>$verify_link</p>
            <p>如果这不是您的操作，请忽略此邮件。</p>
            <p>祝您游戏愉快！<br>Sonice Games 团队</p>
        </body>
        </html>";
        
        $mail_sent = $mail->send();
        
        echo json_encode([
            'success' => true,
            'msg' => '注册成功！请检查邮箱激活账号',
            'mail_sent' => $mail_sent,
            'debug_info' => $debug_output,
            'verify_link' => $verify_link // 仅用于调试
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => true,
            'msg' => '注册成功但邮件发送失败，请联系管理员',
            'mail_sent' => false,
            'error' => $e->getMessage(),
            'debug_info' => $debug_output,
            'verify_link' => $verify_link // 仅用于调试
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'msg' => '注册失败，请稍后重试',
        'error' => $conn->error
    ]);
}

$conn->close(); 
    echo json_encode(['success' => false, 'msg' => '注册失败']);
} 

// 密码加密
$hash = password_hash($password, PASSWORD_DEFAULT);
// 生成验证令牌
$token = bin2hex(random_bytes(32));
// 生成默认头像
$avatar = 'https://api.dicebear.com/7.x/adventurer/svg?seed=' . urlencode($username);

// 插入新用户
$stmt = $conn->prepare('INSERT INTO users (username, email, password, is_active, verify_token, avatar) VALUES (?, ?, ?, 0, ?, ?)');
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'msg' => '创建用户失败',
        'error' => $conn->error,
        'debug' => [
            'last_error' => $conn->error,
            'error_no' => $conn->errno,
            'sql_state' => $conn->sqlstate
        ]
    ]);
    exit;
}

$stmt->bind_param('sssss', $username, $email, $hash, $token, $avatar);

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'msg' => '注册失败',
        'error' => $stmt->error,
        'debug' => [
            'last_query' => $conn->info,
            'insert_id' => $conn->insert_id,
            'error_no' => $stmt->errno,
            'sql_state' => $stmt->sqlstate,
            'param_count' => $stmt->param_count
        ]
    ]);
    exit;
}

$user_id = $stmt->insert_id;
$stmt->close();

// 如果成功插入用户，发送验证邮件
if ($user_id) {
    require_once __DIR__ . '/PHPMailer/Exception.php';
    require_once __DIR__ . '/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/SMTP.php';
    
    // 获取基础URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $base_url = $protocol . $_SERVER['HTTP_HOST'];
    if (dirname($_SERVER['PHP_SELF']) !== '/') {
        $base_url .= dirname(dirname($_SERVER['PHP_SELF']));
    }
    
    $verify_link = $base_url . '/pages/verify-email.php?email=' . urlencode($email) . '&token=' . $token;
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_user'];
        $mail->Password = $config['smtp_pass'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $config['smtp_port'];
        $mail->CharSet = 'UTF-8';
        
        // 启用调试
        $mail->SMTPDebug = 3;
        $debug_output = '';
        $mail->Debugoutput = function($str, $level) use (&$debug_output) {
            $debug_output .= date('Y-m-d H:i:s') . " [$level] $str\n";
        };
        
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->Subject = '激活您的Sonice账号';
        $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>欢迎加入 Sonice Games!</h2>
            <p>感谢您的注册。请点击下面的链接激活您的账号：</p>
            <p><a href='$verify_link' style='display: inline-block; padding: 10px 20px; background-color: #4A90E2; color: white; text-decoration: none; border-radius: 5px;'>激活账号</a></p>
            <p>或复制以下链接到浏览器：</p>
            <p>$verify_link</p>
            <p>如果这不是您的操作，请忽略此邮件。</p>
            <p>祝您游戏愉快！<br>Sonice Games 团队</p>
        </body>
        </html>";
        
        $mail_sent = $mail->send();
        
        echo json_encode([
            'success' => true,
            'msg' => '注册成功！请检查邮箱激活账号',
            'mail_sent' => $mail_sent,
            'debug_info' => $debug_output,
            'verify_link' => $verify_link // 仅用于调试
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => true,
            'msg' => '注册成功但邮件发送失败，请联系管理员',
            'mail_sent' => false,
            'error' => $e->getMessage(),
            'debug_info' => $debug_output,
            'verify_link' => $verify_link // 仅用于调试
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'msg' => '注册失败，请稍后重试',
        'error' => $conn->error
    ]);
}

$conn->close(); 
    echo json_encode(['success' => false, 'msg' => '注册失败']);
} 

// 密码加密
$hash = password_hash($password, PASSWORD_DEFAULT);
// 生成验证令牌
$token = bin2hex(random_bytes(32));
// 生成默认头像
$avatar = 'https://api.dicebear.com/7.x/adventurer/svg?seed=' . urlencode($username);

// 插入新用户
$stmt = $conn->prepare('INSERT INTO users (username, email, password, is_active, verify_token, avatar) VALUES (?, ?, ?, 0, ?, ?)');
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'msg' => '创建用户失败',
        'error' => $conn->error,
        'debug' => [
            'last_error' => $conn->error,
            'error_no' => $conn->errno,
            'sql_state' => $conn->sqlstate
        ]
    ]);
    exit;
}

$stmt->bind_param('sssss', $username, $email, $hash, $token, $avatar);

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'msg' => '注册失败',
        'error' => $stmt->error,
        'debug' => [
            'last_query' => $conn->info,
            'insert_id' => $conn->insert_id,
            'error_no' => $stmt->errno,
            'sql_state' => $stmt->sqlstate,
            'param_count' => $stmt->param_count
        ]
    ]);
    exit;
}

$user_id = $stmt->insert_id;
$stmt->close();

// 如果成功插入用户，发送验证邮件
if ($user_id) {
    require_once __DIR__ . '/PHPMailer/Exception.php';
    require_once __DIR__ . '/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/SMTP.php';
    
    // 获取基础URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $base_url = $protocol . $_SERVER['HTTP_HOST'];
    if (dirname($_SERVER['PHP_SELF']) !== '/') {
        $base_url .= dirname(dirname($_SERVER['PHP_SELF']));
    }
    
    $verify_link = $base_url . '/pages/verify-email.php?email=' . urlencode($email) . '&token=' . $token;
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_user'];
        $mail->Password = $config['smtp_pass'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $config['smtp_port'];
        $mail->CharSet = 'UTF-8';
        
        // 启用调试
        $mail->SMTPDebug = 3;
        $debug_output = '';
        $mail->Debugoutput = function($str, $level) use (&$debug_output) {
            $debug_output .= date('Y-m-d H:i:s') . " [$level] $str\n";
        };
        
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->Subject = '激活您的Sonice账号';
        $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>欢迎加入 Sonice Games!</h2>
            <p>感谢您的注册。请点击下面的链接激活您的账号：</p>
            <p><a href='$verify_link' style='display: inline-block; padding: 10px 20px; background-color: #4A90E2; color: white; text-decoration: none; border-radius: 5px;'>激活账号</a></p>
            <p>或复制以下链接到浏览器：</p>
            <p>$verify_link</p>
            <p>如果这不是您的操作，请忽略此邮件。</p>
            <p>祝您游戏愉快！<br>Sonice Games 团队</p>
        </body>
        </html>";
        
        $mail_sent = $mail->send();
        
        echo json_encode([
            'success' => true,
            'msg' => '注册成功！请检查邮箱激活账号',
            'mail_sent' => $mail_sent,
            'debug_info' => $debug_output,
            'verify_link' => $verify_link // 仅用于调试
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => true,
            'msg' => '注册成功但邮件发送失败，请联系管理员',
            'mail_sent' => false,
            'error' => $e->getMessage(),
            'debug_info' => $debug_output,
            'verify_link' => $verify_link // 仅用于调试
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'msg' => '注册失败，请稍后重试',
        'error' => $conn->error
    ]);
}

$conn->close(); 
    echo json_encode(['success' => false, 'msg' => '注册失败']);
}

// 密码加密
$hash = password_hash($password, PASSWORD_DEFAULT);
// 生成验证令牌
$token = bin2hex(random_bytes(32));
// 生成默认头像
$avatar = 'https://api.dicebear.com/7.x/adventurer/svg?seed=' . urlencode($username);

// 插入新用户
$stmt = $conn->prepare('INSERT INTO users (username, email, password, is_active, verify_token, avatar) VALUES (?, ?, ?, 0, ?, ?)');
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'msg' => '创建用户失败',
        'error' => $conn->error,
        'debug' => [
            'last_error' => $conn->error,
            'error_no' => $conn->errno,
            'sql_state' => $conn->sqlstate
        ]
    ]);
    exit;
}

$stmt->bind_param('sssss', $username, $email, $hash, $token, $avatar);

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'msg' => '注册失败',
        'error' => $stmt->error,
        'debug' => [
            'last_query' => $conn->info,
            'insert_id' => $conn->insert_id,
            'error_no' => $stmt->errno,
            'sql_state' => $stmt->sqlstate,
            'param_count' => $stmt->param_count
        ]
    ]);
    exit;
}

$user_id = $stmt->insert_id;
$stmt->close();

// 如果成功插入用户，发送验证邮件
if ($user_id) {
    require_once __DIR__ . '/PHPMailer/Exception.php';
    require_once __DIR__ . '/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/SMTP.php';
    
    // 获取基础URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $base_url = $protocol . $_SERVER['HTTP_HOST'];
    if (dirname($_SERVER['PHP_SELF']) !== '/') {
        $base_url .= dirname(dirname($_SERVER['PHP_SELF']));
    }
    
    $verify_link = $base_url . '/pages/verify-email.php?email=' . urlencode($email) . '&token=' . $token;
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_user'];
        $mail->Password = $config['smtp_pass'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $config['smtp_port'];
        $mail->CharSet = 'UTF-8';
        
        // 启用调试
        $mail->SMTPDebug = 3;
        $debug_output = '';
        $mail->Debugoutput = function($str, $level) use (&$debug_output) {
            $debug_output .= date('Y-m-d H:i:s') . " [$level] $str\n";
        };
        
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->Subject = '激活您的Sonice账号';
        $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>欢迎加入 Sonice Games!</h2>
            <p>感谢您的注册。请点击下面的链接激活您的账号：</p>
            <p><a href='$verify_link' style='display: inline-block; padding: 10px 20px; background-color: #4A90E2; color: white; text-decoration: none; border-radius: 5px;'>激活账号</a></p>
            <p>或复制以下链接到浏览器：</p>
            <p>$verify_link</p>
            <p>如果这不是您的操作，请忽略此邮件。</p>
            <p>祝您游戏愉快！<br>Sonice Games 团队</p>
        </body>
        </html>";
        
        $mail_sent = $mail->send();
        
        echo json_encode([
            'success' => true,
            'msg' => '注册成功！请检查邮箱激活账号',
            'mail_sent' => $mail_sent,
            'debug_info' => $debug_output,
            'verify_link' => $verify_link // 仅用于调试
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => true,
            'msg' => '注册成功但邮件发送失败，请联系管理员',
            'mail_sent' => false,
            'error' => $e->getMessage(),
            'debug_info' => $debug_output,
            'verify_link' => $verify_link // 仅用于调试
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'msg' => '注册失败，请稍后重试',
        'error' => $conn->error
    ]);
}

$conn->close(); 
    echo json_encode(['success' => false, 'msg' => '注册失败']);
} 