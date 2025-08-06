<?php
try {
    $host = '127.0.0.1';
    $port = 3306;
    $dbname = 'sonice_online_games';
    $username = 'root';
    $password = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];

    $pdo = new PDO($dsn, $username, $password, $options);
    
    // 测试连接
    $pdo->query('SELECT 1');
    
} catch (PDOException $e) {
    // 记录错误但不显示敏感信息
    error_log('Database connection failed: ' . $e->getMessage());
    
    // 创建数据库和表（如果不存在）
    try {
        $pdo = new PDO("mysql:host=$host;port=$port;charset=$charset", $username, $password, $options);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$dbname`");
        
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
        
    } catch (PDOException $e2) {
        error_log('Failed to create database: ' . $e2->getMessage());
        // 如果数据库连接失败，创建一个模拟的PDO对象
        $pdo = null;
    }
} 