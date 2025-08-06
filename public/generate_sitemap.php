<?php
/**
 * 动态生成sitemap.xml
 * 包含所有游戏页面和分类页面
 */

header('Content-Type: application/xml; charset=utf-8');

// 读取游戏数据
$csvFile = __DIR__ . '/../游戏iframe.CSV';
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
            'categories' => array_slice($row, 2)
        ];
    }
    fclose($handle);
}

// 获取当前日期
$currentDate = date('Y-m-d');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// 主页
echo '    <url>' . "\n";
echo '        <loc>https://sonice.games/</loc>' . "\n";
echo '        <lastmod>' . $currentDate . '</lastmod>' . "\n";
echo '        <changefreq>daily</changefreq>' . "\n";
echo '        <priority>1.0</priority>' . "\n";
echo '    </url>' . "\n";

// 分类页面
$categories = [
    'action', 'racing', 'sports', 'shooter', 'cards', 'adventure', 
    'puzzle', 'strategy', 'mining', 'idle', 'clicker', 'simulation',
    'tycoon', 'arcade', 'board', 'multiplayer', 'io', 'platformer',
    'educational', 'music', 'other'
];

foreach ($categories as $category) {
    echo '    <url>' . "\n";
    echo '        <loc>https://sonice.games/category/' . $category . '</loc>' . "\n";
    echo '        <lastmod>' . $currentDate . '</lastmod>' . "\n";
    echo '        <changefreq>weekly</changefreq>' . "\n";
    echo '        <priority>0.8</priority>' . "\n";
    echo '    </url>' . "\n";
}

// 游戏页面
foreach ($games as $game) {
    echo '    <url>' . "\n";
    echo '        <loc>https://sonice.games/game/' . $game['slug'] . '</loc>' . "\n";
    echo '        <lastmod>' . $currentDate . '</lastmod>' . "\n";
    echo '        <changefreq>monthly</changefreq>' . "\n";
    echo '        <priority>0.7</priority>' . "\n";
    echo '    </url>' . "\n";
}

// 静态页面
$staticPages = [
    'about' => 'monthly',
    'contact' => 'monthly', 
    'privacy' => 'monthly',
    'terms' => 'monthly',
    'favorites' => 'weekly',
    'recently-played' => 'weekly',
    'search' => 'daily'
];

foreach ($staticPages as $page => $freq) {
    echo '    <url>' . "\n";
    echo '        <loc>https://sonice.games/' . $page . '</loc>' . "\n";
    echo '        <lastmod>' . $currentDate . '</lastmod>' . "\n";
    echo '        <changefreq>' . $freq . '</changefreq>' . "\n";
    echo '        <priority>0.6</priority>' . "\n";
    echo '    </url>' . "\n";
}

echo '</urlset>';
?> 