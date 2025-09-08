const fs = require('fs');
const path = require('path');

// 简单的静态网站生成器
function buildStaticSite() {
  console.log('🚀 开始构建静态网站...');
  
  // 读取游戏数据
  const csvFile = path.join(__dirname, '游戏iframe.CSV');
  const games = [];
  
  if (fs.existsSync(csvFile)) {
    const csvContent = fs.readFileSync(csvFile, 'utf8');
    const lines = csvContent.split('\n');
    
    for (let i = 1; i < lines.length; i++) {
      const line = lines[i].trim();
      if (line) {
        const [title, iframeUrl, categories] = line.split(',');
        if (title && iframeUrl) {
          const slug = title.toLowerCase()
            .replace(/[^a-z0-9\s]/g, '')
            .replace(/\s+/g, '-');
          
          games.push({
            slug,
            title: title.trim(),
            iframeUrl: iframeUrl.trim(),
            categories: categories ? categories.split(',').map(c => c.trim()) : []
          });
        }
      }
    }
  }
  
  // 生成静态 HTML
  const html = `<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sonice Online Games - 免费在线游戏</title>
    <meta name="description" content="在 Sonice.Games 玩免费在线游戏，包括动作、赛车、体育、射击、卡片、冒险、益智等各类游戏。">
    <link rel="icon" type="image/png" href="/assets/images/icons/logo.png">
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 40px; color: white; }
        .logo { font-size: 3rem; font-weight: bold; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .subtitle { font-size: 1.2rem; opacity: 0.9; }
        .game-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .game-card { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); transition: transform 0.3s ease; text-decoration: none; color: inherit; }
        .game-card:hover { transform: translateY(-5px); }
        .game-title { font-size: 1.3rem; font-weight: bold; margin-bottom: 10px; color: #333; }
        .game-category { display: inline-block; background: #667eea; color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1 class="logo">🎮 Sonice Games</h1>
            <p class="subtitle">免费在线游戏平台</p>
        </header>
        
        <div class="game-grid">
            ${games.map(game => `
                <a href="/game/${game.slug}" class="game-card">
                    <h3 class="game-title">${game.title}</h3>
                    <div class="game-category">${game.categories[0] || '游戏'}</div>
                </a>
            `).join('')}
        </div>
    </div>
</body>
</html>`;
  
  // 写入文件
  fs.writeFileSync(path.join(__dirname, 'public', 'index.html'), html);
  console.log('✅ 静态网站构建完成！');
  console.log(`📊 生成了 ${games.length} 个游戏`);
}

buildStaticSite();
