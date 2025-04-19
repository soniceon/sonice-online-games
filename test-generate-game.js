const fs = require('fs');
const path = require('path');
const csv = require('csv-parser');

// 配置
const CSV_FILE_PATH = path.join(__dirname, '游戏iframe.CSV');
const TEMPLATE_PATH = path.join(__dirname, 'src/templates/game-template.html');
const OUTPUT_DIR = path.join(__dirname, 'test_output');
const TARGET_GAME = 'Soccer Clicker'; // 修改这里来选择具体的游戏

// 营销词语和品牌信息
const BRAND_TAGLINE = "Play various games for free on sonice.online. Thanks for your support and sharing!";

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
  
  // 生成更丰富的游戏特定描述
  let specificDescription = '';
  
  // 根据游戏名称添加特定内容
  if (gameTitle.toLowerCase().includes('miner') || gameTitle.toLowerCase().includes('mining')) {
    specificDescription = `Embark on an exciting mining adventure in ${gameTitle}! Discover rare minerals, upgrade your equipment, and become the ultimate mining tycoon. Strategically manage your resources to maximize profits and unlock advanced technologies. With intuitive controls and addictive gameplay, you'll find yourself mining for hours on end.`;
  } else if (gameTitle.toLowerCase().includes('merge')) {
    specificDescription = `Discover the satisfaction of merging in ${gameTitle}! Combine similar items to create more powerful ones, manage your space efficiently, and unlock special rare items. The thrill of discovering new combinations will keep you engaged as you progress through increasingly challenging levels. Can you discover all the hidden merge combinations?`;
  } else if (gameTitle.toLowerCase().includes('clicker') || gameTitle.toLowerCase().includes('idle')) {
    specificDescription = `${gameTitle} offers the perfect balance of active clicking and idle progression! Start with simple clicks and gradually build your empire, unlocking automated systems that work even when you're away. Return to collect massive rewards, upgrade your production, and watch your numbers grow exponentially. The perfect game for both casual and dedicated players.`;
  } else if (gameTitle.toLowerCase().includes('tycoon')) {
    specificDescription = `Build and expand your business empire in ${gameTitle}! Make smart economic decisions, hire staff, research new technologies, and outperform competitors. From humble beginnings to industry domination, every choice matters in this detailed simulation. Can you create the most profitable business and become the ultimate tycoon?`;
  } else {
    // 使用基础描述加上一些通用内容
    specificDescription = `${baseDescription} ${gameTitle} features colorful graphics, intuitive gameplay, and progressively challenging levels that will keep you engaged for hours.`;
  }
  
  return `${specificDescription} ${BRAND_TAGLINE}`;
}

