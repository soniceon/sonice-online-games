<?php
/**
 * Google Analytics 验证脚本
 * 用于验证GA4代码是否正确安装和配置
 */

// 设置内容类型
header('Content-Type: text/html; charset=utf-8');

// 获取当前时间
$currentTime = date('Y-m-d H:i:s');

// 检查GA4代码是否存在
$baseTwigFile = __DIR__ . '/../templates/layouts/base.twig';
$ga4CodeExists = false;
$ga4Id = '';

if (file_exists($baseTwigFile)) {
    $content = file_get_contents($baseTwigFile);
    if (strpos($content, 'G-C6DQJE930Z') !== false) {
        $ga4CodeExists = true;
        $ga4Id = 'G-C6DQJE930Z';
    }
}

// 检查配置文件
$analyticsConfigFile = __DIR__ . '/../config/analytics.php';
$configExists = file_exists($analyticsConfigFile);

// 检查是否可以通过HTTP访问
$testUrl = 'http://localhost/sonice-online-games-main/public/';
$httpAccessible = false;

try {
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);
    $response = @file_get_contents($testUrl, false, $context);
    $httpAccessible = ($response !== false);
} catch (Exception $e) {
    $httpAccessible = false;
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Analytics 验证 - Sonice Games</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">
                🔍 Google Analytics 验证报告
            </h1>
            
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">📊 验证结果</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">GA4 测量ID</h3>
                                <p class="text-sm text-green-700">G-C6DQJE930Z</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-<?php echo $ga4CodeExists ? 'green' : 'red'; ?>-50 border border-<?php echo $ga4CodeExists ? 'green' : 'red'; ?>-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-<?php echo $ga4CodeExists ? 'green' : 'red'; ?>-400" fill="currentColor" viewBox="0 0 20 20">
                                    <?php if ($ga4CodeExists): ?>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    <?php else: ?>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    <?php endif; ?>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-<?php echo $ga4CodeExists ? 'green' : 'red'; ?>-800">
                                    GA4 代码安装
                                </h3>
                                <p class="text-sm text-<?php echo $ga4CodeExists ? 'green' : 'red'; ?>-700">
                                    <?php echo $ga4CodeExists ? '✅ 已正确安装' : '❌ 未找到GA4代码'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-<?php echo $configExists ? 'green' : 'yellow'; ?>-50 border border-<?php echo $configExists ? 'green' : 'yellow'; ?>-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-<?php echo $configExists ? 'green' : 'yellow'; ?>-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-<?php echo $configExists ? 'green' : 'yellow'; ?>-800">
                                    配置文件
                                </h3>
                                <p class="text-sm text-<?php echo $configExists ? 'green' : 'yellow'; ?>-700">
                                    <?php echo $configExists ? '✅ 配置文件存在' : '⚠️ 配置文件不存在'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-<?php echo $httpAccessible ? 'green' : 'yellow'; ?>-50 border border-<?php echo $httpAccessible ? 'green' : 'yellow'; ?>-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-<?php echo $httpAccessible ? 'green' : 'yellow'; ?>-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-<?php echo $httpAccessible ? 'green' : 'yellow'; ?>-800">
                                    网站可访问性
                                </h3>
                                <p class="text-sm text-<?php echo $httpAccessible ? 'green' : 'yellow'; ?>-700">
                                    <?php echo $httpAccessible ? '✅ 网站可访问' : '⚠️ 无法访问网站'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">📋 详细检查</h2>
                
                <div class="space-y-4">
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h3 class="font-medium text-gray-800">1. GA4 代码检查</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            <?php if ($ga4CodeExists): ?>
                                ✅ 在 <code>templates/layouts/base.twig</code> 中找到GA4代码
                            <?php else: ?>
                                ❌ 未在模板文件中找到GA4代码
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h3 class="font-medium text-gray-800">2. 测量ID验证</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            <?php if ($ga4Id): ?>
                                ✅ 测量ID: <code><?php echo $ga4Id; ?></code>
                            <?php else: ?>
                                ❌ 未找到有效的测量ID
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h3 class="font-medium text-gray-800">3. 配置文件检查</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            <?php if ($configExists): ?>
                                ✅ 配置文件 <code>config/analytics.php</code> 存在
                            <?php else: ?>
                                ⚠️ 配置文件不存在，但这不是必需的
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h3 class="font-medium text-gray-800">4. 网站访问检查</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            <?php if ($httpAccessible): ?>
                                ✅ 网站可以通过HTTP访问
                            <?php else: ?>
                                ⚠️ 无法通过HTTP访问网站，可能需要启动本地服务器
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">🔧 下一步操作</h2>
                
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-blue-600 text-xs font-bold">1</span>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800">验证GA4设置</h3>
                            <p class="text-sm text-gray-600">登录Google Analytics查看实时数据</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-blue-600 text-xs font-bold">2</span>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800">设置自定义维度</h3>
                            <p class="text-sm text-gray-600">在GA4后台创建用户类型和游戏分类维度</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-blue-600 text-xs font-bold">3</span>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800">测试事件跟踪</h3>
                            <p class="text-sm text-gray-600">访问网站并测试游戏播放、搜索等事件</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-blue-600 text-xs font-bold">4</span>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800">监控数据收集</h3>
                            <p class="text-sm text-gray-600">定期检查GA4报告确保数据正常收集</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">验证时间</h3>
                        <p class="text-sm text-blue-700 mt-1">
                            最后验证时间: <?php echo $currentTime; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 测试GA4代码 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-C6DQJE930Z"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-C6DQJE930Z');
        
        // 测试事件
        gtag('event', 'page_view', {
            'page_title': 'Google Analytics 验证页面',
            'page_location': window.location.href
        });
        
        console.log('✅ Google Analytics 代码已加载');
    </script>
</body>
</html> 