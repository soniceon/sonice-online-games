<?php
// 游戏详情页面
require_once __DIR__ . '/../config/database.php';

// 获取游戏slug
$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('Location: test-game.php');
    exit;
}

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
            $games[] = $game;
        }
        fclose($handle);
    }
    return $games;
}

$csvFile = __DIR__ . '/../游戏iframe.CSV';
$games = load_games_from_csv($csvFile);

// 查找当前游戏
$currentGame = null;
foreach ($games as $game) {
    if ($game['slug'] === $slug) {
        $currentGame = $game;
        break;
    }
}

if (!$currentGame) {
    header('Location: test-game.php');
    exit;
}

// 获取相关游戏推荐
$relatedGames = [];
foreach ($games as $game) {
    if ($game['slug'] !== $slug && count($relatedGames) < 5) {
        // 检查是否有共同分类
        $commonCategories = array_intersect($currentGame['categories'], $game['categories']);
        if (!empty($commonCategories)) {
            $relatedGames[] = $game;
        }
    }
}

// 如果没有相关游戏，随机选择
if (empty($relatedGames)) {
    $randomGames = array_filter($games, function($game) use ($slug) {
        return $game['slug'] !== $slug;
    });
    $relatedGames = array_slice(array_values($randomGames), 0, 5);
}

