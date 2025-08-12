<?php
/**
 * Sonice Online Games 安装脚本
 * 自动设置项目环境
 */

// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Sonice Online Games - Installer</title>
    <style>
        body { font-family: Arial, sans-serif; background: #1a1a1a; color: #fff; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #2a2a2a; padding: 30px; border-radius: 10px; }
        .step { margin-bottom: 30px; padding: 20px; background: #333; border-radius: 8px; }
        .success { color: #4ade80; }
        .error { color: #f87171; }
        .warning { color: #fbbf24; }
        .btn { display: inline-block; padding: 10px 20px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
        .btn:hover { background: #2563eb; }
        pre { background: #1a1a1a; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🎮 Sonice Online Games - Installer</h1>";

// 检查PHP版本
echo "<div class='step'>";
echo "<h2>Step 1: System Requirements</h2>";
$phpVersion = phpversion();
if (version_compare($phpVersion, '7.4.0', '>=')) {
    echo "<p class='success'>✓ PHP version: $phpVersion (OK)</p>";
} else {
    echo "<p class='error'>✗ PHP version: $phpVersion (Requires 7.4+)</p>";
}

// 检查必要的PHP扩展
$requiredExtensions = ['pdo', 'pdo_mysql', 'gd', 'json', 'mbstring'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='success'>✓ $ext extension loaded</p>";
    } else {
        echo "<p class='error'>✗ $ext extension not loaded</p>";
    }
}
echo "</div>";

// 创建必要的目录
echo "<div class='step'>";
echo "<h2>Step 2: Directory Setup</h2>";
$directories = [
    'cache',
    'logs',
    'public/uploads',
    'public/assets/images/games'
];

foreach ($directories as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    if (!is_dir($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "<p class='success'>✓ Created directory: $dir</p>";
        } else {
            echo "<p class='error'>✗ Failed to create directory: $dir</p>";
        }
    } else {
        echo "<p class='success'>✓ Directory exists: $dir</p>";
    }
}
echo "</div>";

// 检查文件权限
echo "<div class='step'>";
echo "<h2>Step 3: File Permissions</h2>";
$writableDirs = ['cache', 'logs', 'public/uploads'];
foreach ($writableDirs as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    if (is_writable($fullPath)) {
        echo "<p class='success'>✓ $dir is writable</p>";
    } else {
        echo "<p class='error'>✗ $dir is not writable</p>";
        echo "<p class='warning'>Run: chmod 755 $dir</p>";
    }
}
echo "</div>";

// 数据库设置
echo "<div class='step'>";
echo "<h2>Step 4: Database Setup</h2>";
try {
    require_once __DIR__ . '/config/database.php';
    if ($pdo) {
        echo "<p class='success'>✓ Database connection successful</p>";
        
        // 检查表是否存在
        $tables = ['users', 'favorites', 'recent_games'];
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                if ($stmt->rowCount() > 0) {
                    echo "<p class='success'>✓ Table '$table' exists</p>";
                } else {
                    echo "<p class='warning'>⚠ Table '$table' not found (will be created automatically)</p>";
                }
            } catch (Exception $e) {
                echo "<p class='error'>✗ Error checking table '$table': " . $e->getMessage() . "</p>";
            }
        }
    } else {
        echo "<p class='error'>✗ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Database error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 检查游戏数据
echo "<div class='step'>";
echo "<h2>Step 5: Game Data</h2>";
$csvFile = __DIR__ . '/游戏iframe.CSV';
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
    echo "<p class='success'>✓ Found " . count($games) . " games in CSV</p>";
    
    // 检查游戏图片
    $imagesDir = __DIR__ . '/public/assets/images/games/';
    $missingImages = 0;
    $totalImages = 0;
    
    if (is_dir($imagesDir)) {
        $files = glob($imagesDir . '*.webp');
        $totalImages = count($files);
        
        foreach ($games as $game) {
            $slug = strtolower(str_replace([' ', "'", ":", "(", ")", "-"], ['-', '', '', '', '', '-'], $game));
            $imagePath = $imagesDir . $slug . '.webp';
            if (!file_exists($imagePath)) {
                $missingImages++;
            }
        }
    }
    
    echo "<p class='success'>✓ Found $totalImages game images</p>";
    if ($missingImages > 0) {
        echo "<p class='warning'>⚠ $missingImages games missing images</p>";
        echo "<p><a href='public/generate_missing_images.php' class='btn'>Generate Missing Images</a></p>";
    }
} else {
    echo "<p class='error'>✗ CSV file not found</p>";
}
echo "</div>";

// Composer依赖
echo "<div class='step'>";
echo "<h2>Step 6: Dependencies</h2>";
$vendorDir = __DIR__ . '/vendor';
if (is_dir($vendorDir)) {
    echo "<p class='success'>✓ Composer dependencies installed</p>";
} else {
    echo "<p class='warning'>⚠ Composer dependencies not found</p>";
    echo "<p>Run: composer install</p>";
}
echo "</div>";

// 完成安装
echo "<div class='step'>";
echo "<h2>Installation Complete!</h2>";
echo "<p>Your Sonice Online Games platform is ready to use.</p>";
echo "<p><a href='public/' class='btn'>Go to Website</a></p>";
echo "<p><a href='public/clear_cache.php' class='btn'>Clear Cache</a></p>";
echo "</div>";

echo "</div></body></html>";
?> 