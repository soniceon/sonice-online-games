<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sitemap - Sonice Games</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .url { margin: 10px 0; padding: 10px; background: #f0f0f0; }
        .priority { color: #666; }
    </style>
</head>
<body>
    <h1>🌐 Sonice Games Sitemap</h1>
    
    <h2>📄 XML格式</h2>
    <p><a href="sitemap.xml" target="_blank">查看原始XML文件</a></p>
    
    <h2>📋 页面列表</h2>
    <?php
    $sitemap = 'sitemap.xml';
    if (file_exists($sitemap)) {
        $xml = simplexml_load_file($sitemap);
        if ($xml) {
            foreach ($xml->url as $url) {
                $loc = (string)$url->loc;
                $priority = (string)$url->priority;
                $changefreq = (string)$url->changefreq;
                echo "<div class='url'>";
                echo "<a href='$loc' target='_blank'>$loc</a>";
                echo "<span class='priority'> (优先级: $priority, 更新频率: $changefreq)</span>";
                echo "</div>";
            }
        } else {
            echo "<p>❌ 无法解析XML文件</p>";
        }
    } else {
        echo "<p>❌ Sitemap文件不存在</p>";
    }
    ?>
    
    <h2>🎯 Google Search Console提交地址</h2>
    <p><code>http://localhost:8000/sitemap.xml</code></p>
</body>
</html> 