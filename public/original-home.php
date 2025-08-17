<?php
// 原始首页的PHP版本 - 保持原有设计和功能
require_once __DIR__ . '/../config/database.php';

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
            
            // 检查多种图片格式
            $imgPath = __DIR__ . '/assets/images/games/' . $game['slug'] . '.webp';
            $imgPathPng = __DIR__ . '/assets/images/games/' . $game['slug'] . '.png';
            $imgPathJpg = __DIR__ . '/assets/images/games/' . $game['slug'] . '.jpg';
            
            // 过滤无图片或无有效iframe的游戏
            if (!file_exists($imgPath) && !file_exists($imgPathPng) && !file_exists($imgPathJpg)) {
                continue; // 跳过没有图片的游戏
            }
            
            if (empty($game['iframe_url']) || !preg_match('#^https?://#', $game['iframe_url'])) {
                continue; // 跳过无效iframe的游戏
            }
            
            $games[] = $game;
        }
        fclose($handle);
    }
    return $games;
}

$csvFile = __DIR__ . '/../游戏iframe.CSV';
$games = load_games_from_csv($csvFile);

// 按分类组织游戏
$categories = [
    'Action' => [],
    'Racing' => [],
    'Sports' => [],
    'Shooter' => [],
    'Cards' => [],
    'Adventure' => [],
    'Puzzle' => [],
    'Strategy' => [],
    'Mining' => [],
    'Idle' => [],
    'Clicker' => [],
    'Simulation' => [],
    'Tycoon' => [],
    'Arcade' => [],
    'Board' => [],
    'Multiplayer' => [],
    'IO' => [],
    'Platformer' => [],
    'Educational' => [],
    'Music' => [],
    'Other' => []
];

foreach ($games as $game) {
    $category = $game['categories'][0] ?? 'Other';
    if (!isset($categories[$category])) {
        $categories[$category] = [];
    }
    $categories[$category][] = $game;
}

// 获取特色游戏（前6个）
$featuredGames = array_slice($games, 0, 6);

// 获取最新游戏（按CSV顺序，前12个）
$latestGames = array_slice($games, 0, 12);

