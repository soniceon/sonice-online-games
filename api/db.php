<?php
$config = require __DIR__ . '/config.php';

$conn = new mysqli(
    $config['db_host'],
    $config['db_user'],
    $config['db_pass'],
    $config['db_name']
);

if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}
?> 