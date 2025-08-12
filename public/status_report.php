<?php
/**
 * 项目状态报告脚本
 * 显示游戏数量、图片覆盖率、系统状态等关键指标
 */

// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 包含数据库配置
require_once __DIR__ . '/../config/database.php';

echo "<!DOCTYPE html>\n";
echo "<html lang='zh-CN'>\n";
echo "<head>\n";
echo "    <meta charset='UTF-8'>\n";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "    <title>Sonice Online Games - 状态报告</title>\n";
echo "    <style>\n";
echo "        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; padding: 20px; background: #f5f5f7; }\n";
echo "        .container { max-width: 1200px; margin: 0 auto; }\n";
echo "        .header { text-align: center; margin-bottom: 30px; }\n";
echo "        .header h1 { color: #007AFF; margin-bottom: 10px; }\n";
echo "        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px; }\n";
echo "        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }\n";
echo "        .stat-card h3 { color: #333; margin-bottom: 15px; border-bottom: 2px solid #007AFF; padding-bottom: 10px; }\n";
echo "        .stat-item { display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #eee; }\n";
echo "        .stat-label { font-weight: 500; color: #666; }\n";
echo "        .stat-value { font-weight: bold; color: #333; }\n";
echo "        .status-ok { color: #28a745; }\n";
echo "        .status-warning { color: #ffc107; }\n";
echo "        .status-error { color: #dc3545; }\n";
echo "        .progress-bar { width: 100%; height: 20px; background: #e9ecef; border-radius: 10px; overflow: hidden; margin: 10px 0; }\n";
echo "        .progress-fill { height: 100%; background: linear-gradient(90deg, #28a745, #20c997); transition: width 0.3s; }\n";
echo "        .actions { text-align: center; margin-top: 30px; }\n";
echo "        .btn { display: inline-block; padding: 12px 24px; margin: 0 10px; background: #007AFF; color: white; text-decoration: none; border-radius: 8px; transition: background 0.3s; }\n";
echo "        .btn:hover { background: #0056CC; }\n";
echo "        .btn-success { background: #28a745; }\n";
echo "        .btn-success:hover { background: #218838; }\n";
echo "        .btn-warning { background: #ffc107; color: #333; }\n";
echo "        .btn-warning:hover { background: #e0a800; }\n";
echo "    </style>\n";
echo "</head>\n";
echo "<body>\n";
echo "    <div class='container'>\n";
echo "        <div class='header'>\n";
echo "            <h1>🎮 Sonice Online Games - 项目状态报告</h1>\n";
echo "            <p>生成时间: " . date('Y-m-d H:i:s') . "</p>\n";
echo "        </div>\n";

// 1. 系统状态
echo "        <div class='stats-grid'>\n";
echo "            <div class='stat-card'>\n";
echo "                <h3>🖥️ 系统状态</h3>\n";
echo "                <div class='stat-item'>\n";
echo "                    <span class='stat-label'>PHP版本</span>\n";
echo "                    <span class='stat-value'>" . PHP_VERSION . "</span>\n";
echo "                </div>\n";
echo "                <div class='stat-item'>\n";
echo "                    <span class='stat-label'>操作系统</span>\n";
echo "                    <span class='stat-value'>" . PHP_OS . "</span>\n";
echo "                </div>\n";
echo "                <div class='stat-item'>\n";
echo "                    <span class='stat-label'>内存限制</span>\n";
echo "                    <span class='stat-value'>" . ini_get('memory_limit') . "</span>\n";
echo "                </div>\n";
echo "                <div class='stat-item'>\n";
echo "                    <span class='stat-label'>最大执行时间</span>\n";
echo "                    <span class='stat-value'>" . ini_get('max_execution_time') . "秒</span>\n";
echo "                </div>\n";
echo "            </div>\n";

// 2. 扩展状态
echo "            <div class='stat-card'>\n";
echo "                <h3>🔧 PHP扩展</h3>\n";
$requiredExtensions = ['pdo', 'pdo_mysql', 'gd', 'json', 'mbstring', 'fileinfo'];
foreach ($requiredExtensions as $ext) {
    $status = extension_loaded($ext) ? 'status-ok' : 'status-error';
    $icon = extension_loaded($ext) ? '✅' : '❌';
    echo "                <div class='stat-item'>\n";
    echo "                    <span class='stat-label'>{$icon} {$ext}</span>\n";
    echo "                    <span class='stat-value {$status}'>" . (extension_loaded($ext) ? '已安装' : '未安装') . "</span>\n";
    echo "                </div>\n";
}
echo "            </div>\n";

