<?php
// 最终测试
echo "🧪 最终测试...\n\n";

// 测试模板路径
$templatePath = __DIR__ . '/public/templates';
echo "模板路径: {$templatePath}\n";
echo "路径存在: " . (is_dir($templatePath) ? '是' : '否') . "\n";

if (is_dir($templatePath)) {
    $pagesPath = $templatePath . '/pages';
    echo "页面路径: {$pagesPath}\n";
    echo "页面路径存在: " . (is_dir($pagesPath) ? '是' : '否') . "\n";
    
    if (is_dir($pagesPath)) {
        $keyFiles = ['404.twig', 'error.twig', 'home.twig'];
        foreach ($keyFiles as $file) {
            $filePath = $pagesPath . '/' . $file;
            echo "{$file}: " . (file_exists($filePath) ? '存在' : '不存在') . "\n";
        }
    }
}

echo "\n🎉 测试完成！\n";
?>
