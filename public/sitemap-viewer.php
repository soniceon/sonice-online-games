<?php
// 直接显示sitemap.xml的内容
$sitemapFile = 'sitemap.xml';

if (file_exists($sitemapFile)) {
    // 设置正确的Content-Type
    header('Content-Type: application/xml; charset=utf-8');
    
    // 直接输出XML内容
    readfile($sitemapFile);
} else {
    // 如果文件不存在，显示错误
    header('Content-Type: text/html; charset=utf-8');
    echo '<h1>❌ Sitemap文件不存在</h1>';
    echo '<p>文件路径: ' . realpath($sitemapFile) . '</p>';
    echo '<p>当前目录: ' . getcwd() . '</p>';
    echo '<p>文件列表:</p>';
    echo '<ul>';
    foreach (scandir('.') as $file) {
        if ($file != '.' && $file != '..') {
            echo '<li>' . $file . '</li>';
        }
    }
    echo '</ul>';
}
?> 