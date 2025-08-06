<?php
// 完全独立的sitemap测试文件
// 不依赖任何外部文件

// 设置正确的Content-Type
header('Content-Type: application/xml; charset=utf-8');

// 输出XML声明
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- 测试页面 -->
    <url>
        <loc>https://sonice.online/</loc>
        <lastmod>2024-08-06</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>https://sonice.online/category/action</loc>
        <lastmod>2024-08-06</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>https://sonice.online/category/racing</loc>
        <lastmod>2024-08-06</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
</urlset> 