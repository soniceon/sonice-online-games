<?php
// echo 'INDEX_PHP_LOADED';
// exit;
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

session_start();

// Initialize Twig
$loader = new FilesystemLoader([
    __DIR__ . '/../templates/pages',
    __DIR__ . '/../templates/layouts',
    __DIR__ . '/../templates/partials'
]);
$twig = new Environment($loader, [
    'cache' => false,
]);

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$currentUser = null;

if ($isLoggedIn) {
    try {
        $stmt = $pdo->prepare("SELECT id, username, email, avatar FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $currentUser = $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Error fetching user data: ' . $e->getMessage());
    }
}

// Get the current page from URL
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$basePath = '';
$path = substr($requestUri, strlen($basePath));
$path = trim($path, '/');
$path = explode('?', $path, 2)[0];

// Twig 初始化后立即初始化 $data
$data = [
    'base_url' => $basePath,
    'current_url' => $requestUri,
    'is_logged_in' => $isLoggedIn,
    'user' => $currentUser,
    'current_user' => $currentUser,
    'page_title' => 'Welcome',
    'page_description' => 'Play free online games at Sonice.Games',
    'favorites_count' => 5,
    'recently_played_count' => 3,
    'new_games_count' => 10,
    'categories' => [
        [ 'name' => 'Action',    'slug' => 'action',    'icon' => 'fas fa-gamepad',      'color' => '#3b82f6',  'game_count' => 42 ],
        [ 'name' => 'Racing',    'slug' => 'racing',    'icon' => 'fas fa-car',          'color' => '#ff7f50',  'game_count' => 28 ],
        [ 'name' => 'Sports',    'slug' => 'sports',    'icon' => 'fas fa-futbol',       'color' => '#06d6a0',  'game_count' => 35 ],
        [ 'name' => 'Shooter',   'slug' => 'shooter',   'icon' => 'fas fa-crosshairs',   'color' => '#ffd166',  'game_count' => 24 ],
        [ 'name' => 'Cards',     'slug' => 'cards',     'icon' => 'fas fa-chess',        'color' => '#a259fa',  'game_count' => 18 ],
        [ 'name' => 'Adventure', 'slug' => 'adventure', 'icon' => 'fas fa-map',          'color' => '#ffb703',  'game_count' => 20 ],
        [ 'name' => 'Puzzle',    'slug' => 'puzzle',    'icon' => 'fas fa-puzzle-piece', 'color' => '#f72585',  'game_count' => 35 ],
        [ 'name' => 'Strategy',  'slug' => 'strategy',  'icon' => 'fas fa-chess',        'color' => '#4361ee',  'game_count' => 15 ],
        [ 'name' => 'Mining',    'slug' => 'mining',    'icon' => 'fas fa-gem',          'color' => '#ffd700',  'game_count' => 19 ],
        [ 'name' => 'Idle',      'slug' => 'idle',      'icon' => 'fas fa-hourglass',    'color' => '#06d6a0',  'game_count' => 19 ],
        [ 'name' => 'Clicker',   'slug' => 'clicker',   'icon' => 'fas fa-mouse-pointer','color' => '#f72585',  'game_count' => 21 ],
        [ 'name' => 'Simulation','slug' => 'simulation','icon' => 'fas fa-cogs',         'color' => '#a259fa',  'game_count' => 17 ],
        [ 'name' => 'Tycoon',    'slug' => 'tycoon',    'icon' => 'fas fa-building',     'color' => '#4361ee',  'game_count' => 16 ],
        [ 'name' => 'Arcade',    'slug' => 'arcade',    'icon' => 'fas fa-rocket',       'color' => '#3b82f6',  'game_count' => 22 ],
        [ 'name' => 'Board',     'slug' => 'board',     'icon' => 'fas fa-th-large',     'color' => '#ff7f50',  'game_count' => 14 ],
        [ 'name' => 'Multiplayer','slug' => 'multiplayer','icon' => 'fas fa-users',      'color' => '#06d6a0',  'game_count' => 13 ],
        [ 'name' => 'IO',        'slug' => 'io',        'icon' => 'fas fa-network-wired','color' => '#ffd166',  'game_count' => 12 ],
        [ 'name' => 'Platformer','slug' => 'platformer','icon' => 'fas fa-shapes',       'color' => '#ffb703',  'game_count' => 18 ],
        [ 'name' => 'Educational','slug' => 'educational','icon' => 'fas fa-graduation-cap','color' => '#f72585','game_count' => 9 ],
        [ 'name' => 'Music',     'slug' => 'music',     'icon' => 'fas fa-music',        'color' => '#a259fa',  'game_count' => 8 ],
        [ 'name' => 'Other',     'slug' => 'other',     'icon' => 'fas fa-ellipsis-h',   'color' => '#888888',  'game_count' => 5 ]
    ]
];

// 自动修正 Font Awesome 图标前缀，兼容新版 fa-solid
foreach ($data['categories'] as &$cat) {
    if (isset($cat['icon'])) {
        $cat['icon'] = str_replace('fas ', 'fa-solid ', $cat['icon']);
    }
}
unset($cat);

// 分类slug到名称和icon的映射
$categoryMap = [
    'action'   => ['name' => 'Action',   'icon' => 'fas fa-gamepad'],
    'racing'   => ['name' => 'Racing',   'icon' => 'fas fa-car'],
    'sports'   => ['name' => 'Sports',   'icon' => 'fas fa-futbol'],
    'shooter'  => ['name' => 'Shooter',  'icon' => 'fas fa-crosshairs'],
    'cards'    => ['name' => 'Cards',    'icon' => 'fas fa-chess'],
    'adventure'=> ['name' => 'Adventure','icon' => 'fas fa-map'],
    'puzzle'   => ['name' => 'Puzzle',   'icon' => 'fas fa-puzzle-piece'],
    'strategy' => ['name' => 'Strategy', 'icon' => 'fas fa-chess'],
];

// CSV驱动游戏数据
function load_games_from_csv($csvFile) {
    $games = [];
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        $header = fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== FALSE) {
            if (count($row) < 3) continue;
            $game = [
                'title' => $row[0],
                'iframe_url' => $row[1],
                'categories' => array_slice($row, 2),
            ];
            $game['slug'] = strtolower(str_replace([' ', "'", ":"], ['-', '', ''], $game['title']));
            $imgPath = __DIR__ . '/assets/images/games/' . $game['slug'] . '.webp';
            // 过滤无图片或无有效iframe的游戏
            if (!file_exists($imgPath)) continue;
            if (empty($game['iframe_url']) || !preg_match('#^https?://#', $game['iframe_url'])) continue;
            $games[] = $game;
        }
        fclose($handle);
    }
    return $games;
}
$csvFile = __DIR__ . '/../游戏iframe.CSV';
$games = load_games_from_csv($csvFile);
// 首页和新游都用CSV
$data['featured_games'] = $games;
$data['new_games'] = $games;