// 3. 数据库状态
echo "            <div class='stat-card'>\n";
echo "                <h3>🗄️ 数据库状态</h3>\n";
if (isDatabaseConnected()) {
    echo "                <div class='stat-item'>\n";
    echo "                    <span class='stat-label'>连接状态</span>\n";
    echo "                    <span class='stat-value status-ok'>正常</span>\n";
    echo "                </div>\n";
    
    // 检查表结构
    try {
        $tables = ['users', 'favorites', 'recent_games', 'game_stats'];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
            $status = $stmt->rowCount() > 0 ? 'status-ok' : 'status-warning';
            $icon = $stmt->rowCount() > 0 ? '✅' : '⚠️';
            echo "                <div class='stat-item'>\n";
            echo "                    <span class='stat-label'>{$icon} 表 {$table}</span>\n";
            echo "                    <span class='stat-value {$status}'>" . ($stmt->rowCount() > 0 ? '存在' : '不存在') . "</span>\n";
            echo "                </div>\n";
        }
    } catch (Exception $e) {
        echo "                <div class='stat-item'>\n";
        echo "                    <span class='stat-label'>表结构检查</span>\n";
        echo "                    <span class='stat-value status-error'>失败</span>\n";
        echo "                </div>\n";
    }
} else {
    echo "                <div class='stat-item'>\n";
    echo "                    <span class='stat-label'>连接状态</span>\n";
    echo "                    <span class='stat-value status-error'>失败</span>\n";
    echo "                </div>\n";
}
echo "            </div>\n";

// 4. 游戏数据状态
echo "            <div class='stat-card'>\n";
echo "                <h3>🎮 游戏数据</h3>\n";
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
    
    echo "                <div class='stat-item'>\n";
    echo "                    <span class='stat-label'>CSV文件</span>\n";
    echo "                    <span class='stat-value status-ok'>存在</span>\n";
    echo "                </div>\n";
    echo "                <div class='stat-item'>\n";
    echo "                    <span class='stat-label'>游戏总数</span>\n";
    echo "                    <span class='stat-value'>" . count($games) . "</span>\n";
    echo "                </div>\n";
    
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
    $coverageStatus = $coverage >= 90 ? 'status-ok' : ($coverage >= 70 ? 'status-warning' : 'status-error');
    
    echo "                <div class='stat-item'>\n";
    echo "                    <span class='stat-label'>图片覆盖率</span>\n";
    echo "                    <span class='stat-value {$coverageStatus}'>{$coverage}%</span>\n";
    echo "                </div>\n";
    echo "                <div class='stat-item'>\n";
    echo "                    <span class='stat-label'>有效图片</span>\n";
    echo "                    <span class='stat-value'>{$validImages}/{$totalImages}</span>\n";
    echo "                </div>\n";
    
    // 进度条
    echo "                <div class='progress-bar'>\n";
    echo "                    <div class='progress-fill' style='width: {$coverage}%'></div>\n";
    echo "                </div>\n";
    
} else {
    echo "                <div class='stat-item'>\n";
    echo "                    <span class='stat-label'>CSV文件</span>\n";
    echo "                    <span class='stat-value status-error'>不存在</span>\n";
    echo "                </div>\n";
}
echo "            </div>\n";

// 5. 文件权限状态
echo "            <div class='stat-card'>\n";
echo "                <h3>📁 文件权限</h3>\n";
$directories = [
    'cache' => '../cache',
    'uploads' => 'uploads',
    'assets/images/games' => 'assets/images/games',
    'logs' => '../logs'
];

foreach ($directories as $name => $path) {
    $fullPath = __DIR__ . '/' . $path;
    if (is_dir($fullPath)) {
        $status = is_writable($fullPath) ? 'status-ok' : 'status-error';
        $icon = is_writable($fullPath) ? '✅' : '❌';
        echo "                <div class='stat-item'>\n";
        echo "                    <span class='stat-label'>{$icon} {$name}</span>\n";
        echo "                    <span class='stat-value {$status}'>" . (is_writable($fullPath) ? '可写' : '不可写') . "</span>\n";
        echo "                </div>\n";
    } else {
        echo "                <div class='stat-item'>\n";
        echo "                    <span class='stat-label'>⚠️ {$name}</span>\n";
        echo "                    <span class='stat-value status-warning'>不存在</span>\n";
        echo "                </div>\n";
    }
}
echo "            </div>\n";

// 6. 性能状态
echo "            <div class='stat-card'>\n";
echo "                <h3>⚡ 性能指标</h3>\n";
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

echo "                <div class='stat-item'>\n";
echo "                    <span class='stat-label'>执行时间</span>\n";
echo "                    <span class='stat-value'>" . round($executionTime, 2) . "ms</span>\n";
echo "                </div>\n";
echo "                <div class='stat-item'>\n";
echo "                    <span class='stat-label'>内存使用</span>\n";
echo "                    <span class='stat-value'>" . round($memoryUsed / 1024, 2) . "KB</span>\n";
echo "                </div>\n";
echo "                <div class='stat-item'>\n";
echo "                    <span class='stat-label'>磁盘空间</span>\n";
echo "                    <span class='stat-value'>" . round(disk_free_space(__DIR__) / 1024 / 1024, 2) . "MB</span>\n";
echo "                </div>\n";
echo "            </div>\n";

echo "        </div>\n";

// 操作按钮
echo "        <div class='actions'>\n";
echo "            <a href='system_check.php' class='btn'>🔍 详细系统检查</a>\n";
echo "            <a href='generate_missing_images.php' class='btn btn-success'>🖼️ 生成缺失图片</a>\n";
echo "            <a href='clear_cache.php' class='btn btn-warning'>🧹 清理缓存</a>\n";
echo "            <a href='../install.php' class='btn'>⚙️ 安装配置</a>\n";
echo "            <a href='../' class='btn'>🏠 返回首页</a>\n";
echo "        </div>\n";

echo "    </div>\n";
echo "</body>\n";
echo "</html>\n";
?> 