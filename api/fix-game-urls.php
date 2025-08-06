<?php
/**
 * 修复游戏URL API
 * 批量更新游戏页面中的URL，使其与CSV中的URL保持一致
 */

// 设置错误处理
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 记录错误到日志
function logError($message) {
    $logDir = '../logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, $logDir . '/api_errors.log');
}

// 设置响应头
header('Content-Type: application/json; charset=utf-8');

// 只允许POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => '只允许POST请求'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 设置无限执行时间，防止超时
set_time_limit(0);

$results = [
    'total' => 0,
    'success' => 0,
    'failed' => 0,
    'details' => []
];

try {
    // 读取POST数据
    $postData = file_get_contents('php://input');
    if (empty($postData)) {
        throw new Exception('POST数据为空');
    }
    
    // 解码JSON数据
    $data = json_decode($postData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON解析错误: ' . json_last_error_msg() . ' - 原始数据: ' . substr($postData, 0, 100));
    }
    
    // 验证数据
    if (!isset($data['games']) || !is_array($data['games'])) {
        throw new Exception('缺少游戏数据');
    }
    
    // 游戏页面目录
    $gamesDir = '../pages/games/';
    if (!file_exists($gamesDir) && !mkdir($gamesDir, 0755, true)) {
        throw new Exception('无法创建游戏页面目录');
    }
    
    // 更新结果总数
    $results['total'] = count($data['games']);
    
    // 处理每个游戏
    foreach ($data['games'] as $index => $game) {
        try {
            if (!isset($game['id']) || !isset($game['title']) || !isset($game['url'])) {
                throw new Exception('游戏数据不完整');
            }
            
            // 文件名
            $gameId = intval($game['id']);
            $filename = 'game-' . $gameId . '.html';
            $filepath = $gamesDir . $filename;
            
            // 检查文件是否存在
            if (!file_exists($filepath)) {
                // 如果页面不存在，尝试重新生成
                $regenerated = regenerateGamePage($game);
                
                if ($regenerated) {
                    $results['success']++;
                    $results['details'][] = [
                        'title' => $game['title'],
                        'status' => 'success',
                        'message' => '已重新生成页面'
                    ];
                } else {
                    throw new Exception('无法生成游戏页面');
                }
                
                continue;
            }
            
            // 读取文件内容
            $content = file_get_contents($filepath);
            if ($content === false) {
                throw new Exception('无法读取文件: ' . $filepath);
            }
            
            // 更新URL
            $updatedContent = updateGameUrl($content, $game['url']);
            
            // 保存文件
            if (file_put_contents($filepath, $updatedContent) === false) {
                throw new Exception('无法保存文件: ' . $filepath);
            }
            
            $results['success']++;
            $results['details'][] = [
                'title' => $game['title'],
                'status' => 'success',
                'message' => 'URL已更新'
            ];
        } catch (Exception $e) {
            $results['failed']++;
            $results['details'][] = [
                'title' => isset($game['title']) ? $game['title'] : 'Unknown',
                'status' => 'failed',
                'message' => $e->getMessage()
            ];
            logError('游戏处理错误: ' . $e->getMessage() . ' 游戏: ' . (isset($game['title']) ? $game['title'] : 'Unknown') . ' (#' . $index . ')');
        }
    }
    
    // 返回结果
    echo json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    
} catch (Exception $e) {
    http_response_code(500);
    logError('API错误: ' . $e->getMessage() . ' - 追踪: ' . $e->getTraceAsString());
    $results['error'] = $e->getMessage();
    echo json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
}
exit;

/**
 * 重新生成游戏页面
 * @param array $game 游戏数据
 * @return bool 是否成功
 */
function regenerateGamePage($game) {
    try {
        // 获取模板
        $templatePath = '../game-template.html';
        if (!file_exists($templatePath)) {
            logError('模板文件不存在: ' . $templatePath);
            return false;
        }
        
        $template = file_get_contents($templatePath);
        if ($template === false) {
            logError('无法读取模板文件');
            return false;
        }
        
        // 生成HTML
        $html = generateHtml($template, $game);
        
        // 保存文件
        $gameId = intval($game['id']);
        $filename = 'game-' . $gameId . '.html';
        $filepath = '../pages/games/' . $filename;
        
        // 确保目录存在
        if (!is_dir('../pages/games/')) {
            if (!mkdir('../pages/games/', 0755, true)) {
                logError('无法创建游戏页面目录');
                return false;
            }
        }
        
        return file_put_contents($filepath, $html) !== false;
    } catch (Exception $e) {
        logError('生成游戏页面错误: ' . $e->getMessage());
        return false;
    }
}

