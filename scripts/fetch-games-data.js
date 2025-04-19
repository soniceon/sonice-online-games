const fs = require('fs');
const path = require('path');
const csv = require('csv-parser');
const axios = require('axios');
const cheerio = require('cheerio');

// 配置
const CSV_FILE_PATH = path.join(__dirname, '../游戏iframe.CSV');
const GAMES_JSON_PATH = path.join(__dirname, '../data/games.json');
const CATEGORIES_JSON_PATH = path.join(__dirname, '../data/categories.json');
const IMAGES_DIR = path.join(__dirname, '../assets/images/games');

// 游戏类型关键词映射 (与generate-game-pages.js相同)
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

// 确保输出目录存在
function ensureDirectoryExists(directory) {
  if (!fs.existsSync(directory)) {
    fs.mkdirSync(directory, { recursive: true });
  }
}

// 根据游戏标题生成slug
function slugify(text) {
  return text
    .toString()
    .toLowerCase()
    .replace(/\s+/g, '-')
    .replace(/[^\w\-]+/g, '')
    .replace(/\-\-+/g, '-')
    .replace(/^-+/, '')
    .replace(/-+$/, '');
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

// 创建游戏描述
function generateDescription(gameTitle, categories) {
  const primaryCategory = categories[0];
  let description = `Play ${gameTitle} online for free! `;
  
  switch(primaryCategory) {
    case 'clicker':
      description += 'Click your way to success in this addictive incremental game.';
      break;
    case 'idle':
      description += 'Set up your idle empire and watch it grow even when you\'re not playing.';
      break;
    case 'merge':
      description += 'Combine similar items to create more powerful ones in this satisfying merge game.';
      break;
    case 'mining':
      description += 'Dig for valuable resources and upgrade your mining operation.';
      break;
    case 'tycoon':
      description += 'Build and manage your own business empire in this detailed simulation.';
      break;
    case 'puzzle':
      description += 'Challenge your brain with fun and increasingly difficult puzzles.';
      break;
    case 'action':
      description += 'Test your reflexes in this exciting action-packed adventure.';
      break;
    case 'strategy':
      description += 'Plan your moves carefully to overcome challenges in this strategic game.';
      break;
    case 'racing':
      description += 'Speed through tracks and challenge your driving skills.';
      break;
    case 'shooter':
      description += 'Test your aim and reflexes in this targeting challenge.';
      break;
    case 'sports':
      description += 'Experience the thrill of competitive sports in a virtual environment.';
      break;
    case 'simulation':
      description += 'Create and manage your own virtual world in this detailed simulation.';
      break;
    case 'rpg':
      description += 'Embark on an epic adventure and develop your character as you progress.';
      break;
    case 'card':
      description += 'Collect and play cards strategically to outsmart your opponents.';
      break;
    default:
      description += 'Enjoy this entertaining game directly in your browser, no download required.';
  }
  
  return description;
}

// 从页面获取游戏的额外信息 (异步)
async function fetchGameDetails(url) {
  try {
    const response = await axios.get(url);
    const $ = cheerio.load(response.data);
    
    // 尝试提取描述
    let description = '';
    const metaDescription = $('meta[name="description"]').attr('content');
    if (metaDescription) {
      description = metaDescription;
    }
    
    // 尝试提取控制信息 (需根据具体网站结构调整)
    let controls = '';
    $('.controls, .game-controls, .how-to-play').each(function() {
      controls += $(this).text().trim() + ' ';
    });
    
    return { description, controls };
  } catch (error) {
    console.error(`Error fetching details from ${url}:`, error.message);
    return { description: '', controls: '' };
  }
}

// 主函数：处理CSV并生成JSON数据
async function main() {
  ensureDirectoryExists(path.dirname(GAMES_JSON_PATH));
  ensureDirectoryExists(IMAGES_DIR);
  
  const games = [];
  
  // 读取CSV文件
  await new Promise((resolve, reject) => {
    fs.createReadStream(CSV_FILE_PATH)
      .pipe(csv())
      .on('data', (data) => games.push(data))
      .on('end', resolve)
      .on('error', reject);
  });
  
  console.log(`Found ${games.length} games in CSV file.`);
  
  // 处理游戏数据
  const processedGames = [];
  const allCategories = new Set();
  
  for (let i = 0; i < games.length; i++) {
    const game = games[i];
    const title = game.title;
    const iframeUrl = game.iframe_url;
    const slug = slugify(title);
    const categories = identifyCategories(title);
    
    // 将分类添加到全局分类集合
    categories.forEach(cat => allCategories.add(cat));
    
    // 创建默认描述
    let description = generateDescription(title, categories);
    
    console.log(`Processing game ${i+1}/${games.length}: ${title}`);
    
    // 可选：尝试从游戏URL获取更多信息
    // 注意：此步骤可能会很慢，可以注释掉以加快处理速度
    /*
    try {
      const details = await fetchGameDetails(iframeUrl);
      if (details.description) {
        description = details.description;
      }
    } catch (error) {
      console.error(`Error fetching details for ${title}:`, error);
    }
    */
    
    // 添加处理后的游戏数据
    processedGames.push({
      id: slug,
      title: title,
      categories: categories,
      description: description,
      iframeUrl: iframeUrl,
      imageUrl: `/assets/images/games/${slug}-360-240.webp`
    });
  }
  
  // 保存游戏数据
  const gamesData = {
    games: processedGames,
    updated: new Date().toISOString()
  };
  
  fs.writeFileSync(GAMES_JSON_PATH, JSON.stringify(gamesData, null, 2));
  console.log(`Games data saved to ${GAMES_JSON_PATH}`);
  
  // 保存分类数据
  const categoriesArray = Array.from(allCategories).sort();
  const categoriesData = {
    categories: categoriesArray,
    updated: new Date().toISOString()
  };
  
  fs.writeFileSync(CATEGORIES_JSON_PATH, JSON.stringify(categoriesData, null, 2));
  console.log(`Categories data saved to ${CATEGORIES_JSON_PATH}`);
  
  console.log('All game data processed successfully!');
}

// 运行主函数
main().catch(error => {
  console.error('Error in main function:', error);
  process.exit(1);
}); 