// 分类slug到游戏的映射
$categoryGames = [];
foreach ($games as $game) {
    foreach ($game['categories'] as $catName) {
        $catSlug = strtolower(str_replace([' ', "'", ":"], ['-', '', ''], $catName));
        if (!isset($categoryGames[$catSlug])) $categoryGames[$catSlug] = [];
        $categoryGames[$catSlug][] = $game;
    }
}
// 给每个分类加上 games 字段
foreach ($data['categories'] as &$cat) {
    $catSlug = $cat['slug'];
    $cat['games'] = $categoryGames[$catSlug] ?? [];
}
unset($cat);
// 首页热门分类
$data['popular_categories'] = array_slice($data['categories'], 0, 6);

// 自动分类路由匹配，优先于switch($path)
if (preg_match('#^category/([a-z0-9-]+)$#', $path, $matches)) {
    $slug = $matches[1];
    foreach ($data['categories'] as $cat) {
        if ($cat['slug'] === $slug) {
            $data['category'] = $cat;
            $data['category_name'] = $cat['name'];
            $data['category_games'] = $cat['games'];
            $data['total_pages'] = 1;
            $data['current_page'] = 1;
            echo $twig->render('category.twig', $data);
            exit;
        }
    }
    echo $twig->render('404.twig', $data);
    exit;
}

