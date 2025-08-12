<?php
/**
 * 生成缺失的游戏图片占位符
 * 这个脚本会为CSV中列出的游戏生成默认的占位符图片
 */

// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 游戏图片目录
$imagesDir = __DIR__ . '/assets/images/games/';

// 确保目录存在
if (!is_dir($imagesDir)) {
    mkdir($imagesDir, 0755, true);
}

// 读取CSV文件
$csvFile = __DIR__ . '/../游戏iframe.CSV';
if (!file_exists($csvFile)) {
    die("CSV file not found: $csvFile\n");
}

$games = [];
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    $header = fgetcsv($handle, 0, ',', '"', '\\');
            while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== FALSE) {
        if (count($row) < 3) continue;
        
        $title = $row[0];
        $slug = strtolower(str_replace([' ', "'", ":", "(", ")", "-"], ['-', '', '', '', '', '-'], $title));
        
        $games[] = [
            'title' => $title,
            'slug' => $slug,
            'iframe_url' => $row[1],
            'categories' => array_slice($row, 2)
        ];
    }
    fclose($handle);
}

echo "Found " . count($games) . " games in CSV\n";

// 生成占位符图片
$missingCount = 0;
$generatedCount = 0;

foreach ($games as $game) {
    $imagePath = $imagesDir . $game['slug'] . '.webp';
    $imagePathPng = $imagesDir . $game['slug'] . '.png';
    $imagePathJpg = $imagesDir . $game['slug'] . '.jpg';
    
    // 检查是否已有图片
    if (file_exists($imagePath) || file_exists($imagePathPng) || file_exists($imagePathJpg)) {
        continue;
    }
    
    $missingCount++;
    
    // 生成占位符图片
    if (generatePlaceholderImage($imagePath, $game['title'])) {
        $generatedCount++;
        echo "Generated placeholder for: {$game['title']}\n";
    } else {
        echo "Failed to generate placeholder for: {$game['title']}\n";
    }
}

echo "\nSummary:\n";
echo "Missing images: $missingCount\n";
echo "Generated placeholders: $generatedCount\n";

// 验证所有图片
echo "\nVerifying all images...\n";
$totalImages = 0;
$validImages = 0;
$invalidImages = 0;

foreach ($games as $game) {
    $totalImages++;
    $imagePath = $imagesDir . $game['slug'] . '.webp';
    $imagePathPng = $imagesDir . $game['slug'] . '.png';
    $imagePathJpg = $imagesDir . $game['slug'] . '.jpg';
    
    if (file_exists($imagePath) || file_exists($imagePathPng) || file_exists($imagePathJpg)) {
        $validImages++;
    } else {
        $invalidImages++;
        echo "Missing image for: {$game['title']} (slug: {$game['slug']})\n";
    }
}

echo "\nFinal Image Statistics:\n";
echo "Total games: $totalImages\n";
echo "Valid images: $validImages\n";
echo "Invalid images: $invalidImages\n";
echo "Coverage: " . round(($validImages / $totalImages) * 100, 2) . "%\n";

if ($invalidImages > 0) {
    echo "\nRecommendations:\n";
    echo "1. Check file permissions on images directory\n";
    echo "2. Ensure GD extension is properly installed\n";
    echo "3. Verify disk space availability\n";
    echo "4. Check for special characters in game titles\n";
}

/**
 * 生成占位符图片
 */
function generatePlaceholderImage($path, $title) {
    // 检查GD扩展是否可用
    if (!extension_loaded('gd')) {
        error_log("GD extension not available for image generation");
        return false;
    }
    
    // 图片尺寸
    $width = 320;
    $height = 180;
    
    // 创建图片
    $image = imagecreatetruecolor($width, $height);
    if (!$image) {
        error_log("Failed to create image resource");
        return false;
    }
    
    // 设置颜色
    $bgColor = imagecolorallocate($image, 34, 34, 34); // 深灰色背景
    $textColor = imagecolorallocate($image, 255, 255, 255); // 白色文字
    $accentColor = imagecolorallocate($image, 59, 130, 246); // 蓝色边框
    $gradientColor = imagecolorallocate($image, 59, 130, 246); // 渐变色
    
    // 填充背景
    imagefill($image, 0, 0, $bgColor);
    
    // 创建渐变背景
    for ($i = 0; $i < $height; $i++) {
        $alpha = 127 - (int)(($i / $height) * 127);
        $gradientColor = imagecolorallocatealpha($image, 59, 130, 246, $alpha);
        imageline($image, 0, $i, $width, $i, $gradientColor);
    }
    
    // 绘制边框
    imagerectangle($image, 0, 0, $width-1, $height-1, $accentColor);
    imagerectangle($image, 1, 1, $width-2, $height-2, $accentColor);
    
    // 添加游戏图标
    $iconSize = 48;
    $iconX = ($width - $iconSize) / 2;
    $iconY = ($height - $iconSize) / 2 - 20;
    
    // 绘制简单的游戏控制器图标
    $controllerColor = imagecolorallocate($image, 59, 130, 246);
    imagefilledellipse($image, $iconX + $iconSize/2, $iconY + $iconSize/2, $iconSize, $iconSize, $controllerColor);
    
    // 添加文字
    $fontSize = 3;
    $text = substr($title, 0, 20); // 限制文字长度
    $textWidth = imagefontwidth($fontSize) * strlen($text);
    $textX = ($width - $textWidth) / 2;
    $textY = $iconY + $iconSize + 10;
    
    imagestring($image, $fontSize, $textX, $textY, $text, $textColor);
    
    // 添加"Coming Soon"文字
    $comingSoon = "Coming Soon";
    $comingSoonWidth = imagefontwidth(2) * strlen($comingSoon);
    $comingSoonX = ($width - $comingSoonWidth) / 2;
    $comingSoonY = $textY + 25;
    
    imagestring($image, 2, $comingSoonX, $comingSoonY, $comingSoon, $textColor);
    
    // 保存为多种格式
    $result = false;
    
    // 尝试保存为WebP格式
    if (function_exists('imagewebp')) {
        $result = imagewebp($image, $path, 90); // 提高质量到90
    }
    
    // 如果WebP失败，尝试PNG
    if (!$result && function_exists('imagepng')) {
        $pngPath = str_replace('.webp', '.png', $path);
        $result = imagepng($image, $pngPath, 9); // PNG压缩级别9
    }
    
    // 如果PNG也失败，尝试JPG
    if (!$result && function_exists('imagejpeg')) {
        $jpgPath = str_replace('.webp', '.jpg', $path);
        $result = imagejpeg($image, $jpgPath, 90); // JPG质量90
    }
    
    imagedestroy($image);
    
    return $result;
} 