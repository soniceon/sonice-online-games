<?php
/**
 * 动态Sitemap生成器
 * 自动生成包含所有游戏页面的sitemap
 */

header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=3600'); // 缓存1小时

require_once __DIR__ . '/../config/database.php';

// 获取当前域名
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$domain = $_SERVER['HTTP_HOST'] ?? 'sonice.online';
$baseUrl = $protocol . '://' . $domain;

// 基础页面
$basePages = [
    '/' => ['priority' => '1.0', 'changefreq' => 'daily'],
    '/about' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    '/contact' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    '/privacy' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    '/terms' => ['priority' => '0.6', 'changefreq' => 'monthly'],
];

// 分类页面
$categories = [
    'action', 'racing', 'sports', 'shooter', 'cards', 'adventure', 
    'puzzle', 'strategy', 'mining', 'idle', 'clicker', 'simulation', 
    'tycoon', 'arcade', 'board', 'multiplayer', 'io', 'platformer', 
    'educational', 'music', 'other'
];

// 生成sitemap XML
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// 添加基础页面
foreach ($basePages as $path => $settings) {
    echo "  <url>\n";
    echo "    <loc>{$baseUrl}{$path}</loc>\n";
    echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
    echo "    <changefreq>{$settings['changefreq']}</changefreq>\n";
    echo "    <priority>{$settings['priority']}</priority>\n";
    echo "  </url>\n";
}

// 添加分类页面
foreach ($categories as $category) {
    echo "  <url>\n";
    echo "    <loc>{$baseUrl}/category/{$category}</loc>\n";
    echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.8</priority>\n";
    echo "  </url>\n";
}

// 添加游戏页面（如果数据库可用）
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT slug, updated_at FROM games WHERE status = 'active' ORDER BY updated_at DESC");
        if ($stmt) {
            while ($game = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $lastmod = $game['updated_at'] ? date('Y-m-d', strtotime($game['updated_at'])) : date('Y-m-d');
                echo "  <url>\n";
                echo "    <loc>{$baseUrl}/game/{$game['slug']}</loc>\n";
                echo "    <lastmod>{$lastmod}</lastmod>\n";
                echo "    <changefreq>weekly</changefreq>\n";
                echo "    <priority>0.7</priority>\n";
                echo "  </url>\n";
            }
        }
    } catch (Exception $e) {
        // 如果数据库查询失败，记录错误但不中断sitemap生成
        error_log('Sitemap generation error: ' . $e->getMessage());
    }
}

echo '</urlset>';
?> 