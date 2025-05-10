<?php
/**
 * 删除游戏页面API
 * 删除pages/games/目录下指定的游戏页面文件
 */

// 设置响应类型
header('Content-Type: application/json');

// 错误处理
function returnError($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'error' => $message
    ]);
    exit;
}

// CORS处理（如果需要）
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 处理预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// 接受DELETE或POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    returnError('只接受DELETE或POST请求', 405);
}

// 获取请求体（对于POST请求）或URL参数（对于DELETE请求）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestBody = file_get_contents('php://input');
    $data = json_decode($requestBody, true);
    $filename = $data['filename'] ?? null;
} else {
    $filename = $_GET['filename'] ?? null;
}

// 验证文件名
if (!$filename) {
    returnError('缺少文件名参数');
}

// 验证文件名格式
if (!preg_match('/^game-[a-zA-Z0-9_-]+\.html$/', $filename)) {
    returnError('文件名格式无效');
}

// 构建完整文件路径
$targetDir = '../pages/games/';
$filePath = $targetDir . $filename;

// 检查文件是否存在
if (!file_exists($filePath)) {
    returnError('文件不存在', 404);
}

// 尝试删除文件
if (!unlink($filePath)) {
    returnError('删除文件失败', 500);
}

// 返回成功响应
echo json_encode([
    'success' => true,
    'message' => '文件删除成功',
    'filename' => $filename
]);
exit; 