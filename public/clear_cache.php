<?php
/**
 * 缓存清理脚本
 * 清理各种缓存文件并重新生成必要的文件
 */

// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Sonice Online Games - Cache Cleaner</h1>\n";

// 清理Twig缓存
$twigCacheDir = __DIR__ . '/../cache/';
if (is_dir($twigCacheDir)) {
    $files = glob($twigCacheDir . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✓ Twig cache cleared\n";
} else {
    echo "⚠ Twig cache directory not found\n";
}

// 清理临时文件
$tempFiles = [
    __DIR__ . '/missing_images.txt',
    __DIR__ . '/missing_images_check.txt',
    __DIR__ . '/debug_path.txt'
];

foreach ($tempFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "✓ Deleted: " . basename($file) . "\n";
    }
}

// 检查并创建必要的目录
$directories = [
    __DIR__ . '/../cache/',
    __DIR__ . '/assets/images/games/',
    __DIR__ . '/uploads/',
    __DIR__ . '/../logs/'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✓ Created directory: " . basename($dir) . "\n";
    }
}

// 检查数据库连接
echo "\n<h2>Database Check</h2>\n";
try {
    require_once __DIR__ . '/../config/database.php';
    if ($pdo) {
        $stmt = $pdo->query('SELECT 1');
        echo "✓ Database connection successful\n";
    } else {
        echo "⚠ Database connection failed\n";
    }
} catch (Exception $e) {
    echo "⚠ Database error: " . $e->getMessage() . "\n";
}

// 检查游戏图片
echo "\n<h2>Game Images Check</h2>\n";
$csvFile = __DIR__ . '/../游戏iframe.CSV';
$imagesDir = __DIR__ . '/assets/images/games/';

if (file_exists($csvFile)) {
    $games = [];
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        $header = fgetcsv($handle);
        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== FALSE) {
            if (count($row) < 3) continue;
            $title = $row[0];
            $slug = strtolower(str_replace([' ', "'", ":", "(", ")", "-"], ['-', '', '', '', '', '-'], $title));
            $games[] = $slug;
        }
        fclose($handle);
    }
    
    $missingImages = [];
    foreach ($games as $slug) {
        $imagePath = $imagesDir . $slug . '.webp';
        $imagePathPng = $imagesDir . $slug . '.png';
        $imagePathJpg = $imagesDir . $slug . '.jpg';
        
        if (!file_exists($imagePath) && !file_exists($imagePathPng) && !file_exists($imagePathJpg)) {
            $missingImages[] = $slug;
        }
    }
    
    echo "Total games in CSV: " . count($games) . "\n";
    echo "Missing images: " . count($missingImages) . "\n";
    
    if (count($missingImages) > 0) {
        echo "<p>Run <a href='generate_missing_images.php'>generate_missing_images.php</a> to create placeholder images.</p>\n";
    }
} else {
    echo "⚠ CSV file not found\n";
}

// 检查文件权限
echo "\n<h2>File Permissions Check</h2>\n";
$writableDirs = [
    __DIR__ . '/../cache/',
    __DIR__ . '/uploads/',
    __DIR__ . '/../logs/'
];

foreach ($writableDirs as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "✓ " . basename($dir) . " is writable\n";
        } else {
            echo "⚠ " . basename($dir) . " is not writable\n";
        }
    }
}

echo "\n<h2>Cache Cleanup Complete!</h2>\n";
echo "<p><a href='../'>← Back to Homepage</a></p>\n";
?>

