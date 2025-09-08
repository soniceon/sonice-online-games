<?php
// 测试修复后的数据库连接
echo "🧪 测试数据库连接修复...\n\n";

// 测试数据库加载
$pdo = null;
try {
    require_once __DIR__ . '/config/database.php';
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    echo "✅ 数据库连接成功\n";
} catch (Exception $e) {
    echo "⚠️  数据库连接失败，使用离线模式\n";
    require_once __DIR__ . '/config/database-offline.php';
    echo "✅ 离线模式加载成功\n";
}

// 测试函数是否存在
if (function_exists('safeQuery')) {
    echo "✅ safeQuery 函数存在\n";
} else {
    echo "❌ safeQuery 函数不存在\n";
}

if (function_exists('isDatabaseConnected')) {
    echo "✅ isDatabaseConnected 函数存在\n";
} else {
    echo "❌ isDatabaseConnected 函数不存在\n";
}

// 测试模拟查询
if (function_exists('safeQuery')) {
    $result = safeQuery($pdo, 'SELECT id, username, email, avatar FROM users WHERE id = ?', [1]);
    if ($result) {
        $user = $result->fetch();
        if ($user) {
            echo "✅ 模拟查询成功: " . $user['username'] . "\n";
        } else {
            echo "⚠️  模拟查询返回空结果\n";
        }
    }
}

echo "\n🎉 测试完成！\n";
?>
