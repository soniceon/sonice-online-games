<?php
// 简单的sitemap测试页面
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sitemap测试 - Sonice Online Games</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .test-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin: 10px 5px;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌐 Sitemap测试页面</h1>
        
        <div class="test-section info">
            <h3>📋 测试信息</h3>
            <p><strong>服务器地址:</strong> http://localhost:8000</p>
            <p><strong>当前时间:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>PHP版本:</strong> <?php echo PHP_VERSION; ?></p>
        </div>

        <div class="test-section">
            <h3>🔗 可用的Sitemap地址</h3>
            <p><a href="sitemap.xml" target="_blank" class="button">📄 查看Sitemap XML</a></p>
            <p><a href="http://localhost:8000/sitemap.xml" target="_blank" class="button">🌐 完整URL访问</a></p>
        </div>

        <div class="test-section">
            <h3>📊 Sitemap内容预览</h3>
            <?php
            $sitemapFile = 'sitemap.xml';
            if (file_exists($sitemapFile)) {
                $content = file_get_contents($sitemapFile);
                $xml = simplexml_load_string($content);
                
                if ($xml) {
                    echo '<div class="success">';
                    echo '<p>✅ Sitemap XML格式正确</p>';
                    echo '<p><strong>URL数量:</strong> ' . count($xml->url) . '</p>';
                    echo '</div>';
                    
                    echo '<h4>包含的页面:</h4>';
                    echo '<ul>';
                    foreach ($xml->url as $url) {
                        $loc = (string)$url->loc;
                        $priority = (string)$url->priority;
                        echo '<li><a href="' . $loc . '" target="_blank">' . $loc . '</a> (优先级: ' . $priority . ')</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<div class="error">';
                    echo '<p>❌ Sitemap XML格式错误</p>';
                    echo '</div>';
                }
            } else {
                echo '<div class="error">';
                echo '<p>❌ Sitemap文件不存在</p>';
                echo '</div>';
            }
            ?>
        </div>

        <div class="test-section">
            <h3>🎯 Google Search Console提交</h3>
            <p>您可以在Google Search Console中提交以下地址：</p>
            <p><code>http://localhost:8000/sitemap.xml</code></p>
            <p><strong>注意:</strong> 这是本地测试地址，仅用于开发测试。</p>
        </div>

        <div class="test-section info">
            <h3>📝 使用说明</h3>
            <ol>
                <li>点击上方按钮查看sitemap内容</li>
                <li>确认XML格式正确</li>
                <li>在Google Search Console中提交sitemap地址</li>
                <li>等待搜索引擎索引您的页面</li>
            </ol>
        </div>
    </div>
</body>
</html> 