<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

session_start();

// 修复Twig路径配置
$loader = new FilesystemLoader([
    __DIR__ . '/../templates'
]);

$twig = new Environment($loader, [
    'cache' => false,
    'debug' => true,
    'auto_reload' => true
]);

// 添加全局变量到Twig
$twig->addGlobal('base_url', '');
$twig->addGlobal('current_url', $_SERVER['REQUEST_URI'] ?? '/');

// 检查用户登录状态
$isLoggedIn = isset($_SESSION['user_id']);
$currentUser = null;

if ($isLoggedIn && $pdo) {
    try {
        $stmt = safeQuery($pdo, "SELECT id, username, email, avatar FROM users WHERE id = ?", [$_SESSION['user_id']]);
        if ($stmt) {
            $currentUser = $stmt->fetch();
            if (!$currentUser) {
                session_destroy();
                $isLoggedIn = false;
            }
        } else {
            session_destroy();
            $isLoggedIn = false;
        }
    } catch (Exception $e) {
        error_log('Error fetching user data: ' . $e->getMessage());
        session_destroy();
        $isLoggedIn = false;
    }
}

// 获取当前路径
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = trim(parse_url($requestUri, PHP_URL_PATH), '/');
$path = explode('?', $path, 2)[0];

// 加载游戏数据
function load_games_from_csv($csvFile) {
    $games = [];
    if (!file_exists($csvFile)) {
        error_log("CSV file not found: " . $csvFile);
        return $games;
    }
    
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        $header = fgetcsv($handle, 0, ',', '"', '\\');
        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== FALSE) {
            if (count($row) < 3) continue;
            $game = [
                'title' => $row[0],
                'iframe_url' => $row[1],
                'categories' => array_slice($row, 2),
            ];
            $game['slug'] = strtolower(str_replace([' ', "'", ":", "(", ")", "-"], ['-', '', '', '', '', '-'], $game['title']));
            
            // 检查图片
            $imgPath = __DIR__ . '/assets/images/games/' . $game['slug'] . '.webp';
            $imgPathPng = __DIR__ . '/assets/images/games/' . $game['slug'] . '.png';
            $imgPathJpg = __DIR__ . '/assets/images/games/' . $game['slug'] . '.jpg';
            
            if (file_exists($imgPath)) {
                $game['image_url'] = 'assets/images/games/' . $game['slug'] . '.webp';
            } elseif (file_exists($imgPathPng)) {
                $game['image_url'] = 'assets/images/games/' . $game['slug'] . '.png';
            } elseif (file_exists($imgPathJpg)) {
                $game['image_url'] = 'assets/images/games/' . $game['slug'] . '.jpg';
            } else {
                $game['image_url'] = 'assets/images/defaults/game-default.webp';
            }
            
            // 过滤无效游戏
            if (empty($game['iframe_url']) || !preg_match('#^https?://#', $game['iframe_url'])) {
                continue;
            }
            
            $games[] = $game;
        }
        fclose($handle);
    }
    return $games;
}

// 加载游戏数据
$csvFile = __DIR__ . '/../游戏iframe.CSV';
$games = load_games_from_csv($csvFile);

// 按分类组织游戏，并添加侧边栏需要的图标和颜色
$categories = [];
$categoryConfig = [
    'idle' => ['icon' => 'fa-solid fa-hourglass', 'color' => '#06d6a0'],
    'tycoon' => ['icon' => 'fa-solid fa-building', 'color' => '#4361ee'],
    'farm' => ['icon' => 'fa-solid fa-seedling', 'color' => '#06d6a0'],
    'clicker' => ['icon' => 'fa-solid fa-mouse-pointer', 'color' => '#f72585'],
    'mining' => ['icon' => 'fa-solid fa-gem', 'color' => '#ffd700'],
    'card' => ['icon' => 'fa-solid fa-chess', 'color' => '#a259fa'],
    'monster' => ['icon' => 'fa-solid fa-dragon', 'color' => '#ef476f'],
    'merge' => ['icon' => 'fa-solid fa-object-group', 'color' => '#7209b7'],
    'simulator' => ['icon' => 'fa-solid fa-cogs', 'color' => '#a259fa'],
    'defense' => ['icon' => 'fa-solid fa-shield-alt', 'color' => '#06d6a0'],
    'adventure' => ['icon' => 'fa-solid fa-map', 'color' => '#ffb703'],
    'block' => ['icon' => 'fa-solid fa-cube', 'color' => '#4361ee'],
    'factory' => ['icon' => 'fa-solid fa-industry', 'color' => '#7209b7'],
    'fishing' => ['icon' => 'fa-solid fa-fish', 'color' => '#06d6a0'],
    'runner' => ['icon' => 'fa-solid fa-running', 'color' => '#ffb703'],
    'shooter' => ['icon' => 'fa-solid fa-crosshairs', 'color' => '#ffd166'],
    'fish' => ['icon' => 'fa-solid fa-fish', 'color' => '#06d6a0'],
    'treasure' => ['icon' => 'fa-solid fa-treasure-chest', 'color' => '#ffd700'],
    'racing' => ['icon' => 'fa-solid fa-car', 'color' => '#ff7f50'],
    'dance' => ['icon' => 'fa-solid fa-music', 'color' => '#a259fa'],
    'crafting' => ['icon' => 'fa-solid fa-hammer', 'color' => '#7209b7']
];

