<?php
/**
 * 简单的URL修复测试API
 * 用于测试API调用是否正常
 */

// 先记录访问
$log_file = __DIR__ . '/../logs/api_test.log';
if (!is_dir(dirname($log_file))) {
    mkdir(dirname($log_file), 0755, true);
}
file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "测试API被访问\n", FILE_APPEND);

// 启用错误显示
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 设置响应头
header('Content-Type: application/json; charset=utf-8');

$results = [
    'status' => 'success',
    'message' => '测试成功',
    'time' => date('c'),
    'total' => 0,
    'success' => 0,
    'failed' => 0,
    'details' => []
];

// 记录POST数据
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_data = file_get_contents('php://input');
    file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "收到POST数据: " . substr($raw_data, 0, 1000) . "\n", FILE_APPEND);
    
    try {
        // 解析JSON
        $data = json_decode($raw_data, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON解析错误: ' . json_last_error_msg());
        }
        
        // 验证数据
        if (!isset($data['games']) || !is_array($data['games'])) {
            throw new Exception('缺少游戏数据');
        }
        
        // 更新统计
        $results['total'] = count($data['games']);
        
        // 假装处理了游戏数据
        foreach ($data['games'] as $index => $game) {
            if (rand(0, 10) > 1) { // 90%的成功率
                $results['success']++;
                $results['details'][] = [
                    'title' => $game['title'] ?? "Game $index",
                    'status' => 'success',
                    'message' => '测试更新成功'
                ];
            } else {
                $results['failed']++;
                $results['details'][] = [
                    'title' => $game['title'] ?? "Game $index",
                    'status' => 'failed',
                    'message' => '测试更新失败'
                ];
            }
        }
        
    } catch (Exception $e) {
        $results = [
            'status' => 'error',
            'message' => $e->getMessage(),
            'time' => date('c')
        ];
        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "错误: " . $e->getMessage() . "\n", FILE_APPEND);
    }
}

// 输出结果
echo json_encode($results, JSON_UNESCAPED_UNICODE);
file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "返回数据: " . json_encode($results, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND); 