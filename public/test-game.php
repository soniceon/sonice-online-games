<?php
// 简化的游戏网站测试页面
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
    'Other' => []
];

foreach ($games as $game) {
    $category = $game['categories'][0] ?? 'Other';
    if (!isset($categories[$category])) {
        $categories[$category] = [];
    }
    $categories[$category][] = $game;
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎮 Sonice Online Games - 游戏平台</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    </style>
</head>
<body class="min-h-screen">
    <!-- Header -->
    <header class="bg-black bg-opacity-50 backdrop-blur-sm border-b border-gray-700">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-white">
                    🎮 Sonice<span class="text-blue-400">.Games</span>
                </h1>
                <div class="text-white">
                    <span class="text-sm">总游戏数: <?php echo count($games); ?></span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- 游戏分类 -->
        <?php foreach ($categories as $categoryName => $categoryGames): ?>
            <?php if (count($categoryGames) > 0): ?>
                <section class="mb-12">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <span class="mr-3">🎯</span>
                        <?php echo htmlspecialchars($categoryName); ?> Games
                        <span class="ml-3 text-blue-300 text-lg">(<?php echo count($categoryGames); ?>)</span>
                    </h2>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                        <?php foreach (array_slice($categoryGames, 0, 12) as $game): ?>
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
                    
                    <?php if (count($categoryGames) > 12): ?>
                        <div class="text-center mt-4">
                            <a href="#" class="text-blue-300 hover:text-blue-200 text-sm">
                                查看更多 <?php echo $categoryName; ?> 游戏 →
                            </a>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        <?php endforeach; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-black bg-opacity-50 backdrop-blur-sm border-t border-gray-700 mt-12">
        <div class="container mx-auto px-4 py-6">
            <div class="text-center text-gray-300">
                <p>&copy; 2024 Sonice.Games - 免费在线游戏平台</p>
                <p class="text-sm mt-2">
                    <a href="system_check.php" class="text-blue-300 hover:text-blue-200 mr-4">系统检查</a>
                    <a href="status_report.php" class="text-blue-300 hover:text-blue-200 mr-4">状态报告</a>
                    <a href="../" class="text-blue-300 hover:text-blue-200">返回主页</a>
                </p>
            </div>
        </div>
    </footer>
</body>
</html> 