<?php
/**
 * 规范链接修复工具
 * 自动为页面模板添加规范链接标签
 */

require_once __DIR__ . '/../config/database.php';

// 获取当前域名
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$domain = $_SERVER['HTTP_HOST'] ?? 'sonice.online';
$baseUrl = $protocol . '://' . $domain;

// 检查并修复模板文件
function fixTemplateCanonical($templatePath, $baseUrl) {
    if (!file_exists($templatePath)) {
        return "模板文件不存在: $templatePath";
    }
    
    $content = file_get_contents($templatePath);
    
    // 检查是否已有规范链接
    if (strpos($content, 'rel="canonical"') !== false) {
        return "模板已包含规范链接标签";
    }
    
    // 查找</head>标签位置
    $headEndPos = strpos($content, '</head>');
    if ($headEndPos === false) {
        return "无法找到</head>标签";
    }
    
    // 构建规范链接标签
    $canonicalTag = "\n    <link rel=\"canonical\" href=\"{$baseUrl}\" />\n";
    
    // 插入规范链接标签
    $newContent = substr_replace($content, $canonicalTag, $headEndPos, 0);
    
    // 备份原文件
    $backupPath = $templatePath . '.backup.' . date('Y-m-d-H-i-s');
    if (copy($templatePath, $backupPath)) {
        // 写入新内容
        if (file_put_contents($templatePath, $newContent)) {
            return "成功修复规范链接，原文件已备份为: " . basename($backupPath);
        } else {
            return "写入文件失败";
        }
    } else {
        return "创建备份文件失败";
    }
}

// 处理请求
$message = '';
$templatePath = '';

if ($_POST && isset($_POST['template_path'])) {
    $templatePath = $_POST['template_path'];
    $message = fixTemplateCanonical($templatePath, $baseUrl);
}

// 获取可用的模板文件
$templatesDir = __DIR__ . '/../templates';
$templateFiles = [];
if (is_dir($templatesDir)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($templatesDir));
    foreach ($iterator as $file) {
        if ($file->isFile() && pathinfo($file->getPathname(), PATHINFO_EXTENSION) === 'twig') {
            $relativePath = str_replace($templatesDir . '/', '', $file->getPathname());
            $templateFiles[] = $relativePath;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>规范链接修复工具 - Sonice Online Games</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .message { padding: 15px; margin: 15px 0; border-radius: 4px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .template-list { max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f8f9fa; }
        .template-item { padding: 5px; border-bottom: 1px solid #eee; }
        .template-item:hover { background: #e9ecef; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔗 规范链接修复工具</h1>
            <p>自动为页面模板添加规范链接标签，解决重复内容问题</p>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, '成功') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="message info">
            <h3>📋 工具说明</h3>
            <p>此工具将自动为选定的模板文件添加规范链接标签，帮助搜索引擎识别页面的规范URL，避免重复内容问题。</p>
            <p><strong>注意:</strong> 修复前会自动创建备份文件，以防需要恢复。</p>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="template_path">选择要修复的模板文件:</label>
                <select name="template_path" id="template_path" required>
                    <option value="">请选择模板文件...</option>
                    <?php foreach ($templateFiles as $template): ?>
                        <option value="<?php echo htmlspecialchars($templatesDir . '/' . $template); ?>" <?php echo $templatePath === $templatesDir . '/' . $template ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($template); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <button type="submit">🔧 修复规范链接</button>
            </div>
        </form>
        
        <h3>📁 可用的模板文件</h3>
        <div class="template-list">
            <?php if (empty($templateFiles)): ?>
                <p>未找到模板文件</p>
            <?php else: ?>
                <?php foreach ($templateFiles as $template): ?>
                    <div class="template-item">
                        <strong><?php echo htmlspecialchars($template); ?></strong>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="message info">
            <h3>🔧 手动修复指南</h3>
            <p>如果您想手动添加规范链接，请在模板文件的<code>&lt;/head&gt;</code>标签前添加：</p>
            <code>&lt;link rel="canonical" href="https://sonice.online<?php echo $_SERVER['REQUEST_URI'] ?? '/'; ?>" /&gt;</code>
            
            <h3>📝 规范链接的作用</h3>
            <ul>
                <li>告诉搜索引擎哪个URL是页面的规范版本</li>
                <li>避免重复内容问题</li>
                <li>集中页面权重到规范URL</li>
                <li>改善搜索引擎索引效果</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="/" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">返回首页</a>
            <a href="/seo-checker.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;">SEO检查工具</a>
        </div>
    </div>
</body>
</html> 