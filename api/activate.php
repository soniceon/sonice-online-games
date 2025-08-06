<?php
header('Content-Type: text/html; charset=utf-8');
$code = $_GET['code'] ?? '';
if (!$code) {
    echo '激活码无效。';
    exit;
}
$users = file_exists('users.json') ? json_decode(file_get_contents('users.json'), true) : [];
$found = false;
foreach ($users as &$u) {
    if (isset($u['activation_code']) && $u['activation_code'] === $code) {
        $u['activated'] = true;
        unset($u['activation_code']);
        $found = true;
        break;
    }
}
if ($found) {
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
    echo '账号激活成功！现在可以登录了。';
} else {
    echo '激活码无效或账号已激活。';
} 