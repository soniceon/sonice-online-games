<?php
/**
 * SEO检查工具
 * 检查网站的SEO问题并提供修复建议
 */

require_once __DIR__ . '/../config/database.php';

// 检查函数
function checkRedirects() {
    $issues = [];
    
    // 检查.htaccess重定向
    $htaccess = __DIR__ . '/.htaccess';
    if (file_exists($htaccess)) {
        $content = file_get_contents($htaccess);
        if (strpos($content, 'R=302') !== false || strpos($content, 'R=301') !== false) {
            $issues[] = '发现重定向规则，可能导致重复内容问题';
        }
    }
    
    return $issues;
}

function checkCanonicalTags() {
    $issues = [];
    
    // 检查主页
    $homepage = file_get_contents('http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
    if (strpos($homepage, 'rel="canonical"') === false) {
        $issues[] = '主页缺少规范链接标签';
    }
    
    return $issues;
}

function checkSitemap() {
    $issues = [];
    
    // 检查sitemap.xml
    $sitemap = __DIR__ . '/../sitemap.xml';
    if (!file_exists($sitemap)) {
        $issues[] = 'sitemap.xml文件不存在';
    } else {
        $content = file_get_contents($sitemap);
        if (strpos($content, 'sonice.online') === false) {
            $issues[] = 'sitemap.xml中的域名不正确';
        }
    }
    
    // 检查robots.txt
    $robots = __DIR__ . '/robots.txt';
    if (!file_exists($robots)) {
        $issues[] = 'robots.txt文件不存在';
    }
    
    return $issues;
}

function checkMetaTags() {
    $issues = [];
    
    // 检查主页meta标签
    $homepage = file_get_contents('http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
    
    if (strpos($homepage, 'name="description"') === false) {
        $issues[] = '主页缺少meta description';
    }
    
    if (strpos($homepage, 'name="keywords"') === false) {
        $issues[] = '主页缺少meta keywords';
    }
    
    if (strpos($homepage, 'name="robots"') === false) {
        $issues[] = '主页缺少robots meta标签';
    }
    
    return $issues;
}

// 执行检查
$redirectIssues = checkRedirects();
$canonicalIssues = checkCanonicalTags();
$sitemapIssues = checkSitemap();
$metaIssues = checkMetaTags();

$allIssues = array_merge($redirectIssues, $canonicalIssues, $sitemapIssues, $metaIssues);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO检查工具 - Sonice Online Games</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; }
        .issue { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .critical { background: #f8d7da; border-color: #f5c6cb; }
        .warning { background: #fff3cd; border-color: #ffeaa7; }
        .info { background: #d1ecf1; border-color: #bee5eb; }
        .fix { background: #d4edda; border-color: #c3e6cb; margin-top: 10px; padding: 10px; border-radius: 3px; }
        .status { display: inline-block; padding: 5px 10px; border-radius: 3px; color: white; font-weight: bold; }
        .status.ok { background: #28a745; }
        .status.warning { background: #ffc107; color: #212529; }
        .status.error { background: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 SEO检查工具</h1>
            <p>Sonice Online Games - 网站SEO健康检查报告</p>
        </div>
        
        <h2>📊 检查结果概览</h2>
        <div class="issue info">
            <strong>检查时间:</strong> <?php echo date('Y-m-d H:i:s'); ?><br>
            <strong>发现问题:</strong> <?php echo count($allIssues); ?> 个<br>
            <strong>检查项目:</strong> 重定向、规范链接、Sitemap、Meta标签
        </div>
        
        <?php if (empty($allIssues)): ?>
            <div class="issue fix">
                <h3>🎉 恭喜！</h3>
                <p>您的网站SEO配置良好，没有发现明显问题。</p>
            </div>
        <?php else: ?>
            <h2>⚠️ 发现的问题</h2>
            
            <?php foreach ($allIssues as $issue): ?>
                <div class="issue warning">
                    <strong>问题:</strong> <?php echo htmlspecialchars($issue); ?>
                    <div class="fix">
                        <strong>建议修复:</strong>
                        <?php if (strpos($issue, '重定向') !== false): ?>
                            检查.htaccess文件，移除不必要的重定向规则，确保每个页面只有一个URL。
                        <?php elseif (strpos($issue, '规范链接') !== false): ?>
                            在每个页面的head部分添加rel="canonical"标签，指向页面的规范URL。
                        <?php elseif (strpos($issue, 'sitemap') !== false): ?>
                            确保sitemap.xml文件存在且包含正确的URL，更新robots.txt中的sitemap链接。
                        <?php elseif (strpos($issue, 'meta') !== false): ?>
                            添加必要的meta标签，包括description、keywords和robots标签。
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <h2>🔧 修复建议</h2>
        <div class="issue info">
            <h3>1. 重定向问题修复</h3>
            <p>已更新.htaccess文件，移除了强制重定向规则，添加了SEO友好的URL重写规则。</p>
            
            <h3>2. 规范链接修复</h3>
            <p>建议在每个页面模板中添加规范链接标签：</p>
            <code>&lt;link rel="canonical" href="https://sonice.online<?php echo $_SERVER['REQUEST_URI'] ?? '/'; ?>" /&gt;</code>
            
            <h3>3. Sitemap优化</h3>
            <p>已创建动态sitemap生成器，包含所有游戏页面。建议定期更新sitemap。</p>
            
            <h3>4. Meta标签优化</h3>
            <p>确保每个页面都有独特的title、description和keywords标签。</p>
        </div>
        
        <h2>📈 下一步行动</h2>
        <div class="issue fix">
            <ol>
                <li>在Google Search Console中重新提交sitemap</li>
                <li>检查并修复所有页面的规范链接</li>
                <li>优化页面meta标签</li>
                <li>监控索引状态变化</li>
                <li>定期运行此检查工具</li>
            </ol>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="/" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">返回首页</a>
            <a href="/sitemap-generator.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;">查看动态Sitemap</a>
        </div>
    </div>
</body>
</html> 