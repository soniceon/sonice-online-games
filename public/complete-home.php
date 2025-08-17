<?php
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

$categories = array_values($categories);
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
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%) !important;
            color: #ffffff;
        }
        .content-wrapper {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
        .game-card-home { 
            aspect-ratio: 16/9; 
            height: auto; 
            min-height: 100px; 
            background: transparent; 
            border-radius: 12px; 
            overflow: hidden; 
            display: flex; 
            flex-direction: column; 
            align-items: stretch; 
            justify-content: flex-start; 
        }
        .game-card-home img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            border-radius: 8px 8px 0 0; 
            background: #222; 
        }
        .category-block { 
            margin-bottom: 0.75rem; 
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
    </style>
</head>
<body class="min-h-screen flex flex-col bg-dark text-white">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-30 bg-black bg-opacity-90 backdrop-blur-sm border-b border-gray-800">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-2">
                <img src="/assets/images/icons/logo.png" alt="Sonice.Games" class="h-10 w-10 rounded-full object-cover">
                <span class="text-2xl font-bold text-white">Sonice<span class="text-blue-500">.Games</span></span>
            </a>
            <!-- Search Bar -->
            <div class="flex-1 max-w-2xl mx-8">
                <div class="relative">
                    <form id="searchForm" action="/search" method="get" class="relative">
                        <input type="search" name="q" id="searchInput" placeholder="Search games..." class="w-full px-5 py-2 bg-[#233a6b] border-none rounded-full text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent shadow-inner">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-blue-500 hover:bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
            <!-- 登录注册入口 -->
            <button id="navLoginBtn" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded">登录/注册</button>
        </div>
    </header>

    <div class="flex flex-1 min-h-0 pt-16">
        <!-- Sidebar -->
        <nav id="sidebar" class="group fixed left-0 top-16 bottom-0 h-[calc(100vh-4rem)] w-14 hover:w-56 bg-sidebar-blue flex flex-col z-20 transition-all duration-300 ease-in-out overflow-hidden">
            <div class="flex-1 py-2 overflow-y-auto" style="scrollbar-width:none; -ms-overflow-style:none; overflow-y:scroll;">
                <style>.overflow-y-auto::-webkit-scrollbar { display:none!important; width:0!important; height:0!important; background:transparent!important; }</style>
                <ul class="mt-2">
                    <!-- 首页 -->
                    <li>
                        <a href="/" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                            <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                                <i class="fa-solid fa-home text-2xl" style="color:#3b82f6;"></i>
                            </span>
                            <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Home</span>
                        </a>
                    </li>
                    <!-- 收藏 -->
                    <li>
                        <a href="/favorites" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                            <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                                <i class="fa-solid fa-heart text-2xl" style="color:#ef476f;"></i>
                            </span>
                            <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Favorites</span>
                        </a>
                    </li>
                    <!-- 最近游玩 -->
                    <li>
                        <a href="/recently-played" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                            <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                                <i class="fa-solid fa-history text-2xl" style="color:#06d6a0;"></i>
                            </span>
                            <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Recently Played</span>
                        </a>
                    </li>
                </ul>
                <!-- 分类 -->
                <div class="mt-2">
                    <h3 class="px-2 text-xs font-semibold text-gray-300 uppercase tracking-wider opacity-0 group-hover:opacity-100 transition-opacity duration-200">Categories</h3>
                    <ul class="mt-2">
                        <?php foreach ($categories as $category): ?>
                        <li>
                            <a href="/category/<?= $category['slug'] ?>" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                                <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                                    <i class="<?= $category['icon'] ?> text-2xl" style="color: <?= $category['color'] ?>;"></i>
                                </span>
                                <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap"><?= $category['name'] ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <!-- Logo和链接 -->
                <div class="w-full py-4 flex flex-col items-center justify-center gap-2">
                    <a href="/" class="flex items-center justify-center mb-2">
                        <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;">
                            <img src="/assets/images/icons/logo.png" alt="Sonice Games" class="w-8 h-8 transition-all duration-200" />
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
                                <li><a href="/about" class="hover:text-blue-300 text-gray-300">About Us</a></li>
                                <li><a href="/contact" class="hover:text-blue-300 text-gray-300">Contact</a></li>
                                <li><a href="/privacy" class="hover:text-blue-300 text-gray-300">Privacy Policy</a></li>
                                <li><a href="/terms" class="hover:text-blue-300 text-gray-300">Terms of Service</a></li>
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
                    <div class="pl-8">
                        <!-- 游戏分类 -->
                        <?php foreach ($categories as $category): ?>
                        <?php if (count($category['games']) > 0): ?>
                        <div class="category-block mb-4 group" id="cat-block-<?= $category['slug'] ?>">
                            <div class="flex items-center mb-2">
                                <h2 class="text-xl font-bold text-white mr-2 flex items-center">
                                    <span class="inline-block align-middle mr-2">
                                        <i class="<?= $category['icon'] ?>" style="color: <?= $category['color'] ?>;"></i>
                                    </span>
                                    <?= $category['name'] ?> Games
                                </h2>
                                <a href="/category/<?= $category['slug'] ?>" class="ml-2 text-blue-300 hover:text-blue-400 text-base font-medium px-2 py-0.5 rounded transition bg-blue-900/40">更多游戏</a>
                            </div>
                            <div class="relative px-2 flex items-center">
                                <button class="carousel-arrow carousel-arrow-left absolute left-0 top-1/2" aria-label="Left arrow" onclick="scrollCategoryPage('<?= $category['slug'] ?>', -1)"></button>
                                <div class="game-grid grid grid-cols-7 gap-2 w-full" id="cat-grid-<?= $category['slug'] ?>"></div>
                                <button class="carousel-arrow carousel-arrow-right absolute right-0 top-1/2" aria-label="Right arrow" onclick="scrollCategoryPage('<?= $category['slug'] ?>', 1)"></button>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- 登录弹窗 -->
    <div id="loginModal" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-60 hidden">
        <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-xs relative">
            <button id="closeLoginModal" class="absolute top-3 right-3 text-gray-400 hover:text-blue-600 text-2xl">&times;</button>
            <h2 class="text-2xl font-bold mb-6 text-center text-blue-700">登录</h2>
            <div id="loginError" class="text-red-500 text-center mb-2 hidden"></div>
            <input id="loginUsername" type="text" placeholder="用户名/邮箱" class="w-full mb-3 p-2 border rounded text-gray-900 focus:ring-2 focus:ring-blue-400" required>
            <input id="loginPassword" type="password" placeholder="密码" class="w-full mb-4 p-2 border rounded text-gray-900 focus:ring-2 focus:ring-blue-400" required>
            <button id="loginSubmit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-semibold transition">登录</button>
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

        window.categoryPages = {};
        
        function renderCategoryPage(slug) {
            const games = window.categoryGames[slug] || [];
            const page = window.categoryPages[slug] || 0;
            const grid = document.getElementById('cat-grid-' + slug);
            const leftBtn = document.querySelector('#cat-block-' + slug + ' .carousel-arrow-left');
            const rightBtn = document.querySelector('#cat-block-' + slug + ' .carousel-arrow-right');
            if (!grid) return;
            grid.innerHTML = '';
            const start = page * 7;
            const end = start + 7;
            const pageGames = games.slice(start, end);
            pageGames.forEach(game => {
                const card = document.createElement('div');
                card.className = 'game-card-home';
                card.innerHTML = `<a href="/game/${game.slug}"><img src="/assets/images/games/${game.slug}.webp" alt="${game.title}" onerror="this.src='/assets/images/defaults/game-default.webp'"></a>`;
                grid.appendChild(card);
            });
            // 箭头显示/隐藏
            if (leftBtn) leftBtn.disabled = !(page > 0);
            if (rightBtn) rightBtn.disabled = !(end < games.length);
        }
        
        function scrollCategoryPage(slug, dir) {
            const games = window.categoryGames[slug] || [];
            const maxPage = Math.floor((games.length - 1) / 7);
            window.categoryPages[slug] = (window.categoryPages[slug] || 0) + dir;
            if (window.categoryPages[slug] < 0) window.categoryPages[slug] = 0;
            if (window.categoryPages[slug] > maxPage) window.categoryPages[slug] = maxPage;
            renderCategoryPage(slug);
        }

        // 初始化
        document.addEventListener('DOMContentLoaded', function() {
            Object.keys(window.categoryGames).forEach(slug => {
                window.categoryPages[slug] = 0;
                renderCategoryPage(slug);
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
            if (q) window.location.href = '/search?q=' + encodeURIComponent(q);
        };
    </script>
</body>
</html> 