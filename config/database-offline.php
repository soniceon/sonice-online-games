<?php
// 离线数据库配置 - 用于开发环境
// 当没有数据库时，提供模拟数据

// 模拟数据库连接
$pdo = null;

// 模拟用户数据
$mockUsers = [
    [
        'id' => 1,
        'username' => 'demo_user',
        'email' => 'demo@sonice.online',
        'avatar' => 'public/assets/images/user/default-avatar.png'
    ]
];

// 模拟数据库查询函数
if (!function_exists('safeQuery')) {
    function safeQuery($pdo, $sql, $params = []) {
        // 模拟查询结果
        if (strpos($sql, 'SELECT id, username, email, avatar FROM users WHERE id = ?') !== false) {
            $userId = $params[0] ?? 1;
            $user = $GLOBALS['mockUsers'][0] ?? null;
            if ($user && $user['id'] == $userId) {
                return new MockPDOStatement($user);
            }
            return new MockPDOStatement(null);
        }
        return new MockPDOStatement(null);
    }
}

// 模拟 PDO Statement
class MockPDOStatement {
    private $data;
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function fetch() {
        return $this->data;
    }
    
    public function fetchAll() {
        return $this->data ? [$this->data] : [];
    }
    
    public function rowCount() {
        return $this->data ? 1 : 0;
    }
}

// 数据库连接状态检查函数
if (!function_exists('isDatabaseConnected')) {
    function isDatabaseConnected() {
        return false; // 模拟离线状态
    }
}

// 设置全局变量
$GLOBALS['pdo'] = $pdo;
?>
