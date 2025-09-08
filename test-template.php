<?php
// 测试模板路径
require_once __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

echo "🧪 测试模板路径...\n\n";

// 测试不同的路径配置
$paths = [
    __DIR__ . '/templates',
    __DIR__ . '/templates/pages',
    __DIR__ . '/public/../templates',
    __DIR__ . '/public/../templates/pages'
];

foreach ($paths as $path) {
    echo "测试路径: $path\n";
    if (is_dir($path)) {
        echo "✅ 目录存在\n";
        if (file_exists($path . '/404.twig')) {
            echo "✅ 404.twig 存在\n";
        } else {
            echo "❌ 404.twig 不存在\n";
        }
        if (file_exists($path . '/error.twig')) {
            echo "✅ error.twig 存在\n";
        } else {
            echo "❌ error.twig 不存在\n";
        }
    } else {
        echo "❌ 目录不存在\n";
    }
    echo "---\n";
}

// 测试 Twig 加载
try {
    $loader = new FilesystemLoader([
        __DIR__ . '/templates/pages'
    ]);
    $twig = new Environment($loader, [
        'cache' => false,
    ]);
    
    echo "✅ Twig 初始化成功\n";
    
    // 测试加载模板
    $template = $twig->load('404.twig');
    echo "✅ 404.twig 模板加载成功\n";
    
} catch (Exception $e) {
    echo "❌ Twig 错误: " . $e->getMessage() . "\n";
}

echo "\n🎉 测试完成！\n";
?>
