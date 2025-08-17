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
    if ($game['slug'] !== $slug && count($relatedGames) < 6) {
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
    $relatedGames = array_slice(array_values($randomGames), 0, 6);
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($currentGame['title']); ?> - Sonice.Games</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%);
            color: #ffffff;
        }
        .game-iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 8px;
        }
        .game-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .game-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Header -->
    <header class="bg-black bg-opacity-50 backdrop-blur-sm border-b border-gray-700">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="test-game.php" class="text-blue-300 hover:text-blue-200">
                        ← 返回游戏列表
                    </a>
                    <h1 class="text-2xl font-bold text-white">
                        🎮 <?php echo htmlspecialchars($currentGame['title']); ?>
                    </h1>
                </div>
                <div class="text-white">
                    <span class="text-sm">分类: <?php echo implode(', ', $currentGame['categories']); ?></span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- 游戏区域 -->
            <div class="lg:col-span-2">
                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                    <h2 class="text-xl font-bold text-white mb-4">🎮 开始游戏</h2>
                    <div class="aspect-video bg-gray-800 rounded-lg overflow-hidden">
                        <?php if (!empty($currentGame['iframe_url']) && preg_match('#^https?://#', $currentGame['iframe_url'])): ?>
                            <iframe 
                                src="<?php echo htmlspecialchars($currentGame['iframe_url']); ?>" 
                                class="game-iframe"
                                allowfullscreen
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            ></iframe>
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-center p-8">
                                <div>
                                    <div class="text-4xl mb-4">🎮</div>
                                    <div class="text-xl font-bold mb-2">游戏加载中...</div>
                                    <div class="text-sm opacity-75">如果游戏没有显示，请检查网络连接</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- 游戏信息 -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-white mb-3">游戏信息</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-300">游戏名称:</span>
                                <span class="text-white ml-2"><?php echo htmlspecialchars($currentGame['title']); ?></span>
                            </div>
                            <div>
                                <span class="text-gray-300">游戏分类:</span>
                                <span class="text-white ml-2"><?php echo implode(', ', $currentGame['categories']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 侧边栏 -->
            <div class="lg:col-span-1">
                <!-- 相关游戏推荐 -->
                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-white mb-4">🎯 相关游戏推荐</h3>
                    <div class="space-y-3">
                        <?php foreach ($relatedGames as $game): ?>
                            <a href="game.php?slug=<?php echo urlencode($game['slug']); ?>" class="block">
                                <div class="game-card bg-white bg-opacity-5 rounded-lg p-3 hover:bg-opacity-10 transition-all">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-16 h-12 bg-gray-700 rounded overflow-hidden flex-shrink-0">
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
                                                echo '<div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs">' . htmlspecialchars(substr($game['title'], 0, 10)) . '</div>';
                                            }
                                            ?>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-white text-sm font-medium truncate" title="<?php echo htmlspecialchars($game['title']); ?>">
                                                <?php echo htmlspecialchars($game['title']); ?>
                                            </h4>
                                            <p class="text-gray-300 text-xs mt-1">
                                                <?php echo implode(', ', array_slice($game['categories'], 0, 2)); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 快速操作 -->
                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-white mb-4">⚡ 快速操作</h3>
                    <div class="space-y-3">
                        <a href="test-game.php" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg transition-colors">
                            浏览更多游戏
                        </a>
                        <a href="system_check.php" class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center py-2 px-4 rounded-lg transition-colors">
                            系统检查
                        </a>
                        <a href="../" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded-lg transition-colors">
                            返回主页
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-black bg-opacity-50 backdrop-blur-sm border-t border-gray-700 mt-12">
        <div class="container mx-auto px-4 py-6">
            <div class="text-center text-gray-300">
                <p>&copy; 2024 Sonice.Games - 免费在线游戏平台</p>
                <p class="text-sm mt-2">
                    <a href="system_check.php" class="text-blue-300 hover:text-blue-200 mr-4">系统检查</a>
                    <a href="status_report.php" class="text-blue-300 hover:text-blue-200 mr-4">状态报告</a>
                    <a href="test-game.php" class="text-blue-300 hover:text-blue-200">游戏列表</a>
                </p>
            </div>
        </div>
    </footer>
</body>
</html> 