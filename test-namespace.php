<?php
// 测试 Twig 命名空间配置
require_once __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

echo "🧪 测试 Twig 命名空间配置...\n\n";

// 使用命名空间配置
$loader = new FilesystemLoader([
    'pages' => __DIR__ . '/templates/pages',
    'layouts' => __DIR__ . '/templates/layouts',
    'components' => __DIR__ . '/templates/components',
    'partials' => __DIR__ . '/templates/partials'
]);

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

echo "\n🎉 命名空间测试完成！\n";
?>
