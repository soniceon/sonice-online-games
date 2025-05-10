<?php
require_once __DIR__ . '/db.php';
$config = require __DIR__ . '/config.php';
header('Content-Type: application/json');

$email = trim($_POST['email'] ?? '');
if (!$email) {
    echo json_encode(['success' => false, 'msg' => '请输入邮箱']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'msg' => '邮箱格式不正确']);
    exit;
}
// 查找用户
$stmt = $conn->prepare('SELECT id, username FROM users WHERE email=?');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'msg' => '该邮箱未注册']);
    exit;
}
$stmt->bind_result($user_id, $username);
$stmt->fetch();
// 生成重置token
$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', time() + 3600); // 1小时有效
// 保存token到数据库（假设有reset_token, reset_expires字段）
$stmt2 = $conn->prepare('UPDATE users SET reset_token=?, reset_expires=? WHERE id=?');
$stmt2->bind_param('ssi', $token, $expires, $user_id);
$stmt2->execute();
// 发送重置邮件
$reset_link = 'http://' . $_SERVER['HTTP_HOST'] . '/sonice-online-games-new/api/reset-password.php?email=' . urlencode($email) . '&token=' . $token;
$subject = '重置你的Sonice账号密码';
$body = "Hi $username,<br>请点击以下链接重置你的密码：<a href='$reset_link'>$reset_link</a><br>该链接1小时内有效。";
$headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\n";
$headers .= "From: " . $config['from_name'] . " <" . $config['from_email'] . ">\r\n";
$mail_sent = mail($email, $subject, $body, $headers);
echo json_encode(['success' => true, 'msg' => '重置邮件已发送', 'mail_sent' => $mail_sent]); 
// 保存token到数据库（假设有reset_token, reset_expires字段）
$stmt2 = $conn->prepare('UPDATE users SET reset_token=?, reset_expires=? WHERE id=?');
$stmt2->bind_param('ssi', $token, $expires, $user_id);
$stmt2->execute();
// 发送重置邮件
$reset_link = 'http://' . $_SERVER['HTTP_HOST'] . '/sonice-online-games-new/api/reset-password.php?email=' . urlencode($email) . '&token=' . $token;
$subject = '重置你的Sonice账号密码';
$body = "Hi $username,<br>请点击以下链接重置你的密码：<a href='$reset_link'>$reset_link</a><br>该链接1小时内有效。";
$headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\n";
$headers .= "From: " . $config['from_name'] . " <" . $config['from_email'] . ">\r\n";
$mail_sent = mail($email, $subject, $body, $headers);
// 保存token到数据库（假设有reset_token, reset_expires字段）
$stmt2 = $conn->prepare('UPDATE users SET reset_token=?, reset_expires=? WHERE id=?');
$stmt2->bind_param('ssi', $token, $expires, $user_id);
$stmt2->execute();
// 发送重置邮件
$reset_link = 'http://' . $_SERVER['HTTP_HOST'] . '/sonice-online-games-new/api/reset-password.php?email=' . urlencode($email) . '&token=' . $token;
$subject = '重置你的Sonice账号密码';
$body = "Hi $username,<br>请点击以下链接重置你的密码：<a href='$reset_link'>$reset_link</a><br>该链接1小时内有效。";
$headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\n";
$headers .= "From: " . $config['from_name'] . " <" . $config['from_email'] . ">\r\n";
$mail_sent = mail($email, $subject, $body, $headers);
echo json_encode(['success' => true, 'msg' => '重置邮件已发送', 'mail_sent' => $mail_sent]); 
// 保存token到数据库（假设有reset_token, reset_expires字段）
$stmt2 = $conn->prepare('UPDATE users SET reset_token=?, reset_expires=? WHERE id=?');
$stmt2->bind_param('ssi', $token, $expires, $user_id);
$stmt2->execute();
// 发送重置邮件
$reset_link = 'http://' . $_SERVER['HTTP_HOST'] . '/sonice-online-games-new/api/reset-password.php?email=' . urlencode($email) . '&token=' . $token;
$subject = '重置你的Sonice账号密码';
$body = "Hi $username,<br>请点击以下链接重置你的密码：<a href='$reset_link'>$reset_link</a><br>该链接1小时内有效。";
$headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\n";
$headers .= "From: " . $config['from_name'] . " <" . $config['from_email'] . ">\r\n";
$mail_sent = mail($email, $subject, $body, $headers);