// 获取热门游戏（随机选择12个）
$popularGames = array_slice(shuffle($games) ? $games : $games, 0, 12);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎮 Sonice Online Games - 免费在线游戏平台</title>
    <meta name="description" content="在Sonice.Games玩免费在线游戏 - 最佳浏览器游戏、HTML5游戏和免费在线游戏集合">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%);
            color: #ffffff;
        }
        .game-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .game-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        .hero-section {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.9) 0%, rgba(37, 99, 235, 0.9) 50%, rgba(96, 165, 250, 0.9) 100%);
        }
        .category-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .category-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-30 bg-black bg-opacity-50 backdrop-blur-sm border-b border-gray-700">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <!-- Logo -->
            <a href="original-home.php" class="flex items-center space-x-2">
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                    <span class="text-white text-xl font-bold">🎮</span>
                </div>
                <span class="text-2xl font-bold text-white">Sonice<span class="text-blue-400">.Games</span></span>
            </a>
            
            <!-- Search Bar -->
            <div class="flex-1 max-w-2xl mx-8">
                <div class="relative">
                    <form id="searchForm" class="relative">
                        <input type="search" id="searchInput" placeholder="搜索游戏..." class="w-full px-5 py-2 bg-[#233a6b] border-none rounded-full text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent shadow-inner">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-blue-500 hover:bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex items-center space-x-4">
                <a href="original-home.php" class="text-white hover:text-blue-300 transition">首页</a>
                <a href="test-game.php" class="text-white hover:text-blue-300 transition">游戏列表</a>
                <a href="system_check.php" class="text-white hover:text-blue-300 transition">系统</a>
                <a href="../" class="text-white hover:text-blue-300 transition">返回</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section pt-24 pb-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
                欢迎来到 <span class="text-blue-300">Sonice.Games</span>
            </h1>
            <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
                发现并享受最好的免费在线游戏集合，包括动作、策略、益智等各类游戏
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="#featured-games" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-full text-lg font-semibold transition">
                    开始游戏
                </a>
                <a href="test-game.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-8 py-3 rounded-full text-lg font-semibold transition border border-white border-opacity-30">
                    浏览全部
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Games -->
    <section id="featured-games" class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-white text-center mb-12">
                <span class="text-blue-300">🌟</span> 特色游戏
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <?php foreach ($featuredGames as $game): ?>
                    <div class="game-card bg-white bg-opacity-10 backdrop-blur-sm rounded-lg overflow-hidden border border-white border-opacity-20">
                        <a href="game.php?slug=<?php echo urlencode($game['slug']); ?>" class="block">
                            <div class="aspect-video bg-gray-800 relative">
                                <?php
                                $imgPath = __DIR__ . '/assets/images/games/' . $game['slug'] . '.webp';
                                $imgPathPng = __DIR__ . '/assets/images/games/' . $game['slug'] . '.png';
                                $imgPathJpg = __DIR__ . '/assets/images/games/' . $game['slug'] . '.jpg';
                                
                                if (file_exists($imgPath)) {
                                    echo '<img src="assets/images/games/' . $game['slug'] . '.webp" alt="' . htmlspecialchars($game['title']) . '" class="w-full h-full object-cover">';
                                } elseif (file_exists($imgPathPng)) {
                                    echo '<img src="assets/images/games/' . $game['slug'] . '.png" alt="' . htmlspecialchars($game['title']) . '" class="w-full h-full object-cover">';
                                } elseif (file_exists($imgPathJpg)) {
                                    echo '<img src="assets/images/games/' . $game['slug'] . '.jpg" alt="' . htmlspecialchars($game['title']) . '" class="w-full h-full object-cover">';
                                } else {
                                    echo '<div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs text-center p-2">' . htmlspecialchars(substr($game['title'], 0, 20)) . '</div>';
                                }
                                ?>
                            </div>
                            <div class="p-3">
                                <h3 class="text-white text-sm font-medium truncate" title="<?php echo htmlspecialchars($game['title']); ?>">
                                    <?php echo htmlspecialchars($game['title']); ?>
                                </h3>
                                <p class="text-gray-300 text-xs mt-1">
                                    <?php echo implode(', ', array_slice($game['categories'], 0, 2)); ?>
                                </p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Game Categories -->
    <section class="py-16 bg-black bg-opacity-20">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-white text-center mb-12">
                <span class="text-blue-300">🎯</span> 游戏分类
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                <?php foreach ($categories as $categoryName => $categoryGames): ?>
                    <?php if (count($categoryGames) > 0): ?>
                        <a href="test-game.php#<?php echo strtolower($categoryName); ?>" class="block">
                            <div class="category-card rounded-lg p-6 text-center transition-all duration-300">
                                <div class="text-4xl mb-3">
                                    <?php
                                    $icons = [
                                        'Action' => '⚔️', 'Racing' => '🏎️', 'Sports' => '⚽', 'Shooter' => '🎯',
                                        'Cards' => '🃏', 'Adventure' => '🗺️', 'Puzzle' => '🧩', 'Strategy' => '♟️',
                                        'Mining' => '⛏️', 'Idle' => '⏰', 'Clicker' => '🖱️', 'Simulation' => '⚙️',
                                        'Tycoon' => '🏢', 'Arcade' => '🎮', 'Board' => '🎲', 'Multiplayer' => '👥',
                                        'IO' => '🌐', 'Platformer' => '🦘', 'Educational' => '📚', 'Music' => '🎵'
                                    ];
                                    echo $icons[$categoryName] ?? '🎮';
                                    ?>
                                </div>
                                <h3 class="text-white font-semibold text-lg mb-2"><?php echo $categoryName; ?></h3>
                                <p class="text-blue-200 text-sm"><?php echo count($categoryGames); ?> 个游戏</p>
                            </div>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Latest Games -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-white text-center mb-12">
                <span class="text-blue-300">🆕</span> 最新游戏
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                <?php foreach ($latestGames as $game): ?>
                    <div class="game-card bg-white bg-opacity-10 backdrop-blur-sm rounded-lg overflow-hidden border border-white border-opacity-20">
                        <a href="game.php?slug=<?php echo urlencode($game['slug']); ?>" class="block">
                            <div class="aspect-video bg-gray-800 relative">
                                <?php
                                $imgPath = __DIR__ . '/assets/images/games/' . $game['slug'] . '.webp';
                                $imgPathPng = __DIR__ . '/assets/images/games/' . $game['slug'] . '.png';
                                $imgPathJpg = __DIR__ . '/assets/images/games/' . $game['slug'] . '.jpg';
                                
                                if (file_exists($imgPath)) {
                                    echo '<img src="assets/images/games/' . $game['slug'] . '.webp" alt="' . htmlspecialchars($game['title']) . '" class="w-full h-full object-cover">';
                                } elseif (file_exists($imgPathPng)) {
                                    echo '<img src="assets/images/games/' . $game['slug'] . '.png" alt="' . htmlspecialchars($game['title']) . '" class="w-full h-full object-cover">';
                                } elseif (file_exists($imgPathJpg)) {
                                    echo '<img src="assets/images/games/' . $game['slug'] . '.jpg" alt="' . htmlspecialchars($game['title']) . '" class="w-full h-full object-cover">';
                                } else {
                                    echo '<div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs text-center p-2">' . htmlspecialchars(substr($game['title'], 0, 20)) . '</div>';
                                }
                                ?>
                            </div>
                            <div class="p-3">
                                <h3 class="text-white text-sm font-medium truncate" title="<?php echo htmlspecialchars($game['title']); ?>">
                                    <?php echo htmlspecialchars($game['title']); ?>
                                </h3>
                                <p class="text-gray-300 text-xs mt-1">
                                    <?php echo implode(', ', array_slice($game['categories'], 0, 2)); ?>
                                </p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-black bg-opacity-20">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold text-blue-300 mb-2"><?php echo count($games); ?>+</div>
                    <div class="text-white">免费游戏</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-green-300 mb-2"><?php echo count(array_filter($categories, function($cat) { return count($cat) > 0; })); ?></div>
                    <div class="text-white">游戏分类</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-purple-300 mb-2">24/7</div>
                    <div class="text-white">在线服务</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-yellow-300 mb-2">100%</div>
                    <div class="text-white">免费游玩</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black bg-opacity-50 backdrop-blur-sm border-t border-gray-700">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold text-white mb-4">🎮 Sonice.Games</h3>
                    <p class="text-gray-300 text-sm">
                        最佳免费在线游戏平台，提供各种类型的浏览器游戏和HTML5游戏。
                    </p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">快速链接</h4>
                    <ul class="space-y-2">
                        <li><a href="original-home.php" class="text-gray-300 hover:text-white transition">首页</a></li>
                        <li><a href="test-game.php" class="text-gray-300 hover:text-white transition">游戏列表</a></li>
                        <li><a href="system_check.php" class="text-gray-300 hover:text-white transition">系统检查</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">支持</h4>
                    <ul class="space-y-2">
                        <li><a href="status_report.php" class="text-gray-300 hover:text-white transition">状态报告</a></li>
                        <li><a href="../" class="text-gray-300 hover:text-white transition">返回主页</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">联系我们</h4>
                    <p class="text-gray-300 text-sm">
                        有任何问题或建议？<br>
                        请通过系统工具联系我们。
                    </p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-300">&copy; 2024 Sonice.Games - 免费在线游戏平台</p>
            </div>
        </div>
    </footer>

    <script>
        // 搜索功能
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const query = document.getElementById('searchInput').value.trim();
            if (query) {
                // 跳转到游戏列表页面并传递搜索参数
                window.location.href = `test-game.php?search=${encodeURIComponent(query)}`;
            }
        });

        // 平滑滚动
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html> 