// 按分类组织游戏（用于侧边栏）
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($currentGame['title']); ?> - Sonice Online Games</title>
    <meta name="description" content="Play <?php echo htmlspecialchars($currentGame['title']); ?> online for free at Sonice.Games">
    <meta name="keywords" content="<?php echo htmlspecialchars($currentGame['title']); ?>, online games, free games, browser games">
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-C6DQJE930Z"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-C6DQJE930Z');
    </script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%);
            color: white;
        }
        .content-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            margin-top: 20px;
        }
        .main-content {
            flex: 1;
            padding: 24px;
        }
        .game-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .title-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 32px;
        }
        .game-wrapper {
            background: rgba(0,0,0,0.5);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            width: 100%;
            padding-bottom: 56.25%;
            margin: 2rem 0;
        }
        .game-frame {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 12px;
        }
        .glass-container {
            background: rgba(59, 130, 246, 0.8);
            border-radius: 12px;
            padding: 24px;
        }
        .game-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .game-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        .bg-blue-primary { background-color: #0EA5E9; }
        .bg-blue-secondary { background-color: #0284C7; }
        .bg-blue-600\/80 { background-color: rgba(37, 99, 235, 0.8); }
        .bg-blue-600\/40 { background-color: rgba(37, 99, 235, 0.4); }
        .bg-blue-500\/30 { background-color: rgba(59, 130, 246, 0.3); }
        .bg-white\/10 { background-color: rgba(255, 255, 255, 0.1); }
        .bg-white\/20 { background-color: rgba(255, 255, 255, 0.2); }
        .border-blue-500\/30 { border-color: rgba(59, 130, 246, 0.3); }
        .text-blue-100 { color: #DBEAFE; }
        .text-blue-200 { color: #BFDBFE; }
        .text-blue-300 { color: #93C5FD; }
        .text-white\/70 { color: rgba(255, 255, 255, 0.7); }
        .text-white\/90 { color: rgba(255, 255, 255, 0.9); }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .sidebar-blue { background-color: #152a69; }
        .sidebar-hover { background-color: #1d3a8f; }
        .ml-14 { margin-left: 3.5rem; }
        .ml-56 { margin-left: 14rem; }
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%) !important;
            color: #ffffff;
        }
        .content-wrapper {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
        .gradient-blue {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
    </style>
</head>
<body>
<!-- Header -->
<header class="fixed top-0 left-0 right-0 z-30 bg-black bg-opacity-90 backdrop-blur-sm border-b border-gray-800">
    <div class="container mx-auto px-4 h-16 flex items-center justify-between">
        <!-- Logo -->
        <a href="../index.php" class="flex items-center space-x-2">
            <img src="assets/images/icons/logo.png" alt="Sonice.Games" class="h-10 w-10 rounded-full object-cover">
            <span class="text-2xl font-bold text-white">Sonice<span class="text-blue-500">.Games</span></span>
        </a>
        <!-- Search Bar -->
        <div class="flex-1 max-w-2xl mx-8">
            <div class="relative">
                <form id="searchForm" action="test-game.php" method="get" class="relative">
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
                    <a href="../index.php" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                        <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                            <i class="fa-solid fa-home text-2xl" style="color:#3b82f6;"></i>
                        </span>
                        <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Home</span>
                    </a>
                </li>
                <!-- Favorites -->
                <li>
                    <a href="test-game.php#favorites" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                        <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                            <i class="fa-solid fa-heart text-2xl" style="color:#ef476f;"></i>
                        </span>
                        <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Favorites</span>
                    </a>
                </li>
                <!-- Recently Played -->
                <li>
                    <a href="test-game.php#recent" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
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
                        <a href="../index.php#<?= $category['slug'] ?>" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
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
                <a href="../index.php" class="flex items-center justify-center mb-2">
                    <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;">
                        <img src="assets/images/icons/logo.png" alt="Sonice Games" class="w-8 h-8 transition-all duration-200" />
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
                                <li><a href="about.php" class="hover:text-blue-300 text-gray-300">About Us</a></li>
                                <li><a href="contact.php" class="hover:text-blue-300 text-gray-300">Contact</a></li>
                                <li><a href="privacy.php" class="hover:text-blue-300 text-gray-300">Privacy Policy</a></li>
                                <li><a href="terms.php" class="hover:text-blue-300 text-gray-300">Terms of Service</a></li>
                                <li><a href="system_check.php" class="hover:text-blue-300 text-gray-300">System Check</a></li>
                                <li><a href="status_report.php" class="hover:text-blue-300 text-gray-300">Status Report</a></li>
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
                    <div class="content-wrapper">
                        <div class="flex justify-center">
                            <main class="main-content">
      <div class="game-container">
        <div class="mb-6">
          <a href="test-game.php" class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
            ← Back to Game List
          </a>
        </div>
        
        <div class="title-section">
          <div class="inline-block px-8 py-2 rounded-xl font-bold text-3xl md:text-4xl text-white bg-blue-400/90 shadow-lg mb-3" style="box-shadow: 0 4px 16px rgba(14,165,233,0.15);"><?php echo htmlspecialchars($currentGame['title']); ?></div>
          <div class="flex flex-wrap justify-center gap-2 mb-3">
            <?php foreach ($currentGame['categories'] as $category): ?>
              <span class="inline-block px-4 py-1 rounded-full text-white text-base font-semibold bg-purple-500/90 shadow" style="box-shadow: 0 2px 8px rgba(124,58,237,0.15);"><?php echo htmlspecialchars($category); ?></span>
            <?php endforeach; ?>
          </div>
        </div>
        
        <div class="game-wrapper">
          <iframe id="game-frame" src="<?php echo htmlspecialchars($currentGame['iframe_url']); ?>" class="game-frame" allowfullscreen></iframe>
        </div>
        
        <div class="flex justify-between items-center mb-6">
          <div class="flex space-x-3">
            <button id="fullscreen-btn" class="bg-blue-primary hover:bg-blue-secondary text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
              <i class="fas fa-expand"></i>
              <span>Fullscreen</span>
            </button>
            <button id="favorite-btn" class="favorite-btn bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
              <i class="fas fa-heart"></i>
              <span>Add to Favorites</span>
            </button>
          </div>
          <button id="report-btn" class="text-gray-400 hover:text-white flex items-center space-x-1">
            <i class="fas fa-flag"></i>
            <span>Report Issue</span>
          </button>
        </div>
        
        <!-- Controls section -->
        <div class="bg-blue-600/80 p-6 rounded-lg mb-6">
          <h2 class="text-2xl font-bold mb-4 text-white">Key Controls</h2>
          <ul class="text-white space-y-2 pl-4">
            <li class="flex items-start"><span class="mr-2">•</span><span>Click - Click to earn points or currency</span></li>
            <li class="flex items-start"><span class="mr-2">•</span><span>Number Keys or Letter Keys - Purchase upgrades or activate special abilities</span></li>
            <li class="flex items-start"><span class="mr-2">•</span><span>Left/Right arrow keys - Navigate through menus (in some games)</span></li>
            <li class="flex items-start"><span class="mr-2">•</span><span>Spacebar - Pause game or activate primary function</span></li>
            <li class="flex items-start"><span class="mr-2">•</span><span>Z, X, C - Special actions or combo moves</span></li>
          </ul>
        </div>
        
        <!-- Overview section -->
        <div class="bg-blue-600/80 p-6 rounded-lg mb-6">
          <h2 class="text-2xl font-bold mb-4 text-white">Game Overview</h2>
          <p class="text-white leading-relaxed mb-4">This is a clicker/incremental game where you start small and gradually build up your resources. Click to earn points, invest in upgrades, and watch your earnings grow exponentially. The game features automatic income generation, various upgrades and achievements to unlock, and increasingly rewarding milestones to reach.</p>
          <p class="text-white leading-relaxed mb-6">As you progress, you'll unlock new features and mechanics that add depth to the gameplay. Strategize your investments to maximize your earnings and compete for the highest scores. Special events and bonuses appear randomly to boost your progress!</p>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-white/10 p-4 rounded-lg">
              <h3 class="text-lg font-semibold mb-2 text-white">Key Features</h3>
              <ul class="text-white space-y-1 pl-4">
                <li class="flex items-start"><span class="mr-2">•</span><span>Auto-clickers and passive income generators</span></li>
                <li class="flex items-start"><span class="mr-2">•</span><span>Multiple upgrade paths and strategies</span></li>
                <li class="flex items-start"><span class="mr-2">•</span><span>Achievement system with rewards</span></li>
              </ul>
            </div>
            <div class="bg-white/10 p-4 rounded-lg">
              <h3 class="text-lg font-semibold mb-2 text-white">Game Progress</h3>
              <ul class="text-white space-y-1 pl-4">
                <li class="flex items-start"><span class="mr-2">•</span><span>Automatic saving of your progress</span></li>
                <li class="flex items-start"><span class="mr-2">•</span><span>Prestige system for advanced players</span></li>
                <li class="flex items-start"><span class="mr-2">•</span><span>Special weekend events with bonus rewards</span></li>
              </ul>
            </div>
          </div>
          <div class="flex flex-wrap gap-2">
            <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white">Clicker</span>
            <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white">Incremental</span>
            <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white">Idle</span>
            <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white">Strategy</span>
            <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white">Resource Management</span>
            <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white">Casual</span>
          </div>
        </div>
        
        <!-- Recommendations section -->
        <div class="game-container glass-container mt-12">
          <div class="recommended-games mb-10">
            <h2 class="text-2xl font-bold mb-4 text-white">You May Also Like</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
              <?php if (!empty($relatedGames)): ?>
                <?php foreach ($relatedGames as $related): ?>
                <div class="game-card bg-blue-700/60 rounded-xl overflow-hidden shadow-lg flex flex-col">
                  <a href="game.php?slug=<?php echo urlencode($related['slug']); ?>" class="block">
                    <img src="assets/images/games/<?php echo htmlspecialchars($related['slug']); ?>.webp"
                         alt="<?php echo htmlspecialchars($related['title']); ?>"
                         class="w-full h-auto rounded-xl"
                         loading="lazy"
                         onerror="this.onerror=null;this.src='assets/images/defaults/game-default.webp';">
                  </a>
                </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="text-white/70">No related games found.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <!-- Rating section -->
        <div class="rating-section bg-blue-600 p-6 rounded-lg text-center mt-8">
          <h2 class="text-2xl font-bold mb-4 text-white">Rate This Game</h2>
          <div class="rating-stars flex justify-center space-x-4 mb-4">
            <span class="star text-4xl cursor-pointer text-white opacity-70 hover:opacity-100" data-rating="1">★</span>
            <span class="star text-4xl cursor-pointer text-white opacity-70 hover:opacity-100" data-rating="2">★</span>
            <span class="star text-4xl cursor-pointer text-white opacity-70 hover:opacity-100" data-rating="3">★</span>
            <span class="star text-4xl cursor-pointer text-white opacity-70 hover:opacity-100" data-rating="4">★</span>
            <span class="star text-4xl cursor-pointer text-white opacity-70 hover:opacity-100" data-rating="5">★</span>
          </div>
          <div class="rating-count text-white">
            Average Rating: <span id="avgRating">4.5</span>/5
            (<span id="ratingCount">128</span> votes)
          </div>
        </div>
      </div>
    </main>
  </div>
                                </div>
                            </main>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

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

<!-- Comments Section -->
<div class="game-container glass-container mt-12">
  <div class="comments-section">
    <h2 class="text-2xl font-bold mb-6 text-white">Comments</h2>
    
    <!-- Comment Form -->
    <div class="bg-blue-600/80 p-6 rounded-lg mb-8">
      <h3 class="text-xl font-bold mb-4 text-white flex items-center">
        <i class="fas fa-comment-dots mr-2"></i>
        Leave a Comment
      </h3>
      
      <form id="commentForm" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="commentName" class="block text-sm font-medium text-white mb-2">Name</label>
            <input 
              type="text" 
              id="commentName" 
              name="name" 
              required 
              class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
              placeholder="Enter your name"
            >
          </div>
          <div>
            <label for="commentEmail" class="block text-sm font-medium text-white mb-2">Email</label>
            <input 
              type="email" 
              id="commentEmail" 
              name="email" 
              required 
              class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
              placeholder="Enter your email"
            >
          </div>
        </div>
        
        <div>
          <label for="commentContent" class="block text-sm font-medium text-white mb-2">Content</label>
          <textarea 
            id="commentContent" 
            name="content" 
            rows="4" 
            required 
            class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent resize-none"
            placeholder="Share your thoughts about this game..."
          ></textarea>
        </div>
        
        <div class="flex items-center">
          <input 
            type="checkbox" 
            id="commentTerms" 
            name="terms" 
            required 
            class="w-4 h-4 text-blue-600 bg-white/10 border-white/20 rounded focus:ring-blue-400 focus:ring-2"
          >
          <label for="commentTerms" class="ml-2 text-sm text-white">
            I'd read and agree to the <a href="terms.php" class="text-blue-300 hover:underline">terms and conditions</a>.
          </label>
        </div>
        
        <button 
          type="submit" 
          class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105"
        >
          Comment
        </button>
      </form>
    </div>
    
    <!-- Comments List -->
    <div id="commentsList" class="space-y-4">
      <!-- Sample comment for demonstration -->
      <div class="bg-blue-600/40 p-4 rounded-lg border border-blue-500/30">
        <div class="flex items-start justify-between mb-2">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
              <span class="text-white font-bold text-sm">JD</span>
            </div>
            <div>
              <h4 class="text-white font-semibold">John Doe</h4>
              <p class="text-blue-200 text-sm">2 hours ago</p>
            </div>
          </div>
          <div class="flex items-center space-x-2">
            <button class="text-blue-300 hover:text-white transition" title="Like">
              <i class="fas fa-thumbs-up"></i>
            </button>
            <button class="text-blue-300 hover:text-white transition" title="Reply">
              <i class="fas fa-reply"></i>
            </button>
          </div>
        </div>
        <p class="text-white/90 leading-relaxed">
          This game is absolutely amazing! The graphics are stunning and the gameplay is so addictive. 
          I've been playing for hours and can't get enough. Highly recommend to everyone!
        </p>
      </div>
      
      <div class="bg-blue-600/40 p-4 rounded-lg border border-blue-500/30">
        <div class="flex items-center space-x-3 mb-2">
          <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
            <span class="text-white font-bold text-sm">SG</span>
          </div>
          <div>
            <h4 class="text-white font-semibold">Sarah Green</h4>
            <p class="text-blue-200 text-sm">1 day ago</p>
          </div>
        </div>
        <p class="text-white/90 leading-relaxed">
          Great idle game! Love the progression system and the different upgrades available. 
          The art style is really cute too.
        </p>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // loading overlay
  var iframe = document.getElementById('game-frame');
  var overlay = document.getElementById('game-loading-overlay');
  if (iframe && overlay) {
    var hideOverlay = function() {
      overlay.style.display = 'none';
    };
    iframe.onload = hideOverlay;
    setTimeout(hideOverlay, 10000);
  }

  // ===== Fullscreen functionality =====
  const fullscreenBtn = document.getElementById('fullscreen-btn');
  const gameFrame = document.getElementById('game-frame');
  if (fullscreenBtn && gameFrame) {
    fullscreenBtn.onclick = function() {
      if (gameFrame.requestFullscreen) {
        gameFrame.requestFullscreen();
      } else if (gameFrame.webkitRequestFullscreen) {
        gameFrame.webkitRequestFullscreen();
      } else if (gameFrame.mozRequestFullScreen) {
        gameFrame.mozRequestFullScreen();
      } else if (gameFrame.msRequestFullscreen) {
        gameFrame.msRequestFullscreen();
      } else {
        alert('Current browser does not support fullscreen API');
      }
    };
  }

  // ===== Favorites functionality =====
  const favoriteBtn = document.getElementById('favorite-btn');
  if (favoriteBtn) {
    const gameId = '<?php echo htmlspecialchars($currentGame['slug']); ?>';
    const gameSlug = '<?php echo htmlspecialchars($currentGame['slug']); ?>';
    function updateFavoriteBtn() {
      const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
      const isFav = favorites.some(fav => fav.id === gameId || fav.slug === gameSlug);
      if (isFav) {
        favoriteBtn.classList.add('is-favorite');
        favoriteBtn.querySelector('span').textContent = 'Remove from Favorites';
      } else {
        favoriteBtn.classList.remove('is-favorite');
        favoriteBtn.querySelector('span').textContent = 'Add to Favorites';
      }
    }
    updateFavoriteBtn();
    favoriteBtn.onclick = function() {
      let favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
      const idx = favorites.findIndex(fav => fav.id === gameId || fav.slug === gameSlug);
      if (idx === -1) {
        favorites.push({id: gameId, slug: gameSlug, title: document.title, addedAt: new Date().toISOString()});
        localStorage.setItem('favorites', JSON.stringify(favorites));
        showToast('Added to favorites', 'success');
      } else {
        favorites.splice(idx, 1);
        localStorage.setItem('favorites', JSON.stringify(favorites));
        showToast('Removed from favorites', 'success');
      }
      updateFavoriteBtn();
    };
  }

  // ===== Report functionality =====
  const reportBtn = document.getElementById('report-btn');
  if (reportBtn) {
    reportBtn.onclick = function() {
      let modal = document.createElement('div');
      modal.style.position = 'fixed';
      modal.style.left = '0';
      modal.style.top = '0';
      modal.style.width = '100vw';
      modal.style.height = '100vh';
      modal.style.background = 'rgba(0,0,0,0.6)';
      modal.style.display = 'flex';
      modal.style.alignItems = 'center';
      modal.style.justifyContent = 'center';
      modal.style.zIndex = '9999';
      modal.innerHTML = `
        <div style="background:#222;padding:2rem;border-radius:12px;max-width:90vw;width:350px;box-shadow:0 8px 32px #000a;position:relative;">
          <button id="closeReportModal" style="position:absolute;top:8px;right:12px;font-size:1.2rem;background:none;border:none;color:#fff;">×</button>
          <h2 style="color:#fff;font-size:1.2rem;margin-bottom:1rem;">Report Issue</h2>
          <form id="reportForm">
            <label style="color:#fff;">Type:</label>
            <select id="reportType" style="width:100%;margin-bottom:1rem;">
              <option value="broken">Cannot Load</option>
<option value="inappropriate">Inappropriate Content</option>
<option value="performance">Performance Issue</option>
<option value="other">Other</option>
            </select>
            <label style="color:#fff;">Description:</label>
            <textarea id="reportDesc" style="width:100%;height:60px;margin-bottom:1rem;"></textarea>
            <button type="submit" style="width:100%;background:#0EA5E9;color:#fff;padding:0.5rem 0;border:none;border-radius:6px;">Submit</button>
          </form>
        </div>
      `;
      document.body.appendChild(modal);
      document.getElementById('closeReportModal').onclick = () => modal.remove();
      modal.onclick = e => { if (e.target === modal) modal.remove(); };
      document.getElementById('reportForm').onsubmit = function(e) {
        e.preventDefault();
        const type = document.getElementById('reportType').value;
        const desc = document.getElementById('reportDesc').value.trim();
        if (!desc) {
          showToast('Please fill in the issue description', 'error');
          return;
        }
        showToast('Report submitted, thank you for your feedback!', 'success');
        modal.remove();
      };
    };
  }

  // ===== Rating functionality =====
  const stars = document.querySelectorAll('.star');
  let userRating = 0;
  stars.forEach(star => {
    star.addEventListener('mouseover', () => {
      const rating = parseInt(star.dataset.rating);
      highlightStars(rating);
    });
    star.addEventListener('mouseout', () => {
      highlightStars(userRating);
    });
    star.addEventListener('click', () => {
      userRating = parseInt(star.dataset.rating);
      highlightStars(userRating);
      saveRating(userRating);
    });
  });
  function highlightStars(rating) {
    stars.forEach(star => {
      const starRating = parseInt(star.dataset.rating);
      if (starRating <= rating) {
        star.classList.add('text-white');
        star.classList.remove('opacity-70');
        star.classList.add('opacity-100');
      } else {
        star.classList.add('opacity-70');
        star.classList.remove('opacity-100');
      }
    });
  }
  function saveRating(rating) {
    const avgRatingEl = document.getElementById('avgRating');
    const ratingCountEl = document.getElementById('ratingCount');
    let currentCount = parseInt(ratingCountEl.textContent);
    let currentAvg = parseFloat(avgRatingEl.textContent);
    const newCount = currentCount + 1;
    const newAvg = ((currentAvg * currentCount) + rating) / newCount;
    avgRatingEl.textContent = newAvg.toFixed(1);
    ratingCountEl.textContent = newCount;
    showToast('Thank you for your rating!', 'success');
  }

  // ===== Comment functionality =====
  const commentForm = document.getElementById('commentForm');
  if (commentForm) {
    commentForm.onsubmit = function(e) {
      e.preventDefault();
      
      const name = document.getElementById('commentName').value.trim();
      const email = document.getElementById('commentEmail').value.trim();
      const content = document.getElementById('commentContent').value.trim();
      const terms = document.getElementById('commentTerms').checked;
      
      if (!name || !email || !content || !terms) {
        showToast('Please fill in all fields and agree to terms', 'error');
        return;
      }
      
      if (!/^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,}$/.test(email)) {
        showToast('Please enter a valid email address', 'error');
        return;
      }
      
      // Add new comment to the list
      addComment(name, content);
      
      // Reset form
      commentForm.reset();
      
      showToast('Comment submitted successfully!', 'success');
    };
  }
  
  function addComment(name, content) {
    const commentsList = document.getElementById('commentsList');
    const initials = name.split(' ').map(n => n[0]).join('').toUpperCase();
    const colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-pink-500', 'bg-yellow-500'];
    const randomColor = colors[Math.floor(Math.random() * colors.length)];
    
    const commentHTML = `
      <div class="bg-blue-600/40 p-4 rounded-lg border border-blue-500/30 animate-fade-in">
        <div class="flex items-start justify-between mb-2">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 ${randomColor} rounded-full flex items-center justify-center">
              <span class="text-white font-bold text-sm">${initials}</span>
            </div>
            <div>
              <h4 class="text-white font-semibold">${name}</h4>
              <p class="text-blue-200 text-sm">Just now</p>
            </div>
          </div>
          <div class="flex items-center space-x-2">
            <button class="text-blue-300 hover:text-white transition" title="Like">
              <i class="fas fa-thumbs-up"></i>
            </button>
            <button class="text-blue-300 hover:text-white transition" title="Reply">
              <i class="fas fa-reply"></i>
            </button>
          </div>
        </div>
        <p class="text-white/90 leading-relaxed">${content}</p>
      </div>
    `;
    
    // Insert new comment at the top
    commentsList.insertAdjacentHTML('afterbegin', commentHTML);
  }

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
            if (q) window.location.href = 'test-game.php?search=' + encodeURIComponent(q);
        };

  // ===== Toast general notifications =====
  function showToast(msg, type) {
    let toast = document.getElementById('toast');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'toast';
      toast.style.position = 'fixed';
      toast.style.top = '30px';
      toast.style.right = '30px';
      toast.style.zIndex = '99999';
      toast.style.padding = '12px 20px';
      toast.style.borderRadius = '8px';
      toast.style.color = '#fff';
      toast.style.fontWeight = 'bold';
      toast.style.fontSize = '1rem';
      toast.style.boxShadow = '0 2px 8px #0004';
      document.body.appendChild(toast);
    }
    toast.style.background = type === 'error' ? '#EF4444' : '#0EA5E9';
    toast.textContent = msg;
    toast.style.opacity = '1';
    toast.style.display = 'block';
    setTimeout(() => {
      toast.style.opacity = '0';
      setTimeout(() => { toast.style.display = 'none'; }, 400);
    }, 2000);
  }
});
</script>

<style>
/* Comment animations */
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}

/* Comment form styling */
#commentForm input:focus,
#commentForm textarea:focus {
  background-color: rgba(255, 255, 255, 0.15);
  border-color: rgba(59, 130, 246, 0.5);
}

#commentForm button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
}

/* Comment list styling */
.comments-section .bg-blue-600\/40 {
  transition: all 0.2s ease;
}

.comments-section .bg-blue-600\/40:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

/* Responsive design for comments */
@media (max-width: 768px) {
  .comments-section .grid {
    grid-template-columns: 1fr;
  }
  
  .comments-section .bg-blue-600\/80 {
    padding: 1rem;
  }
}
</style>
</body>
</html> 