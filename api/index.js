// Vercel Edge Function - 使用 JavaScript 而不是 PHP
export default function handler(request) {
  const games = [
    { title: 'Falling Fruits', category: 'Idle', slug: 'falling-fruits' },
    { title: 'Chipuzik\'s Evolution', category: 'Idle', slug: 'chipuziks-evolution' },
    { title: 'Painter\'s Voyage Idle', category: 'Idle', slug: 'painters-voyage-idle' },
    { title: 'Magic Chop Idle', category: 'Idle', slug: 'magic-chop-idle' },
    { title: 'My Sugar Factory 2', category: 'Tycoon', slug: 'my-sugar-factory-2' },
    { title: 'Slime Farm Remake', category: 'Farm', slug: 'slime-farm-remake' },
    { title: 'Cupcake Clicker', category: 'Clicker', slug: 'cupcake-clicker' },
    { title: 'Haste-Miner', category: 'Mining', slug: 'haste-miner' },
    { title: 'Planet Miner FRVR', category: 'Mining', slug: 'planet-miner-frvr' },
    { title: 'Doggo Clicker', category: 'Clicker', slug: 'doggo-clicker' },
  ];

  const categories = [
    { name: 'Idle', icon: '⏳', color: '#06d6a0' },
    { name: 'Tycoon', icon: '🏢', color: '#4361ee' },
    { name: 'Farm', icon: '🌱', color: '#06d6a0' },
    { name: 'Clicker', icon: '🖱️', color: '#f72585' },
    { name: 'Mining', icon: '💎', color: '#ffd700' },
  ];

  const html = `<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sonice Online Games - 免费在线游戏</title>
    <meta name="description" content="在 Sonice.Games 玩免费在线游戏，包括动作、赛车、体育、射击、卡片、冒险、益智等各类游戏。">
    <link rel="icon" type="image/png" href="/assets/images/icons/logo.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            color: #333; 
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 40px; color: white; }
        .logo { font-size: 3rem; font-weight: bold; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .subtitle { font-size: 1.2rem; opacity: 0.9; }
        .game-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 20px; 
            margin-bottom: 40px; 
        }
        .game-card { 
            background: white; 
            border-radius: 15px; 
            padding: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); 
            transition: transform 0.3s ease; 
            text-decoration: none; 
            color: inherit; 
        }
        .game-card:hover { transform: translateY(-5px); }
        .game-title { font-size: 1.3rem; font-weight: bold; margin-bottom: 10px; color: #333; }
        .game-category { 
            display: inline-block; 
            background: #667eea; 
            color: white; 
            padding: 5px 12px; 
            border-radius: 20px; 
            font-size: 0.9rem; 
        }
        .categories { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 15px; 
            margin-bottom: 40px; 
        }
        .category-card { 
            background: white; 
            border-radius: 10px; 
            padding: 20px; 
            text-align: center; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
            transition: transform 0.3s ease; 
            text-decoration: none; 
            color: inherit; 
        }
        .category-card:hover { transform: translateY(-3px); }
        .category-icon { font-size: 2rem; margin-bottom: 10px; }
        .category-name { font-weight: bold; margin-bottom: 5px; }
        .footer { text-align: center; color: white; margin-top: 40px; padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1 class="logo">🎮 Sonice Games</h1>
            <p class="subtitle">免费在线游戏平台</p>
        </header>
        
        <div class="categories">
            ${categories.map(cat => `
            <div class="category-card">
                <div class="category-icon">${cat.icon}</div>
                <div class="category-name">${cat.name} 游戏</div>
            </div>
            `).join('')}
        </div>
        
        <div class="game-grid">
            ${games.map(game => `
            <a href="/game/${game.slug}" class="game-card">
                <h3 class="game-title">${game.title}</h3>
                <div class="game-category">${game.category}</div>
            </a>
            `).join('')}
        </div>
        
        <footer class="footer">
            <p>&copy; 2025 Sonice Online Games. 保留所有权利。</p>
            <p>联系我们：<a href="mailto:contact@sonice.online" style="color: white;">contact@sonice.online</a></p>
        </footer>
    </div>
</body>
</html>`;

  return new Response(html, {
    headers: {
      'Content-Type': 'text/html; charset=utf-8',
    },
  });
}
