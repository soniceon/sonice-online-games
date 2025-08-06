<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

$input = json_decode(file_get_contents('php://input'), true);
$username = trim($input['username'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$lang = $input['lang'] ?? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);

if (!$username || !$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// 检查邮箱是否已存在
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    exit;
}

// 生成随机 DiceBear 头像
$seed = bin2hex(random_bytes(8));
$avatar = "https://api.dicebear.com/7.x/adventurer/svg?seed=$seed";

// 生成激活token
$activation_code = bin2hex(random_bytes(16));
$is_active = 0;

// 插入新用户
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO users (username, email, password, avatar, is_active, activation_code) VALUES (?, ?, ?, ?, ?, ?)');
$success = $stmt->execute([$username, $email, $hashedPassword, $avatar, $is_active, $activation_code]);
if ($success) {
    $user_id = $pdo->lastInsertId();
    // 发送激活邮件
    $site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
    $activation_link = $site_url . "/activate.php?token=$activation_code";
    send_activation_mail($email, $username, $activation_link, $lang);
    echo json_encode(['success' => true, 'need_activation' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed']);
}

function send_activation_mail($to, $username, $link, $lang) {
    $mail_texts = [
        'zh' => [
            'subject' => '激活你的 sonice.games 账号',
            'greeting' => '欢迎注册 sonice.games，祝你在这玩的愉快！',
            'action' => '请点击下方按钮激活账号',
            'button' => '激活账号',
            'footer' => '如果你没有注册，请忽略此邮件。'
        ],
        'en' => [
            'subject' => 'Activate your sonice.games account',
            'greeting' => 'Welcome to sonice.games! Have fun playing here.',
            'action' => 'Please click the button below to activate your account.',
            'button' => 'Activate Account',
            'footer' => 'If you did not register, please ignore this email.'
        ]
        // 可继续补充更多语言
    ];
    $t = $mail_texts[$lang] ?? $mail_texts['en'];
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'soniceono@gmail.com';
        $mail->Password = 'zxwc bshk zhov pvmy';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('soniceono@gmail.com', 'Sonice Games');
        $mail->addAddress($to, $username);
        $mail->isHTML(true);
        $mail->Subject = $t['subject'];
        $mail->Body = "<h2>{$t['greeting']}</h2><p>{$t['action']}</p><p><a href='$link' style='display:inline-block;padding:10px 24px;background:#2563eb;color:#fff;border-radius:6px;text-decoration:none;font-weight:bold;'>{$t['button']}</a></p><p style='color:#888;font-size:13px;'>$link</p><p>{$t['footer']}</p>";
        $mail->AltBody = $t['greeting'] . "\n" . $t['action'] . "\n$link";
        $mail->send();
    } catch (Exception $e) {
        error_log('Activation mail error: ' . $mail->ErrorInfo);
    }
} 