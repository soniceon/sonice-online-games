const fs = require('fs');
const path = require('path');
const csv = require('csv-parser');

// 配置
const CSV_FILE_PATH = path.join(__dirname, '../游戏iframe.CSV');
const TEMPLATE_PATH = path.join(__dirname, '../src/templates/game-template.html');
const OUTPUT_DIR = path.join(__dirname, '../pages/games');
const IMAGES_DIR = path.join(__dirname, '../assets/images/games');

// 游戏类型关键词映射
const GAME_CATEGORIES = {
  'clicker': ['clicker', 'click', 'tap', 'idle'],
  'idle': ['idle', 'incremental', 'afk'],
  'merge': ['merge', 'merging', 'combining'],
  'mining': ['mine', 'miner', 'mining', 'dig', 'excavate'],
  'tycoon': ['tycoon', 'business', 'company', 'factory', 'empire', 'manager'],
  'puzzle': ['puzzle', 'match', 'mystery', 'brain'],
  'action': ['action', 'adventure', 'jump', 'run'],
  'strategy': ['strategy', 'defense', 'tower', 'battle', 'war'],
  'racing': ['race', 'racing', 'car', 'drift', 'drive'],
  'shooter': ['shoot', 'gun', 'weapon', 'blast', 'battle'],
  'sports': ['sports', 'soccer', 'football', 'basketball', 'hockey', 'baseball'],
  'simulation': ['simulator', 'simulation', 'farm', 'life', 'city', 'build'],
  'rpg': ['rpg', 'role', 'hero', 'character', 'level up', 'adventure'],
  'card': ['card', 'deck', 'collect']
};

// 游戏控制方法映射
const GAME_CONTROLS = {
  'clicker': 'Use your mouse to click on targets. The more you click, the more rewards you earn.',
  'idle': 'Initially click to earn resources, then let the game run automatically. Periodically check back to upgrade and progress.',
  'merge': 'Drag and drop similar items to merge them into more valuable items. Strategically combine resources for maximum efficiency.',
  'mining': 'Click or tap to mine resources. Upgrade your tools to increase mining efficiency and resources gained.',
  'tycoon': 'Click to build and manage your business. Make strategic decisions to maximize profits and expand your empire.',
  'puzzle': 'Use your mouse to move pieces, solve puzzles, and complete challenges. Think carefully about each move.',
  'action': 'Use keyboard (WASD or arrow keys) to move and space/mouse to interact with objects and perform actions.',
  'strategy': 'Click to place units or buildings. Plan your strategy carefully to overcome challenges and defeat opponents.',
  'racing': 'Use arrow keys or WASD to control your vehicle. Master drifting and shortcuts to win races.',
  'shooter': 'Use mouse to aim and click to shoot. Keyboard (WASD or arrows) to move around the environment.',
  'sports': 'Use keyboard controls or mouse clicks to play sports. Time your actions for perfect execution.',
  'simulation': 'Click to interact with the environment and build your simulation. Manage resources and make decisions.',
  'rpg': 'Use keyboard to move and mouse to interact. Complete quests, level up your character, and explore the world.',
  'card': 'Use your mouse to select and play cards. Develop strategies to outsmart your opponents.'
};

// 游戏玩法介绍模板
const GAME_DESCRIPTIONS = {
  'clicker': 'In this addictive clicker game, you\'ll tap your way to success. Start small and gradually build up your clicking empire. Unlock upgrades, discover new features, and watch your numbers grow exponentially!',
  'idle': 'This idle game lets you progress even when you\'re not playing. Set up your resources, make strategic upgrades, and come back to collect your rewards. Perfect for casual gamers who enjoy seeing progress over time.',
  'merge': 'Combine similar items to create more powerful ones in this satisfying merge game. Discover new combinations, manage your space efficiently, and unlock special items as you progress through increasingly challenging levels.',
  'mining': 'Dig deep and discover valuable resources in this mining adventure. Upgrade your tools, explore new areas, and become the ultimate mining tycoon by efficiently gathering and managing your resources.',
  'tycoon': 'Build and manage your own business empire in this detailed tycoon simulation. Make smart economic decisions, hire staff, research new technologies, and outperform your competitors to become an industry leader.',
  'puzzle': 'Challenge your brain with increasingly difficult puzzles. Each level presents unique challenges that will test your logical thinking, pattern recognition, and problem-solving skills.',
  'action': 'Jump, run, and battle your way through exciting levels in this fast-paced action game. Quick reflexes and good timing are essential as you face various obstacles and enemies.',
  'strategy': 'Plan your moves carefully in this strategic challenge. Build defenses, manage resources, and outwit opponents through clever tactics and long-term planning.',
  'racing': 'Speed through thrilling tracks in high-octane racing action. Master the controls, find the optimal racing line, and upgrade your vehicle to leave competitors in the dust.',
  'shooter': 'Test your aim and reflexes in this targeting challenge. Shoot targets with precision, upgrade your weapons, and improve your accuracy to achieve the highest scores.',
  'sports': 'Experience the thrill of competitive sports in a virtual environment. Perfect your technique, develop strategies, and compete against increasingly skilled opponents.',
  'simulation': 'Create and manage your own virtual world in this detailed simulation. Make decisions that affect your environment, handle unexpected events, and watch your creation evolve over time.',
  'rpg': 'Embark on an epic adventure where you\'ll develop your character, complete quests, and explore a rich world. Make choices that affect your journey and become increasingly powerful as you progress.',
  'card': 'Collect and play cards strategically in this engaging card game. Build powerful decks, learn effective combinations, and outsmart your opponents through clever card play.'
};

