<?php
// 最简单的测试文件
echo "🎮 PHP 测试成功！\n";
echo "========================\n";
echo "当前时间: " . date('Y-m-d H:i:s') . "\n";
echo "PHP版本: " . PHP_VERSION . "\n";
echo "当前目录: " . __DIR__ . "\n";
echo "========================\n";

// 测试CSV文件读取
$csvFile = __DIR__ . '/../游戏iframe.CSV';
if (file_exists($csvFile)) {
    echo "✅ CSV文件存在: " . $csvFile . "\n";
    
    // 读取前几行
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        $header = fgetcsv($handle, 0, ',', '"', '\\');
        echo "✅ CSV头部: " . implode(', ', $header) . "\n";
        
        $count = 0;
        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== FALSE && $count < 3) {
            echo "✅ 游戏 " . ($count + 1) . ": " . $row[0] . "\n";
            $count++;
        }
        fclose($handle);
    }
} else {
    echo "❌ CSV文件不存在: " . $csvFile . "\n";
}

echo "\n🎯 测试完成！\n";
?> 