// 生成游戏控制说明
function generateControls(categories, gameTitle) {
  const primaryCategory = categories[0];
  const baseControls = GAME_CONTROLS[primaryCategory] || GAME_CONTROLS['action'];
  
  // 根据游戏名称和类别生成更详细的控制说明
  let detailedControls = '';
  
  if (gameTitle.toLowerCase().includes('miner') || gameTitle.toLowerCase().includes('mining')) {
    detailedControls = `Click or tap on the mining area to extract resources. Upgrade your pickaxe, drills, and other tools to increase mining speed and efficiency. Use the menu buttons to access upgrades, statistics, and special abilities. More powerful equipment allows you to reach deeper levels with rarer minerals. Remember to manage your energy and resources wisely!`;
  } else if (gameTitle.toLowerCase().includes('merge')) {
    detailedControls = `Drag and drop similar items to merge them into more valuable ones. Double-tap or click to select an item, then drag it onto another identical item to combine them. Use the menu to purchase new items, access your inventory, and view merge combinations. Strategic placement and timing are key to maximizing your space and creating the most valuable items.`;
  } else if (gameTitle.toLowerCase().includes('clicker') || gameTitle.toLowerCase().includes('idle')) {
    detailedControls = `Click or tap repeatedly on the main area to earn resources. Use earned currency to purchase upgrades that increase your clicking power or automate resource collection. Navigate between different tabs to manage various aspects of production. For optimal progress, balance active clicking with strategic investment in automated systems.`;
  } else if (gameTitle.toLowerCase().includes('tycoon')) {
    detailedControls = `Click on buildings or businesses to collect income. Use the menu to purchase upgrades, hire employees, and expand your operations. Drag and place new structures in the designated areas. Manage your resources carefully and make strategic investments to maximize your profit margins and grow your business empire.`;
  } else if (categories.includes('racing')) {
    detailedControls = `Use arrow keys or WASD to control your vehicle. Press Up/W to accelerate, Down/S to brake, Left/A and Right/D to steer. Spacebar for handbrake or drift. Master the timing of your drifts to maintain speed through corners. Some vehicles have special abilities activated with the Shift or Ctrl keys.`;
  } else if (categories.includes('shooter')) {
    detailedControls = `Use mouse to aim and left-click to shoot. Move with WASD or arrow keys. Spacebar to jump, Shift to sprint, and Ctrl to crouch. R to reload weapons, E to interact with objects. Number keys 1-9 to switch between weapons. Right-click for scoped aiming or secondary fire mode.`;
  } else {
    // 如果没有特定控制说明，则使用基础控制说明
    detailedControls = baseControls;
  }
  
  return detailedControls;
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

// 生成推荐游戏
function generateRecommendedGames(currentCategories) {
  // 根据当前游戏分类生成相关的推荐游戏
  const recommendations = [
    {
      title: "Merge Mine: Idle Clicker",
      description: "Combine mining equipment to dig deeper and discover rare minerals in this addictive merge game.",
      slug: "merge-mine-idle-clicker",
      image: "/assets/images/games/merge-mine-idle-clicker-360-240.webp"
    },
    {
      title: "Tower Defense Merge",
      description: "Strategically merge towers to defend against waves of enemies in this exciting merge strategy game.",
      slug: "tower-defense-merge",
      image: "/assets/images/games/tower-defense-merge-360-240.webp"
    },
    {
      title: "Mine Merge Mania",
      description: "Merge mining tools and discover valuable resources in this fun mining simulation game.",
      slug: "mine-merge-mania",
      image: "/assets/images/games/mine-merge-mania-360-240.webp"
    },
    {
      title: "The Cool Merge",
      description: "Merge various objects to create cooler and more valuable items in this relaxing merge game.",
      slug: "the-cool-merge",
      image: "/assets/images/games/the-cool-merge-360-240.webp"
    }
  ];
  
  // 如果当前游戏是采矿类游戏，添加更多采矿相关推荐
  if (currentCategories.includes('mining')) {
    recommendations.push({
      title: "Haste-Miner 2",
      description: "The sequel to the popular mining game with more resources, tools and challenges.",
      slug: "haste-miner-2",
      image: "/assets/images/games/haste-miner-2-360-240.webp"
    });
  }
  
  return recommendations;
}

// 生成游戏页面
function generateGamePage(game) {
  const slug = slugify(game.title);
  const categories = identifyCategories(game.title);
  const description = generateDescription(game.title, categories);
  const controls = generateControls(categories, game.title);
  
  // 构建游戏对象，用于替换模板变量
  const gameData = {
    title: game.title,
    slug: slug,
    description: description,
    categories: categories,
    iframeUrl: game.iframe_url,
    controls: controls,
    image: `/assets/images/games/${slug}-360-240.webp`,
    recommendedGames: generateRecommendedGames(categories),
    rating: 4.5,
    ratingCount: 128
  };
  
  // 读取模板文件
  let template;
  try {
    template = fs.readFileSync(TEMPLATE_PATH, 'utf8');
  } catch (error) {
    console.error(`无法读取模板文件: ${error.message}`);
    return;
  }
  
  // 替换模板中的变量
  let pageContent = template;
  
  // 基本替换
  pageContent = pageContent.replace(/{{game.title}}/g, gameData.title);
  pageContent = pageContent.replace(/{{game.slug}}/g, gameData.slug);
  pageContent = pageContent.replace(/{{game.description}}/g, gameData.description);
  pageContent = pageContent.replace(/{{game.image}}/g, gameData.image);
  pageContent = pageContent.replace(/{{game.iframeUrl}}/g, gameData.iframeUrl);
  pageContent = pageContent.replace(/{{game.controls}}/g, gameData.controls);
  pageContent = pageContent.replace(/{{game.rating\|default\(4\.5\)}}/g, gameData.rating);
  pageContent = pageContent.replace(/{{game.ratingCount\|default\(128\)}}/g, gameData.ratingCount);
  
  // 处理Jinja模板中的循环和条件语句
  // 处理分类循环
  pageContent = pageContent.replace(/{%\s*for\s+category\s+in\s+game\.categories\s*%}([\s\S]*?){%\s*endfor\s*%}/g, 
    categories.map(category => {
      let content = RegExp.$1;
      return content.replace(/{{category}}/g, category);
    }).join(''));
  
  // 替换 genre 元数据
  const genreText = categories.join(', ');
  pageContent = pageContent.replace(/"genre": "{% for category in game.categories %}{{category}}{% if not loop.last %}, {% endif %}{% endfor %}"/g, `"genre": "${genreText}"`);
  
  // 处理推荐游戏循环
  pageContent = pageContent.replace(/{%\s*for\s+rec_game\s+in\s+game\.recommendedGames\s*%}([\s\S]*?){%\s*endfor\s*%}/g, 
    gameData.recommendedGames.map(recGame => {
      let content = RegExp.$1;
      return content
        .replace(/{{rec_game\.title}}/g, recGame.title)
        .replace(/{{rec_game\.description}}/g, recGame.description)
        .replace(/{{rec_game\.slug}}/g, recGame.slug)
        .replace(/{{rec_game\.image}}/g, recGame.image);
    }).join(''));
  
  // 处理loop.last条件
  pageContent = pageContent.replace(/{%\s*if\s+not\s+loop\.last\s*%}([\s\S]*?){%\s*endif\s*%}/g, '');
  
  // 写入文件
  const outputPath = path.join(OUTPUT_DIR, `${slug}.html`);
  fs.writeFileSync(outputPath, pageContent);
  
  console.log(`生成的游戏页面: ${game.title} -> ${outputPath}`);
  console.log(`游戏分类: ${categories.join(', ')}`);
  
  return outputPath;
}

// 读取CSV找到特定游戏并生成页面
const foundGames = [];

fs.createReadStream(CSV_FILE_PATH)
  .pipe(csv())
  .on('data', (data) => {
    // 查找指定游戏或累积所有游戏
    if (!TARGET_GAME || data.title.toLowerCase().includes(TARGET_GAME.toLowerCase())) {
      foundGames.push(data);
    }
  })
  .on('end', () => {
    if (foundGames.length === 0) {
      console.log(`未找到名称包含 "${TARGET_GAME}" 的游戏，将生成第一个游戏页面`);
      // 如果没找到，再次读取CSV获取第一个游戏
      fs.createReadStream(CSV_FILE_PATH)
        .pipe(csv())
        .on('data', (data) => {
          generateGamePage(data);
          process.exit(0);
        })
        .on('error', (error) => {
          console.error(`处理CSV时出错: ${error.message}`);
        });
    } else {
      // 找到了匹配的游戏，生成第一个匹配的游戏页面
      console.log(`找到 ${foundGames.length} 个匹配的游戏`);
      generateGamePage(foundGames[0]);
      process.exit(0);
    }
  })
  .on('error', (error) => {
    console.error(`处理CSV时出错: ${error.message}`);
  }); 