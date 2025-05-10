<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// 创建 Twig 加载器
$loader = new FilesystemLoader(__DIR__ . '/../templates');

// 创建 Twig 环境
$twig = new Environment($loader, [
    'cache' => __DIR__ . '/../cache',
    'debug' => true,
    'auto_reload' => true
]);

// 添加全局变量
$twig->addGlobal('site_name', 'Sonice.Games');
$twig->addGlobal('base_url', '/sonice-online-games-new');

// 添加自定义过滤器
$twig->addFilter(new \Twig\TwigFilter('price', function ($number) {
    return number_format($number, 2);
}));

// Add debug extension
$twig->addExtension(new \Twig\Extension\DebugExtension());

return $twig; 