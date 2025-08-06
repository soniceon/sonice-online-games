<?php
/**
 * 性能优化脚本
 * 包括图片压缩、缓存优化、文件清理等
 */

// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🎮 Sonice Online Games - Performance Optimizer</h1>\n";

// 1. 图片优化
echo "<h2>📸 Image Optimization</h2>\n";
$imagesDir = __DIR__ . '/assets/images/games/';
$optimizedCount = 0;

if (is_dir($imagesDir)) {
    $files = glob($imagesDir . '*.webp');
    foreach ($files as $file) {
        $fileSize = filesize($file);
        $fileSizeKB = round($fileSize / 1024, 2);
        
        // 检查文件大小，如果大于100KB则标记为需要优化
        if ($fileSize > 100 * 1024) {
            echo "⚠️ Large file: " . basename($file) . " ({$fileSizeKB}KB)\n";
            $optimizedCount++;
        } else {
            echo "✅ Optimized: " . basename($file) . " ({$fileSizeKB}KB)\n";
        }
    }
}

echo "Found {$optimizedCount} large images that could be optimized\n";

// 2. 缓存优化
echo "\n<h2>⚡ Cache Optimization</h2>\n";

// 清理旧的缓存文件
$cacheDir = __DIR__ . '/../cache/';
if (is_dir($cacheDir)) {
    $cacheFiles = glob($cacheDir . '*');
    $deletedCount = 0;
    
    foreach ($cacheFiles as $file) {
        if (is_file($file)) {
            $fileAge = time() - filemtime($file);
            // 删除超过7天的缓存文件
            if ($fileAge > 7 * 24 * 60 * 60) {
                unlink($file);
                $deletedCount++;
            }
        }
    }
    
    echo "✅ Deleted {$deletedCount} old cache files\n";
}

// 3. 数据库优化（如果可用）
echo "\n<h2>🗄️ Database Optimization</h2>\n";
try {
    require_once __DIR__ . '/../config/database.php';
    if ($pdo) {
        // 检查数据库连接
        $stmt = $pdo->query('SELECT 1');
        echo "✅ Database connection successful\n";
        
        // 这里可以添加数据库优化查询
        // 例如：OPTIMIZE TABLE, ANALYZE TABLE 等
        
    } else {
        echo "⚠️ Database not available for optimization\n";
    }
} catch (Exception $e) {
    echo "⚠️ Database optimization skipped: " . $e->getMessage() . "\n";
}

// 4. 文件压缩检查
echo "\n<h2>📦 File Compression</h2>\n";

// 检查是否启用了gzip压缩
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_deflate', $modules)) {
        echo "✅ Gzip compression enabled\n";
    } else {
        echo "⚠️ Gzip compression not enabled\n";
    }
} else {
    echo "ℹ️ Cannot check Apache modules (not running on Apache)\n";
}

// 5. 性能建议
echo "\n<h2>💡 Performance Recommendations</h2>\n";
echo "<ul>\n";
echo "<li>✅ Enable Gzip compression for text files</li>\n";
echo "<li>✅ Use CDN for static assets</li>\n";
echo "<li>✅ Implement browser caching headers</li>\n";
echo "<li>✅ Optimize images (WebP format)</li>\n";
echo "<li>✅ Minify CSS and JavaScript files</li>\n";
echo "<li>✅ Use lazy loading for images</li>\n";
echo "<li>✅ Implement critical CSS inlining</li>\n";
echo "</ul>\n";

// 6. 创建.htaccess优化文件
echo "\n<h2>🔧 Creating .htaccess Optimizations</h2>\n";

$htaccessContent = <<<'EOT'
# Performance Optimizations
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/pdf "access plus 1 month"
    ExpiresByType text/javascript "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType application/x-javascript "access plus 1 month"
    ExpiresByType application/x-shockwave-flash "access plus 1 month"
    ExpiresByType image/x-icon "access plus 1 year"
    ExpiresDefault "access plus 2 days"
</IfModule>

# Gzip Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Cache Control
<IfModule mod_headers.c>
    <FilesMatch "\.(css|js|png|jpg|jpeg|gif|webp|ico)$">
        Header set Cache-Control "max-age=31536000, public"
    </FilesMatch>
    <FilesMatch "\.(html|htm|xml|txt)$">
        Header set Cache-Control "max-age=7200, public"
    </FilesMatch>
</IfModule>
EOT;

file_put_contents(__DIR__ . '/.htaccess', $htaccessContent);
echo "✅ Created optimized .htaccess file\n";

echo "\n<h2>🎉 Performance Optimization Complete!</h2>\n";
echo "<p><a href='../'>← Back to Homepage</a></p>\n";
?> 