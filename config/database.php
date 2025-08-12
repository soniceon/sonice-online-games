<?php
// 数据库配置
$dbConfig = [
    'host' => '127.0.0.1',
    'port' => 3306,
    'dbname' => 'sonice_online_games',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]
];

// 连接重试函数
function connectDatabase($config, $maxRetries = 3) {
    $retries = 0;
    $lastError = null;
    
    while ($retries < $maxRetries) {
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
            $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            
            // 测试连接
            $pdo->query('SELECT 1');
            return $pdo;
            
        } catch (PDOException $e) {
            $lastError = $e;
            $retries++;
            
            if ($retries < $maxRetries) {
                // 等待一段时间后重试
                usleep(500000); // 0.5秒
                continue;
            }
        }
    }
    
    // 所有重试都失败了
    error_log("Database connection failed after {$maxRetries} attempts: " . $lastError->getMessage());
    return null;
}

// 尝试连接数据库
$pdo = connectDatabase($dbConfig);

// 如果连接失败，尝试创建数据库和表
if (!$pdo) {
    try {
        // 尝试不指定数据库名连接
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};charset={$dbConfig['charset']}";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
        
        // 创建数据库
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['dbname']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$dbConfig['dbname']}`");
        
        // 创建用户表
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `users` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `username` varchar(50) NOT NULL,
                `email` varchar(100) NOT NULL,
                `password` varchar(255) NOT NULL,
                `avatar` varchar(255) DEFAULT NULL,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `username` (`username`),
                UNIQUE KEY `email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // 创建游戏收藏表
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `favorites` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `game_slug` varchar(255) NOT NULL,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `user_game` (`user_id`, `game_slug`),
                FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // 创建最近游戏表
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `recent_games` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `game_slug` varchar(255) NOT NULL,
                `played_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // 创建游戏统计表
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `game_stats` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `game_slug` varchar(255) NOT NULL,
                `plays` int(11) DEFAULT 0,
                `likes` int(11) DEFAULT 0,
                `last_played` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `game_slug` (`game_slug`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        error_log("Database and tables created successfully");
        
    } catch (PDOException $e) {
        error_log('Failed to create database: ' . $e->getMessage());
        // 如果数据库连接失败，创建一个模拟的PDO对象
        $pdo = null;
    }
}

// 数据库连接状态检查函数
function isDatabaseConnected() {
    global $pdo;
    if (!$pdo) return false;
    
    try {
        $pdo->query('SELECT 1');
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// 安全的数据库查询函数
function safeQuery($pdo, $sql, $params = []) {
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log('Database query failed: ' . $e->getMessage());
        return false;
    }
} 