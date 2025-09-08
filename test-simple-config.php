<?php
// 测试简单的 Twig 配置
require_once __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

echo "🧪 测试简单 Twig 配置...\n\n";

// 使用简单配置
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, [
    'cache' => false,
]);

echo "✅ Twig 初始化成功\n";

// 测试加载模板
$templates = [
    'pages/404.twig',
    'pages/error.twig', 
    'pages/home.twig',
    'layouts/base.twig'
];

foreach ($templates as $template) {
    try {
        $twig->load($template);
        echo "✅ {$template} 加载成功\n";
    } catch (Exception $e) {
        echo "❌ {$template} 加载失败: " . $e->getMessage() . "\n";
    }
}

echo "\n🎉 简单配置测试完成！\n";
?>
