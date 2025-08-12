<?php
/**
 * 系统健康检查脚本
 * 检查PHP环境、扩展、文件权限、数据库连接等
 */

// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Sonice Online Games - 系统健康检查</h1>\n";
echo "<style>body{font-family:monospace;margin:20px;background:#f5f5f5} .status{display:inline-block;width:20px;height:20px;border-radius:50%;margin-right:10px} .ok{background:#4CAF50} .error{background:#f44336} .warning{background:#ff9800} .info{background:#2196F3}</style>\n";

// 1. PHP环境检查
echo "<h2>📋 PHP环境检查</h2>\n";
echo "<div><span class='status ok'></span>PHP版本: " . PHP_VERSION . "</div>\n";
echo "<div><span class='status ok'></span>操作系统: " . PHP_OS . "</div>\n";
echo "<div><span class='status ok'></span>内存限制: " . ini_get('memory_limit') . "</div>\n";
echo "<div><span class='status ok'></span>最大执行时间: " . ini_get('max_execution_time') . "秒</div>\n";

// 2. 必要扩展检查
echo "<h2>🔧 PHP扩展检查</h2>\n";
$requiredExtensions = ['pdo', 'pdo_mysql', 'gd', 'json', 'mbstring', 'fileinfo'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<div><span class='status ok'></span>{$ext}: 已安装</div>\n";
    } else {
        echo "<div><span class='status error'></span>{$ext}: 未安装</div>\n";
    }
}

// 3. 文件权限检查
echo "<h2>📁 文件权限检查</h2>\n";
$directories = [
    'cache' => '../cache',
    'uploads' => 'uploads',
    'assets/images/games' => 'assets/images/games',
    'logs' => '../logs'
];

foreach ($directories as $name => $path) {
    $fullPath = __DIR__ . '/' . $path;
    if (is_dir($fullPath)) {
        if (is_writable($fullPath)) {
            echo "<div><span class='status ok'></span>{$name}: 可写</div>\n";
        } else {
            echo "<div><span class='status error'></span>{$name}: 不可写</div>\n";
        }
    } else {
        if (mkdir($fullPath, 0755, true)) {
            echo "<div><span class='status info'></span>{$name}: 已创建</div>\n";
        } else {
            echo "<div><span class='status error'></span>{$name}: 创建失败</div>\n";
        }
    }
}

// 4. 数据库连接检查
echo "<h2>🗄️ 数据库连接检查</h2>\n";
require_once __DIR__ . '/../config/database.php';

if (isDatabaseConnected()) {
    echo "<div><span class='status ok'></span>数据库连接: 正常</div>\n";
    
    // 检查表结构
    try {
        $tables = ['users', 'favorites', 'recent_games', 'game_stats'];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
            if ($stmt->rowCount() > 0) {
                echo "<div><span class='status ok'></span>表 {$table}: 存在</div>\n";
            } else {
                echo "<div><span class='status warning'></span>表 {$table}: 不存在</div>\n";
            }
        }
    } catch (Exception $e) {
        echo "<div><span class='status error'></span>表结构检查失败: " . htmlspecialchars($e->getMessage()) . "</div>\n";
    }
} else {
    echo "<div><span class='status error'></span>数据库连接: 失败</div>\n";
}

// 5. 游戏数据检查
echo "<h2>🎮 游戏数据检查</h2>\n";
$csvFile = __DIR__ . '/../游戏iframe.CSV';
if (file_exists($csvFile)) {
    $games = [];
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        $header = fgetcsv($handle, 0, ',', '"', '\\');
        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== FALSE) {
            if (count($row) >= 3) {
                $games[] = $row[0];
            }
        }
        fclose($handle);
    }
    
    echo "<div><span class='status ok'></span>CSV文件: 存在 (" . count($games) . " 个游戏)</div>\n";
    
    // 检查图片覆盖率
    $imagesDir = __DIR__ . '/assets/images/games/';
    $totalImages = 0;
    $validImages = 0;
    
    foreach ($games as $gameTitle) {
        $slug = strtolower(str_replace([' ', "'", ":", "(", ")", "-"], ['-', '', '', '', '', '-'], $gameTitle));
        $totalImages++;
        
        $imagePath = $imagesDir . $slug . '.webp';
        $imagePathPng = $imagesDir . $slug . '.png';
        $imagePathJpg = $imagesDir . $slug . '.jpg';
        
        if (file_exists($imagePath) || file_exists($imagePathPng) || file_exists($imagePathJpg)) {
            $validImages++;
        }
    }
    
    $coverage = $totalImages > 0 ? round(($validImages / $totalImages) * 100, 2) : 0;
    if ($coverage >= 90) {
        echo "<div><span class='status ok'></span>图片覆盖率: {$coverage}% ({$validImages}/{$totalImages})</div>\n";
    } elseif ($coverage >= 70) {
        echo "<div><span class='status warning'></span>图片覆盖率: {$coverage}% ({$validImages}/{$totalImages})</div>\n";
    } else {
        echo "<div><span class='status error'></span>图片覆盖率: {$coverage}% ({$validImages}/{$totalImages})</div>\n";
    }
} else {
    echo "<div><span class='status error'></span>CSV文件: 不存在</div>\n";
}

// 6. 性能检查
echo "<h2>⚡ 性能检查</h2>\n";
$startTime = microtime(true);
$memoryStart = memory_get_usage();

// 模拟一些操作
for ($i = 0; $i < 1000; $i++) {
    $test = "test" . $i;
}

$endTime = microtime(true);
$memoryEnd = memory_get_usage();
$executionTime = ($endTime - $startTime) * 1000;
$memoryUsed = $memoryEnd - $memoryStart;

echo "<div><span class='status info'></span>执行时间: " . round($executionTime, 2) . "ms</div>\n";
echo "<div><span class='status info'></span>内存使用: " . round($memoryUsed / 1024, 2) . "KB</div>\n";

// 7. 建议
echo "<h2>💡 系统建议</h2>\n";
echo "<div><span class='status info'></span>如果发现问题，请运行以下脚本:</div>\n";
echo "<div><span class='status info'></span>1. <a href='generate_missing_images.php'>生成缺失图片</a></div>\n";
echo "<div><span class='status info'></span>2. <a href='clear_cache.php'>清理缓存</a></div>\n";
echo "<div><span class='status info'></span>3. <a href='../install.php'>重新安装</a></div>\n";

echo "<h2>✅ 检查完成</h2>\n";
echo "<p>系统健康检查已完成。如果发现任何问题，请根据上述建议进行修复。</p>\n";
?> 