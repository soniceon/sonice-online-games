<?php
/**
 * 游戏页面列表API
 * 
 * 获取已生成的游戏页面列表
 */

// 设置响应头
header('Content-Type: application/json');

// 安全检查 - 可选的API密钥验证
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
$validApiKey = ''; // 设置为空字符串表示不需要API密钥

// 如果设置了API密钥要求，则进行验证
if (!empty($validApiKey) && $apiKey !== $validApiKey) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// 检查请求方法
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed. Use GET.']);
    exit;
}

// 游戏页面目录
$gamesDir = __DIR__ . '/../pages/games/';

// 确保目录存在
if (!is_dir($gamesDir)) {
    // 目录不存在，返回空列表
    echo json_encode([
        'success' => true, 
        'message' => 'Games directory does not exist',
        'files' => [],
        'count' => 0
    ]);
    exit;
}

// 获取目录中的所有HTML文件
$gameFiles = [];
$files = scandir($gamesDir);

foreach ($files as $file) {
    // 过滤掉目录和非HTML文件
    if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'html') {
        // 获取文件属性
        $filePath = $gamesDir . $file;
        $fileSize = filesize($filePath);
        $fileModified = filemtime($filePath);
        
        // 提取游戏ID (假设文件名是game-{id}.html格式)
        $gameId = preg_replace('/^game-|-\w+\.html$/', '', $file);
        
        $gameFiles[] = [
            'filename' => $file,
            'path' => '/pages/games/' . $file,
            'size' => $fileSize,
            'modified' => $fileModified,
            'modified_date' => date('Y-m-d H:i:s', $fileModified),
            'game_id' => $gameId
        ];
    }
}

// 按修改时间排序
usort($gameFiles, function($a, $b) {
    return $b['modified'] - $a['modified']; // 降序排列
});

// 返回成功响应
http_response_code(200);
echo json_encode([
    'success' => true, 
    'message' => 'Files retrieved successfully',
    'files' => $gameFiles,
    'count' => count($gameFiles)
]); 