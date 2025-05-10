<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

// 获取 Twig 实例
$twig = require_once dirname(__DIR__) . '/config/twig.php';

// 示例数据
$data = [
    'site_name' => 'Sonice.Games',
    'base_url' => '/sonice-online-games-new',
    'user' => null, // 未登录状态
    'favorites_count' => 0,
    'new_games_count' => 12,
    'categories' => [
        ['name' => 'Action', 'icon' => 'gamepad', 'count' => 42],
        ['name' => 'Puzzle', 'icon' => 'puzzle-piece', 'count' => 35],
        ['name' => 'Racing', 'icon' => 'car', 'count' => 28],
        ['name' => 'Sports', 'icon' => 'futbol', 'count' => 35],
        ['name' => 'Shooter', 'icon' => 'crosshairs', 'count' => 24],
        ['name' => 'Cards', 'icon' => 'cards', 'count' => 18],
        ['name' => 'Strategy', 'icon' => 'chess', 'count' => 15],
        ['name' => 'Educational', 'icon' => 'graduation-cap', 'count' => 9]
    ],
    'featured_games' => [
        [
            'title' => 'Super Adventure',
            'description' => 'An exciting adventure game with amazing graphics',
            'category' => 'Action',
            'plays' => '1.2K'
        ],
        [
            'title' => 'Puzzle Master',
            'description' => 'Challenge your mind with this addictive puzzle game',
            'category' => 'Puzzle',
            'plays' => '3.4K'
        ],
        [
            'title' => 'Speed Racing',
            'description' => 'Feel the adrenaline in this racing game',
            'category' => 'Racing',
            'plays' => '2.8K'
        ]
    ]
];

// 渲染模板
echo $twig->render('pages/demo.twig', $data); 
 
require_once dirname(__DIR__) . '/vendor/autoload.php';

// 获取 Twig 实例
$twig = require_once dirname(__DIR__) . '/config/twig.php';

// 示例数据
$data = [
    'site_name' => 'Sonice.Games',
    'base_url' => '/sonice-online-games-new',
    'user' => null, // 未登录状态
    'favorites_count' => 0,
    'new_games_count' => 12,
    'categories' => [
        ['name' => 'Action', 'icon' => 'gamepad', 'count' => 42],
        ['name' => 'Puzzle', 'icon' => 'puzzle-piece', 'count' => 35],
        ['name' => 'Racing', 'icon' => 'car', 'count' => 28],
        ['name' => 'Sports', 'icon' => 'futbol', 'count' => 35],
        ['name' => 'Shooter', 'icon' => 'crosshairs', 'count' => 24],
        ['name' => 'Cards', 'icon' => 'cards', 'count' => 18],
        ['name' => 'Strategy', 'icon' => 'chess', 'count' => 15],
        ['name' => 'Educational', 'icon' => 'graduation-cap', 'count' => 9]
    ],
    'featured_games' => [
        [
            'title' => 'Super Adventure',
            'description' => 'An exciting adventure game with amazing graphics',
            'category' => 'Action',
            'plays' => '1.2K'
        ],
        [
            'title' => 'Puzzle Master',
            'description' => 'Challenge your mind with this addictive puzzle game',
            'category' => 'Puzzle',
            'plays' => '3.4K'
        ],
        [
            'title' => 'Speed Racing',
            'description' => 'Feel the adrenaline in this racing game',
            'category' => 'Racing',
            'plays' => '2.8K'
        ]
    ]
];

// 渲染模板
echo $twig->render('pages/demo.twig', $data); 
 