foreach ($games as $game) {
    $category = $game['categories'][0] ?? 'Other';
    if (!isset($categories[$category])) {
        $config = $categoryConfig[strtolower(str_replace(' ', '-', $category))] ?? 
                 ['icon' => 'fa-solid fa-gamepad', 'color' => '#888888'];
        
        $categories[$category] = [
            'name' => $category,
            'slug' => strtolower(str_replace(' ', '-', $category)),
            'icon' => $config['icon'],
            'color' => $config['color'],
            'games' => []
        ];
    }
    $categories[$category]['games'][] = $game;
}

// 准备模板数据
$data = [
    'base_url' => '',
    'current_url' => $requestUri,
    'is_logged_in' => $isLoggedIn,
    'user' => $currentUser,
    'current_user' => $currentUser,
    'page_title' => 'Welcome',
    'page_description' => 'Play free online games at Sonice.Games',
    'games' => $games,
    'categories' => array_values($categories),
    'favorites_count' => 5,
    'recently_played_count' => 3,
    'new_games_count' => 10
];

// 路由处理
switch ($path) {
    case '':
    case 'home':
    case 'index-fixed.php':
        $data['page_title'] = 'Home - Sonice.Games';
        $data['page_description'] = 'Play free online games at Sonice.Games';
        $data['featured_games'] = array_slice($games, 0, 6);
        $data['new_games'] = array_slice($games, 0, 12);
        $data['popular_categories'] = array_slice(array_values($categories), 0, 6);
        
        try {
            echo $twig->render('pages/home.twig', $data);
        } catch (Exception $e) {
            error_log('Twig error: ' . $e->getMessage());
            // 如果Twig失败，显示错误信息
            echo '<h1>Twig模板错误</h1>';
            echo '<p>错误信息: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p>请使用备用页面: <a href="game-home.php">game-home.php</a></p>';
        }
        exit;
        
    case 'game':
        $slug = $_GET['slug'] ?? '';
        if ($slug) {
            $game = null;
            foreach ($games as $g) {
                if ($g['slug'] === $slug) {
                    $game = $g;
                    break;
                }
            }
            
            if ($game) {
                $data['game'] = $game;
                $data['page_title'] = $game['title'] . ' - Sonice.Games';
                $data['page_description'] = 'Play ' . $game['title'] . ' online for free';
                
                // 获取相关游戏
                $related_games = array_filter($games, function($g) use ($game) {
                    return $g['slug'] !== $game['slug'] && 
                           array_intersect($g['categories'], $game['categories']);
                });
                $data['related_games'] = array_slice($related_games, 0, 5);
                
                try {
                    echo $twig->render('pages/game-detail.twig', $data);
                } catch (Exception $e) {
                    error_log('Twig error: ' . $e->getMessage());
                    echo '<h1>Twig模板错误</h1>';
                    echo '<p>错误信息: ' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '<p>请使用备用页面: <a href="game.php?slug=' . urlencode($slug) . '">game.php</a></p>';
                }
                exit;
            }
        }
        // 游戏未找到，显示404
        $data['page_title'] = 'Game Not Found';
        echo $twig->render('pages/404.twig', $data);
        exit;
        
    case 'category':
        $categorySlug = $_GET['slug'] ?? '';
        if ($categorySlug) {
            $category = null;
            foreach ($categories as $cat) {
                if ($cat['slug'] === $categorySlug) {
                    $category = $cat;
                    break;
                }
            }
            
            if ($category) {
                $data['category'] = $category;
                $data['page_title'] = $category['name'] . ' Games - Sonice.Games';
                $data['page_description'] = 'Play ' . $category['name'] . ' games online for free';
                echo $twig->render('pages/category.twig', $data);
                exit;
            }
        }
        // 分类未找到，显示404
        $data['page_title'] = 'Category Not Found';
        echo $twig->render('pages/404.twig', $data);
        exit;
        
    case 'search':
        $query = $_GET['q'] ?? '';
        $data['search_query'] = $query;
        $data['page_title'] = 'Search Results - Sonice.Games';
        
        if ($query) {
            $search_results = array_filter($games, function($game) use ($query) {
                return stripos($game['title'], $query) !== false ||
                       array_filter($game['categories'], function($cat) use ($query) {
                           return stripos($cat, $query) !== false;
                       });
            });
            $data['search_results'] = array_values($search_results);
        } else {
            $data['search_results'] = [];
        }
        
        echo $twig->render('pages/search.twig', $data);
        exit;
        
    case 'about':
        $data['page_title'] = 'About Us - Sonice.Games';
        echo $twig->render('pages/about.twig', $data);
        exit;
        
    case 'contact':
        $data['page_title'] = 'Contact - Sonice.Games';
        echo $twig->render('pages/contact.twig', $data);
        exit;
        
    case 'privacy':
        $data['page_title'] = 'Privacy Policy - Sonice.Games';
        echo $twig->render('pages/privacy.twig', $data);
        exit;
        
    case 'terms':
        $data['page_title'] = 'Terms of Service - Sonice.Games';
        echo $twig->render('pages/terms.twig', $data);
        exit;
        
    default:
        // 404页面
        header('HTTP/1.0 404 Not Found');
        $data['page_title'] = '404 Not Found - Sonice.Games';
        echo $twig->render('pages/404.twig', $data);
        exit;
}
?> 