// 处理 /game/{slug} 路由
if (preg_match('#^game/([a-zA-Z0-9-_]+)$#', $path, $matches)) {
    $slug = $matches[1];
    $game = null;
    foreach ($games as $g) {
        if ($g['slug'] === $slug) {
            $game = $g;
            break;
        }
    }
    if ($game) {
        $data['game'] = $game;
        echo $twig->render('game-detail.twig', $data);
        exit;
    } else {
        echo $twig->render('404.twig', $data);
        exit;
    }
}

// Add page-specific data
switch ($path) {
    case '':
    case 'home':
        $data['page_title'] = 'Home';
        // 自动同步首页游戏与详情页
        // $games = [];
        // $gamesDir = __DIR__ . '/../templates/pages/games/';
        // foreach (glob($gamesDir . '*.twig') as $file) {
        //     $slug = basename($file, '.twig');
        //     $games[] = [
        //         'slug' => $slug,
        //         'title' => ucwords(str_replace('-', ' ', $slug)),
        //         'category' => 'Unknown',
        //         'plays' => rand(1000, 10000)
        //     ];
        // }
        // $data['featured_games'] = $games;
        // $data['new_games'] = $games;
        $data['popular_categories'] = array_slice($data['categories'], 0, 6);
        echo $twig->render('home.twig', $data);
        exit;
    case 'about':
        $data['page_title'] = 'About Us';
        echo $twig->render('about.twig', $data);
        exit;
    case 'contact':
        $data['page_title'] = 'Contact';
        echo $twig->render('contact.twig', $data);
        exit;
    case 'privacy':
        $data['page_title'] = 'Privacy Policy';
        echo $twig->render('privacy.twig', $data);
        exit;
    case 'terms':
        $data['page_title'] = 'Terms of Service';
        echo $twig->render('terms.twig', $data);
        exit;
    case '404':
        $data['page_title'] = '404 Not Found';
        header('HTTP/1.0 404 Not Found');
        break;
        
    case 'profile':
        $data['page_title'] = '个人资料';
        echo $twig->render('profile.twig', $data);
        exit;
    case 'recently-played':
        $data['page_title'] = '玩过的游戏';
        echo $twig->render('recently-played.twig', $data);
        exit;
    case 'favorites':
        $data['page_title'] = '收藏的游戏';
        echo $twig->render('favorites.twig', $data);
        exit;
    case 'dashboard':
        $data['page_title'] = 'Dashboard';
        echo $twig->render('dashboard.twig', $data);
        exit;
    case 'search':
        $data['page_title'] = '搜索结果';
        $data['search_query'] = $_GET['q'] ?? '';
        echo $twig->render('search.twig', $data);
        exit;
        
    default:
        // If no matching route found, show 404
        header('HTTP/1.0 404 Not Found');
        $path = '404';
        $data['page_title'] = '404 Not Found';
}

// PHP 输出调试 categories
// echo '<pre style="color:red;">';
// print_r($data['categories']);
// echo '</pre>';

// 只保护其它数据，不覆盖 categories
// foreach (['popular_categories', 'featured_games', 'new_games'] as $key) {
//     if (!isset($data[$key]) || !is_array($data[$key])) {
//         $data[$key] = [];
//     }
// }

$data['games'] = $games;

try {
    // Attempt to load and render the template
    echo $twig->render("{$path}.twig", $data);
} catch (Exception $e) {
    error_log('Template error: ' . $e->getMessage());
    $data['error_message'] = 'An error occurred while loading the page.';
    try {
        echo $twig->render('error.twig', $data);
    } catch (Exception $e2) {
        // 彻底兜底，直接输出纯文本
        echo "<h1>严重错误</h1><p>无法加载错误模板: " . htmlspecialchars($e2->getMessage()) . "</p>";
    }
} 