// 确保输出目录存在
if (!fs.existsSync(OUTPUT_DIR)) {
  fs.mkdirSync(OUTPUT_DIR, { recursive: true });
}

// 读取模板文件
let template;
try {
  template = fs.readFileSync(TEMPLATE_PATH, 'utf8');
} catch (error) {
  console.error(`无法读取模板文件: ${error.message}`);
  template = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{game.title}} - Play Free Online Game</title>
    <meta name="description" content="{{game.description}}">
</head>
<body>
    <h1>{{game.title}}</h1>
    <div class="game-wrapper">
        <iframe src="{{game.iframeUrl}}" frameborder="0" allowfullscreen></iframe>
    </div>
    <div class="game-controls">
        <h2>How to Play</h2>
        <p>{{game.controls}}</p>
    </div>
    <div class="categories">
        {{game.categories_html}}
    </div>
</body>
</html>`;
}

// 从游戏名称中识别游戏分类
function identifyCategories(gameTitle) {
  const title = gameTitle.toLowerCase();
  const categories = [];
  
  // 检查标题中是否包含分类关键词
  for (const [category, keywords] of Object.entries(GAME_CATEGORIES)) {
    if (keywords.some(keyword => title.includes(keyword.toLowerCase()))) {
      categories.push(category);
    }
  }
  
  // 如果没有找到分类，添加默认分类
  if (categories.length === 0) {
    categories.push('action');
  }
  
  return categories;
}

// 从游戏标题创建一个简短的描述
function generateDescription(gameTitle, categories) {
  const primaryCategory = categories[0];
  const baseDescription = GAME_DESCRIPTIONS[primaryCategory] || GAME_DESCRIPTIONS['action'];
  
  return `Play ${gameTitle} online for free! ${baseDescription}`;
}

// 生成游戏控制说明
function generateControls(categories) {
  const primaryCategory = categories[0];
  return GAME_CONTROLS[primaryCategory] || GAME_CONTROLS['action'];
}

// 根据游戏标题生成slug
function slugify(text) {
  return text
    .toString()
    .toLowerCase()
    .replace(/\s+/g, '-')       // 将空格替换为连字符
    .replace(/[^\w\-]+/g, '')   // 删除所有非单词字符
    .replace(/\-\-+/g, '-')     // 将多个连字符替换为单个连字符
    .replace(/^-+/, '')         // 删除开头的连字符
    .replace(/-+$/, '');        // 删除结尾的连字符
}

// 生成游戏页面
function generateGamePage(game) {
  const slug = slugify(game.title);
  const categories = identifyCategories(game.title);
  const description = generateDescription(game.title, categories);
  const controls = generateControls(categories);
  
  // 构建游戏对象，用于替换模板变量
  const gameData = {
    title: game.title,
    slug: slug,
    description: description,
    categories: categories,
    iframeUrl: game.iframe_url,
    controls: controls,
    image: `/assets/images/games/${slug}-360-240.webp`
  };
  
  // 替换模板中的变量
  let pageContent = template;
  
  // 基本替换
  pageContent = pageContent.replace(/{{game.title}}/g, gameData.title);
  pageContent = pageContent.replace(/{{game.slug}}/g, gameData.slug);
  pageContent = pageContent.replace(/{{game.description}}/g, gameData.description);
  pageContent = pageContent.replace(/{{game.image}}/g, gameData.image);
  pageContent = pageContent.replace(/{{game.iframeUrl}}/g, gameData.iframeUrl);
  pageContent = pageContent.replace(/{{game.controls}}/g, gameData.controls);
  
  // 替换分类列表
  const categoriesHtml = categories.map(category => 
    `<span class="category-tag">${category}</span>`
  ).join('\n');
  pageContent = pageContent.replace(/{{game.categories_html}}/g, categoriesHtml);
  
  // 替换 genre
  const genreText = categories.join(', ');
  pageContent = pageContent.replace(/"genre": "{% for category in game.categories %}{{category}}{% if not loop.last %}, {% endif %}{% endfor %}"/g, `"genre": "${genreText}"`);
  
  // 写入文件
  const outputPath = path.join(OUTPUT_DIR, `${slug}.html`);
  fs.writeFileSync(outputPath, pageContent);
  
  console.log(`Generated page for: ${game.title} -> ${outputPath}`);
  
  return outputPath;
}

// 主函数：处理CSV并生成页面
function main() {
  const games = [];
  
  fs.createReadStream(CSV_FILE_PATH)
    .pipe(csv())
    .on('data', (data) => games.push(data))
    .on('end', () => {
      console.log(`Found ${games.length} games in CSV file.`);
      
      // 为每个游戏生成页面
      games.forEach(game => {
        try {
          generateGamePage(game);
        } catch (error) {
          console.error(`Error generating page for ${game.title}:`, error);
        }
      });
      
      console.log('All game pages generated successfully!');
    });
}

// 导出函数
module.exports = {
  generateGamePage,
  identifyCategories,
  generateDescription,
  generateControls,
  slugify
};

// 如果直接运行脚本，则执行main函数
if (require.main === module) {
  main();
} 