/**
 * 生成HTML内容
 * @param string $template 模板内容
 * @param array $game 游戏数据
 * @return string 生成的HTML
 */
function generateHtml($template, $game) {
    // 替换游戏数据
    $html = $template;
    
    // 基本替换
    $replacements = [
        '{{gameId}}' => intval($game['id']),
        '{{gameTitle}}' => htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8'),
        '{{gameDescription}}' => isset($game['description']) ? htmlspecialchars($game['description'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($game['title'] . ' - 在线游戏', ENT_QUOTES, 'UTF-8'),
        '{{gameImage}}' => isset($game['image']) ? htmlspecialchars($game['image'], ENT_QUOTES, 'UTF-8') : '/images/placeholder.jpg',
        '{{gameUrl}}' => htmlspecialchars($game['url'], ENT_QUOTES, 'UTF-8'),
        '{{gameProvider}}' => isset($game['provider']) ? htmlspecialchars($game['provider'], ENT_QUOTES, 'UTF-8') : '',
        '{{metaTitle}}' => htmlspecialchars($game['title'] . ' - 在线游戏', ENT_QUOTES, 'UTF-8'),
        '{{metaDescription}}' => isset($game['description']) ? htmlspecialchars($game['description'], ENT_QUOTES, 'UTF-8') : htmlspecialchars('畅玩' . $game['title'] . '在线游戏', ENT_QUOTES, 'UTF-8'),
        '{{metaKeywords}}' => htmlspecialchars($game['title'] . ', 在线游戏', ENT_QUOTES, 'UTF-8'),
        '{{gameCategories}}' => '',
        '{{generatedTime}}' => date('c')
    ];
    
    foreach ($replacements as $key => $value) {
        $html = str_replace($key, (string)$value, $html);
    }
    
    // 处理游戏框架
    // 创建占位符div和添加加载脚本
    $gameFrameHtml = '<div id="game-frame" class="game-iframe game-frame-container" data-url="' . htmlspecialchars($game['url'], ENT_QUOTES, 'UTF-8') . '"></div>';
    
    // 替换iframe标签
    $html = preg_replace('/<iframe.*?{{gameFrame}}.*?><\/iframe>/', $gameFrameHtml, $html);
    $html = str_replace('{{gameFrame}}', $gameFrameHtml, $html);
    
    // 添加游戏加载器脚本引用
    $scriptReference = '<script src="/js/game-proxy-loader.js"></script>';
    if (strpos($html, 'game-proxy-loader.js') === false) {
        $html = str_replace('</head>', $scriptReference . '</head>', $html);
    }
    
    // 添加初始化脚本
    $customInitScript = "
<script>
// 初始化游戏加载代理
document.addEventListener('DOMContentLoaded', function() {
    const gameProxyLoader = new GameProxyLoader({
        frameId: 'game-frame',
        fullscreenBtnId: 'fullscreen-btn',
        debug: false
    });
    
    gameProxyLoader.init();
    gameProxyLoader.loadGame(\"" . htmlspecialchars($game['url'], ENT_QUOTES, 'UTF-8') . "\").catch(error => {
        console.error(\"游戏加载失败:\", error);
    });
});
</script>";
    
    $html = str_replace('</body>', $customInitScript . '</body>', $html);
    
    return $html;
}

/**
 * 更新游戏URL
 * @param string $content HTML内容
 * @param string $newUrl 新的URL
 * @return string 更新后的内容
 */
function updateGameUrl($content, $newUrl) {
    // 安全处理URL
    $newUrl = htmlspecialchars($newUrl, ENT_QUOTES, 'UTF-8');
    
    // 更新data-url属性
    $content = preg_replace('/(id="game-frame"[^>]*data-url=")[^"]+(")/i', '$1' . $newUrl . '$2', $content);
    
    // 更新loadGame方法中的URL
    $content = preg_replace('/(gameProxyLoader\.loadGame\(")[^"]+("\))/i', '$1' . $newUrl . '$2', $content);
    
    // 更新iframe src属性
    $content = preg_replace('/(<iframe[^>]*src=")[^"]+(")/i', '$1' . $newUrl . '$2', $content);
    
    // 更新gameUrl变量
    $content = preg_replace('/(const gameUrl = ")[^"]+(";)/i', '$1' . $newUrl . '$2', $content);
    
    return $content;
} 