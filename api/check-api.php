<?php
/**
 * Simple API Check
 * Used to confirm that PHP API is working properly
 */

// Enable error display
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set response headers
header('Content-Type: application/json; charset=utf-8');

// Check if API directory is writable
$api_writable = is_writable(__DIR__);
$parent_writable = is_writable(dirname(__DIR__));
$pages_dir = dirname(__DIR__) . '/pages';
$games_dir = dirname(__DIR__) . '/pages/games';
$pages_writable = is_dir($pages_dir) && is_writable($pages_dir);
$games_writable = is_dir($games_dir) ? is_writable($games_dir) : $pages_writable;

// Check PHP version
$php_ok = version_compare(PHP_VERSION, '7.0.0', '>=');

// Return check results
echo json_encode([
    'status' => 'ok',
    'time' => date('c'),
    'php_version' => PHP_VERSION,
    'php_ok' => $php_ok,
    'api_writable' => $api_writable,
    'parent_writable' => $parent_writable,
    'pages_dir_exists' => is_dir($pages_dir),
    'games_dir_exists' => is_dir($games_dir),
    'pages_writable' => $pages_writable,
    'games_writable' => $games_writable,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
    'server_name' => $_SERVER['SERVER_NAME'] ?? 'unknown',
    'request_time' => $_SERVER['REQUEST_TIME'] ?? time()
], JSON_UNESCAPED_UNICODE);

// Log access
$log_file = __DIR__ . '/../logs/api_check.log';
$log_dir = dirname($log_file);

if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
}

@file_put_contents($log_file, date('[Y-m-d H:i:s] ') . "API check accessed\n", FILE_APPEND); 