<?php
// 测试游戏卡片跳转功能
echo "🎮 测试游戏卡片跳转功能\n";
echo "========================\n\n";

// 加载游戏数据
function load_games_from_csv_test($csvFile) {
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
$games = load_games_from_csv_test($csvFile);

echo "📊 总共加载了 " . count($games) . " 个游戏\n\n";

// 测试几个游戏slug
$testSlugs = [
    'cupcake-clicker',
    'doggo-clicker', 
    'haste-miner',
    'planet-miner-frvr',
    'dragon-hunter' // 这个在日志中显示404错误
];

foreach ($testSlugs as $slug) {
    echo "测试游戏: {$slug}\n";
    
    // 查找当前游戏
    $currentGame = null;
    foreach ($games as $game) {
        if ($game['slug'] === $slug) {
            $currentGame = $game;
            break;
        }
    }
    
    if ($currentGame) {
        echo "✅ 找到游戏: {$currentGame['title']}\n";
        echo "   分类: " . implode(', ', $currentGame['categories']) . "\n";
        echo "   iframe: " . ($currentGame['iframe_url'] ? '✅ 有效' : '❌ 无效') . "\n";
        echo "   图片: ";
        
        $imgPath = __DIR__ . '/assets/images/games/' . $slug . '.webp';
        $imgPathPng = __DIR__ . '/assets/images/games/' . $slug . '.png';
        $imgPathJpg = __DIR__ . '/assets/images/games/' . $slug . '.jpg';
        
        if (file_exists($imgPath)) {
            echo "✅ WebP存在\n";
        } elseif (file_exists($imgPathPng)) {
            echo "✅ PNG存在\n";
        } elseif (file_exists($imgPathJpg)) {
            echo "✅ JPG存在\n";
        } else {
            echo "❌ 无图片\n";
        }
    } else {
        echo "❌ 未找到游戏: {$slug}\n";
        
        // 显示所有可用的slug
        echo "   可用的游戏slug:\n";
        $availableSlugs = array_slice(array_column($games, 'slug'), 0, 10);
        foreach ($availableSlugs as $availableSlug) {
            echo "     - {$availableSlug}\n";
        }
        if (count($games) > 10) {
            echo "     ... 还有 " . (count($games) - 10) . " 个游戏\n";
        }
    }
    
    echo "\n";
}

echo "🎯 跳转测试完成！\n";
echo "现在可以在浏览器中测试:\n";
echo "http://localhost:8000/game.php?slug=cupcake-clicker\n";
echo "http://localhost:8000/game.php?slug=doggo-clicker\n";
echo "http://localhost:8000/game.php?slug=haste-miner\n";
echo "\n";
echo "💡 提示: 确保PHP服务器正在运行 (php -S localhost:8000 -t public)\n";
?> 