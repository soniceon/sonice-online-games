<?php
require_once __DIR__ . '/config/database.php';

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
$csvFile = __DIR__ . '/游戏iframe.CSV';
$games = load_games_from_csv($csvFile);

// 按分类组织游戏
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
    // 处理每个游戏的多个分类
    foreach ($game['categories'] as $category) {
        $category = trim($category); // 去除空格
        if (empty($category)) continue;
        
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
}

$categories = array_values($categories);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎮 Sonice Online Games - Free Online Gaming Platform</title>
    <meta name="description" content="Play the best free online games at Sonice.Games - Best browser games, HTML5 games and free online gaming collection">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%) !important;
            color: #ffffff;
        }
        .content-wrapper {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        .category-block { 
            margin-bottom: 0.25rem; /* 从0.5rem进一步减少到0.25rem */
            padding: 8px; /* 从12px减少到8px */
            background: rgba(30, 58, 138, 0.15);
            border-radius: 16px;
            border: 1px solid rgba(59, 130, 246, 0.2);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .category-block:hover {
            background: rgba(30, 58, 138, 0.2); /* 悬停时背景稍微变亮 */
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15); /* 悬停时阴影加深 */
            transform: translateY(-2px); /* 悬停时轻微上移 */
        }
        .category-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: #ffffff;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 0.25rem; /* 从0.5rem进一步减少到0.25rem */
            display: inline-block;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
        }
        .category-title::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #60a5fa, #3b82f6); /* 渐变下划线 */
            border-radius: 2px;
        }
        .game-grid { 
            margin-bottom: 0.25rem; 
        }
        .carousel-arrow {
            width: 48px;
            height: 100%;
            min-height: 40px;
            border-radius: 8px;
            background: rgba(30, 64, 175, 0.25);
            border: none;
            display: none;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2rem;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s, opacity 0.2s;
            z-index: 20;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 2px 8px 0 rgba(0,0,0,0.10);
            position: absolute;
            transform: translateY(-50%);
        }
        .carousel-arrow[disabled], .carousel-arrow.disabled {
            opacity: 0.4;
            pointer-events: none;
        }
        .carousel-arrow:hover {
            background: rgba(30, 64, 175, 0.45);
            transform: translateY(-50%) scale(1.08);
        }
        .carousel-arrow-left::before {
            content: '\2039';
            font-size: 2rem;
            display: block;
            line-height: 1;
        }
        .carousel-arrow-right::before {
            content: '\203A';
            font-size: 2rem;
            display: block;
            line-height: 1;
        }
        .category-block:hover .carousel-arrow {
            display: flex !important;
        }
        @media (max-width: 900px) {
            .carousel-arrow { width: 36px; min-height: 32px; }
        }
        .sidebar-blue { background-color: #152a69; }
        .sidebar-hover { background-color: #1d3a8f; }
        
        /* 游戏卡片样式 */
        .game-card-home {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 8px; /* 增大圆角，更美观 */
            overflow: hidden;
            transition: all 0.3s ease; /* 更平滑的过渡动画 */
            max-width: 100%; /* 让卡片填满网格单元格 */
            max-height: 100%; /* 让卡片填满网格单元格 */
            border: 2px solid transparent; /* 透明边框，为悬停效果做准备 */
            background: linear-gradient(145deg, #1e3a8a, #2563eb); /* 渐变背景 */
        }
        .game-card-home:hover {
            transform: translateY(-6px) scale(1.05); /* 更明显的悬停效果 */
            box-shadow: 0 12px 25px rgba(59, 130, 246, 0.4); /* 蓝色光晕阴影 */
            border-color: #3b82f6; /* 悬停时显示蓝色边框 */
        }
        .game-card-home img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease; /* 图片缩放动画 */
        }
        .game-card-home:hover img {
            transform: scale(1.1); /* 悬停时图片稍微放大 */
        }
        .game-grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr); /* 固定10列 */
            gap: 6px; /* 增加间距到6px，让卡片有呼吸空间 */
            justify-content: start; /* 左对齐 */
            width: 100%; /* 充分利用宽度 */
            padding: 6px; /* 添加内边距 */
            background: rgba(30, 58, 138, 0.1); /* 轻微的背景色 */
            border-radius: 12px; /* 圆角背景 */
            margin-top: 0.125rem; /* 从0.25rem进一步减少到0.125rem */
        }
        
        /* 性能优化 */
        .game-card-home {
            will-change: transform;
            backface-visibility: hidden;
            transform: translateZ(0);
        }
        
        .game-card-home img {
            will-change: transform;
            backface-visibility: hidden;
            transform: translateZ(0);
        }
        
        /* 减少动画复杂度 */
        .carousel-arrow {
            will-change: transform;
            backface-visibility: hidden;
        }
        
        /* 优化侧边栏动画 */
        #sidebar {
            will-change: width;
            backface-visibility: hidden;
        }
        
        #mainContent {
            will-change: margin-left;
            backface-visibility: hidden;
        }
        .more-games-btn {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8); /* 渐变背景 */
            color: white;
            padding: 8px 16px;
            border-radius: 20px; /* 圆角按钮 */
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3); /* 蓝色阴影 */
        }
        .more-games-btn:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af); /* 悬停时颜色变深 */
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4); /* 悬停时阴影加深 */
        }
        .games-count {
            background: rgba(59, 130, 246, 0.1);
            color: #60a5fa;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-dark text-white">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-30 bg-black bg-opacity-90 backdrop-blur-sm border-b border-gray-800">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <!-- Logo -->
            <a href="index.php" class="flex items-center space-x-2">
                <img src="public/assets/images/icons/logo.png" alt="Sonice.Games" class="h-10 w-10 rounded-full object-cover">
                <span class="text-2xl font-bold text-white">Sonice<span class="text-blue-500">.Games</span></span>
            </a>
            <!-- Search Bar -->
            <div class="flex-1 max-w-2xl mx-8">
                <div class="relative">
                    <form id="searchForm" action="public/test-game.php" method="get" class="relative">
                        <input type="search" name="q" id="searchInput" placeholder="Search games..." class="w-full px-5 py-2 bg-[#233a6b] border-none rounded-full text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent shadow-inner">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-blue-500 hover:bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
            <!-- Login/Register Button -->
            <button id="navLoginBtn" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded">Login/Register</button>
        </div>
    </header>

    <div class="flex flex-1 min-h-0 pt-16">
        <!-- Sidebar -->
        <nav id="sidebar" class="group fixed left-0 top-16 bottom-0 h-[calc(100vh-4rem)] w-14 hover:w-56 bg-sidebar-blue flex flex-col z-20 transition-all duration-300 ease-in-out overflow-hidden">
            <div class="flex-1 py-2 overflow-y-auto" style="scrollbar-width:none; -ms-overflow-style:none; overflow-y:scroll;">
                <style>.overflow-y-auto::-webkit-scrollbar { display:none!important; width:0!important; height:0!important; background:transparent!important; }</style>
                <ul class="mt-2">
                    <!-- Home -->
                    <li>
                        <a href="index.php" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                            <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                                <i class="fa-solid fa-home text-2xl" style="color:#3b82f6;"></i>
                            </span>
                            <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Home</span>
                        </a>
                    </li>
                    <!-- Favorites -->
                    <li>
                        <a href="public/test-game.php" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                            <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                                <i class="fa-solid fa-heart text-2xl" style="color:#ef476f;"></i>
                            </span>
                            <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Favorites</span>
                        </a>
                    </li>
                    <!-- Recently Played -->
                    <li>
                        <a href="public/test-game.php" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                            <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                                <i class="fa-solid fa-history text-2xl" style="color:#06d6a0;"></i>
                            </span>
                            <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Recently Played</span>
                        </a>
                    </li>
                </ul>
                <!-- Categories -->
                <div class="mt-2">
                    <h3 class="px-2 text-xs font-semibold text-gray-300 uppercase tracking-wider opacity-0 group-hover:opacity-100 transition-opacity duration-200">Categories</h3>
                    <ul class="mt-2">
                        <?php foreach ($categories as $category): ?>
                        <li>
                            <a href="#<?= $category['slug'] ?>" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                                <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                                    <i class="<?= $category['icon'] ?> text-2xl" style="color: <?= $category['color'] ?>;"></i>
                                </span>
                                <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap"><?= $category['name'] ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <!-- Logo and Links -->
                <div class="w-full py-4 flex flex-col items-center justify-center gap-2">
                                            <a href="index.php" class="flex items-center justify-center mb-2">
                        <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;">
                            <img src="public/assets/images/icons/logo.png" alt="Sonice Games" class="w-8 h-8 transition-all duration-200" />
                        </span>
                    </a>
                    <div class="flex flex-col items-center w-full opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <span class="mt-2 text-lg font-bold text-white whitespace-nowrap">
                            Sonice<span class="text-blue-400">.Games</span>
                        </span>
                        <p class="text-xs text-gray-200 text-center whitespace-nowrap mb-2">
                            Play the best online games for free. New games added daily!
                        </p>
                        <div class="flex space-x-3 mt-1 mb-2">
                            <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-discord"></i></a>
                        </div>
                        <!-- Quick Links -->
                        <div class="w-full mt-2">
                            <h3 class="text-base font-semibold mb-2 text-white text-center">Quick Links</h3>
                            <ul class="space-y-1 text-center">
                                <li><a href="public/about.php" class="hover:text-blue-300 text-gray-300">About Us</a></li>
                                <li><a href="public/contact.php" class="hover:text-blue-300 text-gray-300">Contact</a></li>
                                <li><a href="public/privacy.php" class="hover:text-blue-300 text-gray-300">Privacy Policy</a></li>
                                <li><a href="public/terms.php" class="hover:text-blue-300 text-gray-300">Terms of Service</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div id="mainContent" class="flex-1 flex flex-col min-h-0 ml-14 transition-all duration-300">
            <main class="flex-1 gradient-blue">
                <div class="w-full px-0 py-4">
                    <div class="pl-2"> <!-- 进一步减少左边距 -->
                        <!-- Game Categories -->
                        <?php foreach ($categories as $category): ?>
                        <?php if (count($category['games']) > 0): ?>
                        <div class="category-block mb-4 group" id="cat-block-<?= $category['slug'] ?>">
                            <div class="flex items-center mb-2">
                                <h2 class="category-title mr-2 flex items-center">
                                    <span class="inline-block align-middle mr-2">
                                        <i class="<?= $category['icon'] ?>" style="color: <?= $category['color'] ?>;"></i>
                                    </span>
                                    <?= $category['name'] ?> Games
                                </h2>
                                <div class="flex items-center space-x-3">
                                    <span class="games-count"><?= count($category['games']) ?> games</span>
                                    <a href="public/test-game.php#<?= $category['slug'] ?>" class="more-games-btn">More Games</a>
                                </div>
                            </div>
                            <div class="relative px-0 flex items-center">
                                <!-- 移除分页箭头，像CrazyGames那样直接显示所有游戏 -->
                                <div class="game-grid" id="cat-grid-<?= $category['slug'] ?>"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- 页脚按钮区域 -->
    <div class="w-full flex flex-col items-center justify-center my-8">
        <div class="flex flex-row items-center justify-center gap-6">
            <button id="randomGameBtn" class="flex items-center px-8 py-3 rounded-full border border-blue-600 text-blue-600 font-semibold text-lg bg-white shadow-lg hover:bg-blue-50 transition-all duration-300 hover:scale-105">
                <i class="fa-solid fa-dice-six mr-2"></i> Random Game
            </button>
            <button onclick="window.scrollTo({top:0,behavior:'smooth'})" class="flex items-center px-8 py-3 rounded-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold text-lg transition-all duration-300 hover:scale-105 shadow-lg">
                <i class="fa-solid fa-arrow-up mr-2"></i> Back to Top
            </button>
        </div>
    </div>

    <!-- 页脚 -->
    <footer class="bg-gray-900 text-white py-8 mt-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Stats Section -->
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-xl font-bold mb-4">Game Statistics</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-400"><?= count($games) ?></div>
                            <div class="text-gray-400">Total Games</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-400"><?= count($categories) ?></div>
                            <div class="text-gray-400">Categories</div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="public/about.php" class="text-gray-400 hover:text-white transition">About Us</a></li>
                        <li><a href="public/contact.php" class="text-gray-400 hover:text-white transition">Contact</a></li>
                        <li><a href="public/privacy.php" class="text-gray-400 hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="public/terms.php" class="text-gray-400 hover:text-white transition">Terms of Service</a></li>
                    </ul>
                </div>
                
                <!-- Social Links -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-facebook text-2xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-twitter text-2xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-instagram text-2xl"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-400">&copy; 2025 Sonice.Games. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Debug Info (可以删除) -->
    <?php if (isset($_GET['debug'])): ?>
    <div class="fixed bottom-4 right-4 bg-black bg-opacity-80 p-4 rounded-lg text-white text-sm max-w-md">
        <h3 class="font-bold mb-2">分类调试信息:</h3>
        <?php foreach ($categories as $category): ?>
        <div class="mb-2">
            <strong><?= $category['name'] ?></strong> (<?= count($category['games']) ?> 个游戏)
            <div class="text-xs text-gray-300 ml-2">
                <?php foreach (array_slice($category['games'], 0, 3) as $game): ?>
                • <?= $game['title'] ?><br>
                <?php endforeach; ?>
                <?php if (count($category['games']) > 3): ?>
                • ... 还有 <?= count($category['games']) - 3 ?> 个游戏
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-60 hidden">
        <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-xs relative">
            <button id="closeLoginModal" class="absolute top-3 right-3 text-gray-400 hover:text-blue-600 text-2xl">&times;</button>
            <h2 class="text-2xl font-bold mb-6 text-center text-blue-700">Login</h2>
            <div id="loginError" class="text-red-500 text-center mb-2 hidden"></div>
            <input id="loginUsername" type="text" placeholder="Username/Email" class="w-full mb-3 p-2 border rounded text-gray-900 focus:ring-2 focus:ring-blue-400" required>
            <input id="loginPassword" type="password" placeholder="Password" class="w-full mb-4 p-2 border rounded text-gray-900 focus:ring-2 focus:ring-blue-400" required>
            <button id="loginSubmit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-semibold transition">Login</button>
        </div>
    </div>

    <script>
        // 游戏数据
        window.categoryGames = {};
        <?php foreach ($categories as $category): ?>
        window.categoryGames['<?= $category['slug'] ?>'] = [
            <?php foreach ($category['games'] as $game): ?>
            { slug: "<?= $game['slug'] ?>", title: "<?= addslashes($game['title']) ?>" },
            <?php endforeach; ?>
        ];
        <?php endforeach; ?>

        // 简化的渲染函数，限制每个分类显示20个游戏
        function renderCategoryPage(slug) {
            const games = window.categoryGames[slug] || [];
            const grid = document.getElementById('cat-grid-' + slug);
            const leftBtn = document.querySelector('#cat-block-' + slug + ' .carousel-arrow-left');
            const rightBtn = document.querySelector('#cat-block-' + slug + ' .carousel-arrow-right');
            if (!grid) return;
            
            grid.innerHTML = '';
            
            // 只显示前20个游戏，让页面更简洁
            const displayGames = games.slice(0, 20);
            
            displayGames.forEach(game => {
                const card = document.createElement('div');
                card.className = 'game-card-home';
                card.innerHTML = `<a href="public/game.php?slug=${game.slug}"><img src="public/assets/images/games/${game.slug}.webp" alt="${game.title}" onerror="this.src='public/assets/images/defaults/game-default.webp'" loading="lazy"></a>`;
                grid.appendChild(card);
            });
            
            // 隐藏分页箭头，像CrazyGames那样
            if (leftBtn) leftBtn.style.display = 'none';
            if (rightBtn) rightBtn.style.display = 'none';
        }
        
        // 移除分页相关代码，像CrazyGames那样直接显示所有游戏
        // function scrollCategoryPage(slug, dir) {
        //     const games = window.categoryGames[slug] || [];
        //     const gamesPerPage = 20;
        //     const maxPage = Math.floor((games.length - 1) / gamesPerPage);
        //     window.categoryPages[slug] = (window.categoryPages[slug] || 0) + dir;
        //     if (window.categoryPages[slug] < 0) window.categoryPages[slug] = 0;
        //     if (window.categoryPages[slug] > maxPage) window.categoryPages[slug] = maxPage;
        //     renderCategoryPage(slug);
        // }

        // 平滑滚动到分类
        function smoothScrollToCategory(targetId) {
            const target = document.getElementById(targetId);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        }

        // 随机游戏功能
        function setupRandomGame() {
            const randomBtn = document.getElementById('randomGameBtn');
            if (randomBtn) {
                randomBtn.addEventListener('click', function() {
                    // 获取所有游戏
                    const allGames = [];
                    Object.values(window.categoryGames).forEach(games => {
                        allGames.push(...games);
                    });
                    
                    if (allGames.length > 0) {
                        // 随机选择一个游戏
                        const randomGame = allGames[Math.floor(Math.random() * allGames.length)];
                        // 跳转到游戏详情页
                        window.location.href = 'public/game.php?slug=' + randomGame.slug;
                    } else {
                        alert('No games found!');
                    }
                });
            }
        }

        // 页面加载完成后初始化
        document.addEventListener('DOMContentLoaded', function() {
            // 初始化分类页面
            Object.keys(window.categoryGames).forEach(slug => {
                renderCategoryPage(slug);
            });
            
            // 设置随机游戏功能
            setupRandomGame();
            
            // 设置平滑滚动
            document.querySelectorAll('a[href^="#"]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    smoothScrollToCategory(targetId);
                });
            });
        });

        // 侧边栏展开时推开内容区
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        if (sidebar && mainContent) {
            sidebar.addEventListener('mouseenter', () => {
                mainContent.classList.remove('ml-14');
                mainContent.classList.add('ml-56');
            });
            sidebar.addEventListener('mouseleave', () => {
                mainContent.classList.remove('ml-56');
                mainContent.classList.add('ml-14');
            });
        }

        // 弹窗控制
        const loginModal = document.getElementById('loginModal');
        const showLoginModal = () => { loginModal.classList.remove('hidden'); loginModal.classList.add('flex'); };
        const hideLoginModal = () => { loginModal.classList.add('hidden'); loginModal.classList.remove('flex'); };
        document.getElementById('navLoginBtn').onclick = showLoginModal;
        document.getElementById('closeLoginModal').onclick = hideLoginModal;
        loginModal.addEventListener('click', e => { if (e.target === loginModal) hideLoginModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') hideLoginModal(); });

        // 搜索表单
        document.getElementById('searchForm').onsubmit = function(e) {
            e.preventDefault();
            const q = document.getElementById('searchInput').value.trim();
            if (q) window.location.href = 'public/test-game.php?search=' + encodeURIComponent(q);
        };

        // 处理分类链接点击
        document.addEventListener('click', function(e) {
            if (e.target.closest('a[href^="#"]')) {
                e.preventDefault();
                const href = e.target.closest('a[href^="#"]').getAttribute('href');
                if (href.startsWith('#')) {
                    const categorySlug = href.substring(1);
                    smoothScrollToCategory(categorySlug);
                }
            }
        });
    </script>
</body>
</html> 