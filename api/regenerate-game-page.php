<?php
/**
 * 重新生成游戏页面API
 * 通过游戏ID重新生成单个游戏页面
 */

// 设置响应头
header('Content-Type: application/json');

// 只允许POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => '只允许POST请求']);
    exit;
}

// 读取POST数据
$postData = file_get_contents('php://input');
$data = json_decode($postData, true);

// 验证数据
if (!$data || !isset($data['gameId'])) {
    http_response_code(400);
    echo json_encode(['error' => '缺少游戏ID参数']);
    exit;
}

$gameId = $data['gameId'];

try {
    // 加载游戏数据
    $gamesData = loadGamesData();
    
    // 查找指定游戏
    $game = findGameById($gamesData, $gameId);
    if (!$game) {
        http_response_code(404);
        echo json_encode(['error' => "未找到ID为 $gameId 的游戏"]);
        exit;
    }
    
    // 加载模板
    $template = loadTemplate();
    
    // 生成HTML
    $html = generateHTML($template, $game);
    
    // 保存HTML文件
    $filename = "game-$gameId.html";
    $result = saveGamePage($filename, $html);
    
    // 返回成功响应
    echo json_encode([
        'success' => true,
        'message' => "游戏页面重新生成成功",
        'path' => $result['path']
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

/**
 * 加载游戏数据
 * @return array 游戏数据数组
 */
function loadGamesData() {
    $gamesFile = '../data/games.json';
    
    if (!file_exists($gamesFile)) {
        throw new Exception('游戏数据文件不存在');
    }
    
    $gamesJson = file_get_contents($gamesFile);
    $games = json_decode($gamesJson, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('游戏数据解析失败: ' . json_last_error_msg());
    }
    
    return $games;
}

/**
 * 通过ID查找游戏
 * @param array $games 游戏数据数组
 * @param string|int $id 游戏ID
 * @return array|null 找到的游戏数据或null
 */
function findGameById($games, $id) {
    foreach ($games as $game) {
        if (isset($game['id']) && $game['id'] == $id) {
            return $game;
        }
    }
    return null;
}

/**
 * 加载游戏页面模板
 * @return string 模板内容
 */
function loadTemplate() {
    $templateFile = '../game-template.html';
    
    if (!file_exists($templateFile)) {
        throw new Exception('游戏模板文件不存在');
    }
    
    return file_get_contents($templateFile);
}

/**
 * 生成HTML内容
 * @param string $template 模板内容
 * @param array $game 游戏数据
 * @return string 生成的HTML
 */
function generateHTML($template, $game) {
    // 基本信息替换
    $html = $template;
    
    // 替换游戏数据
    $html = str_replace('{{gameId}}', $game['id'], $html);
    $html = str_replace('{{gameTitle}}', $game['title'] ?? '', $html);
    $html = str_replace('{{gameDescription}}', $game['description'] ?? '', $html);
    $html = str_replace('{{gameImage}}', $game['image'] ?? '/images/placeholder.jpg', $html);
    $html = str_replace('{{gameUrl}}', $game['url'] ?? '#', $html);
    $html = str_replace('{{gameProvider}}', $game['provider'] ?? '', $html);
    
    // 替换元标签
    $html = str_replace('{{metaTitle}}', $game['title'] . ' - 在线游戏', $html);
    $html = str_replace('{{metaDescription}}', $game['description'] ?? "畅玩{$game['title']}在线游戏", $html);
    
    $keywords = $game['title'] . ', 在线游戏';
    if (isset($game['categories']) && is_array($game['categories'])) {
        $keywords .= ', ' . implode(', ', $game['categories']);
    }
    $html = str_replace('{{metaKeywords}}', $keywords, $html);
    
    // 替换分类标签
    if (isset($game['categories']) && is_array($game['categories']) && !empty($game['categories'])) {
        $categoriesHtml = '';
        foreach ($game['categories'] as $category) {
            $slug = strtolower(str_replace(' ', '-', $category));
            $categoriesHtml .= "<a href=\"/pages/categories/{$slug}.html\" class=\"category-tag\">{$category}</a>";
        }
        $html = str_replace('{{gameCategories}}', $categoriesHtml, $html);
    } else {
        $html = str_replace('{{gameCategories}}', '', $html);
    }
    
    // 处理游戏框架
    if (isset($game['type']) && $game['type'] === 'iframe' || isset($game['url'])) {
        // 创建一个占位符div替代iframe标签,使用data-url属性存储游戏URL
        $gameUrl = $game['url'] ?? '#';
        $gameFrameHtml = "<div id=\"game-frame\" class=\"game-iframe game-frame-container\" data-url=\"{$gameUrl}\"></div>";
        
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
    gameProxyLoader.loadGame(\"{$gameUrl}\").catch(error => {
        console.error(\"游戏加载失败:\", error);
    });
});
</script>";
        
        $html = str_replace('</body>', $customInitScript . '</body>', $html);
    } else {
        // 非iframe游戏，使用外部链接
        $externalLink = "<div class=\"game-placeholder\">
            <a href=\"{$game['url']}\" target=\"_blank\" class=\"play-external-btn\">在新窗口中打开游戏</a>
        </div>";
        $html = str_replace('{{gameFrame}}', $externalLink, $html);
    }
    
    // 替换时间戳
    $timestamp = date('c');
    $html = str_replace('{{generatedTime}}', $timestamp, $html);
    
    return $html;
}

/**
 * 保存游戏页面
 * @param string $filename 文件名
 * @param string $content 文件内容
 * @return array 保存结果
 */
function saveGamePage($filename, $content) {
    $targetDir = '../pages/games';
    
    // 确保目录存在
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            throw new Exception('无法创建目标目录');
        }
    }
    
    // 完整文件路径
    $filePath = $targetDir . '/' . $filename;
    
    // 保存文件
    if (file_put_contents($filePath, $content) === false) {
        throw new Exception('保存文件失败');
    }
    
    return [
        'success' => true,
        'path' => '/pages/games/' . $filename,
        'fullPath' => $filePath
    ];
} 