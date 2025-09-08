<?php
/**
 * 开发服务器启动脚本
 * 用于本地测试 PHP 应用程序
 */

echo "🚀 启动 Sonice Online Games 开发服务器...\n";
echo "📁 项目目录: " . __DIR__ . "\n";
echo "🌐 服务器地址: http://localhost:8000\n";
echo "📱 移动端测试: http://192.168.1.xxx:8000 (替换为你的IP)\n";
echo "⏹️  按 Ctrl+C 停止服务器\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// 检查 PHP 版本
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    echo "❌ 错误: 需要 PHP 7.4 或更高版本\n";
    echo "当前版本: " . PHP_VERSION . "\n";
    exit(1);
}

// 检查必要的扩展
$required_extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

if (!empty($missing_extensions)) {
    echo "⚠️  警告: 缺少以下 PHP 扩展: " . implode(', ', $missing_extensions) . "\n";
    echo "某些功能可能无法正常工作\n\n";
}

// 启动内置服务器
$host = 'localhost';
$port = 8000;
$docroot = __DIR__ . '/public';

echo "✅ PHP 版本: " . PHP_VERSION . "\n";
echo "✅ 文档根目录: $docroot\n";
echo "✅ 服务器地址: http://$host:$port\n\n";

echo "🎮 测试链接:\n";
echo "   • 主页: http://$host:$port/\n";
echo "   • 测试页: http://$host:$port/test.html\n";
echo "   • 站点地图: http://$host:$port/sitemap.xml\n";
echo "   • 机器人规则: http://$host:$port/robots.txt\n\n";

echo "🔄 启动服务器...\n";
echo "=" . str_repeat("=", 50) . "\n";

// 启动服务器
$command = "php -S $host:$port -t \"$docroot\"";
echo "执行命令: $command\n\n";

passthru($command);
?>
