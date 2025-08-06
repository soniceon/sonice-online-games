/**
 * 游戏数据管理模块
 * 处理游戏数据的加载、过滤和处理
 */

/**
 * 游戏数据缓存
 * @type {Array<Object>}
 */
let gameDataCache = null;

/**
 * 分类数据缓存
 * @type {Array<String>}
 */
let categoriesCache = null;

/**
 * 加载游戏数据
 * @returns {Promise<Array>} 游戏数据数组
 */
export async function loadGameData() {
  if (gameDataCache) {
    return gameDataCache;
  }

  try {
    const response = await fetch('/data/games.json');
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    const data = await response.json();
    gameDataCache = Array.isArray(data) ? data : data.games || [];
    
    // 转换数据格式，确保字段一致性
    gameDataCache = gameDataCache.map(game => normalizeGameData(game));
    
    return gameDataCache;
  } catch (error) {
    console.error('Failed to load game data:', error);
    return [];
  }
}

/**
 * 加载分类数据
 * @returns {Promise<Array>} 分类数据数组
 */
export async function loadCategories() {
  if (categoriesCache) {
    return categoriesCache;
  }

  try {
    const response = await fetch('/data/categories.json');
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    const data = await response.json();
    categoriesCache = Array.isArray(data) ? data : data.categories || [];
    
    return categoriesCache;
  } catch (error) {
    console.error('Failed to load categories:', error);
    
    // 如果加载失败，尝试从游戏数据中提取分类
    const games = await loadGameData();
    const categoriesSet = new Set();
    
    games.forEach(game => {
      if (game.categories && Array.isArray(game.categories)) {
        game.categories.forEach(category => {
          if (category) categoriesSet.add(category);
        });
      }
    });
    
    categoriesCache = Array.from(categoriesSet);
    return categoriesCache;
  }
}

/**
 * 标准化游戏数据
 * @param {Object} game 游戏对象
 * @returns {Object} 标准化后的游戏对象
 */
function normalizeGameData(game) {
  return {
    title: game.title || '',
    description: game.description || '',
    slug: game.slug || slugify(game.title || ''),
    categories: Array.isArray(game.categories) ? game.categories : 
                (game.category ? [game.category] : []),
    iframeUrl: game.iframeUrl || game.iframe_url || '',
    imageUrl: game.imageUrl || game.image_url || '',
    controls: game.controls || '',
    popularity: game.popularity || 0,
    dateAdded: game.dateAdded || game.date_added || new Date().toISOString().split('T')[0]
  };
}

/**
 * 将文本转换为URL友好的slug
 * @param {string} text 需要转换的文本
 * @returns {string} slug
 */
function slugify(text) {
  return text
    .toString()
    .toLowerCase()
    .replace(/\s+/g, '-')        // 将空格替换为连字符
    .replace(/[^\w\-]+/g, '')    // 删除所有非单词字符
    .replace(/\-\-+/g, '-')      // 将多个连字符替换为单个连字符
    .replace(/^-+/, '')          // 删除开头的连字符
    .replace(/-+$/, '');         // 删除结尾的连字符
}

/**
 * 按分类获取游戏
 * @param {Array} games 游戏数组
 * @param {string} category 分类名称
 * @returns {Array} 筛选后的游戏数组
 */
export function getGamesByCategory(games, category) {
  if (!category) return games;
  
  return games.filter(game => 
    game.categories && 
    Array.isArray(game.categories) && 
    game.categories.some(cat => cat.toLowerCase() === category.toLowerCase())
  );
}

/**
 * 搜索游戏
 * @param {Array} games 游戏数组
 * @param {string} query 搜索关键词
 * @returns {Array} 搜索结果
 */
export function searchGames(games, query) {
  if (!query || query.trim() === '') return games;
  
  const searchTerms = query.toLowerCase().split(' ').filter(term => term.length > 0);
  
  return games.filter(game => {
    // 检查游戏标题
    const title = (game.title || '').toLowerCase();
    
    // 检查游戏描述
    const description = (game.description || '').toLowerCase();
    
    // 检查游戏分类
    const categories = (game.categories || []).map(cat => cat.toLowerCase()).join(' ');
    
    // 计算匹配分数
    let score = 0;
    
    for (const term of searchTerms) {
      // 标题匹配权重最高
      if (title.includes(term)) {
        score += 3;
      }
      
      // 分类匹配其次
      if (categories.includes(term)) {
        score += 2;
      }
      
      // 描述匹配权重最低
      if (description.includes(term)) {
        score += 1;
      }
    }
    
    // 至少有一个匹配项
    return score > 0;
  }).sort((a, b) => {
    // 计算a的匹配分数
    let scoreA = 0;
    for (const term of searchTerms) {
      if ((a.title || '').toLowerCase().includes(term)) scoreA += 3;
      if ((a.categories || []).map(cat => cat.toLowerCase()).join(' ').includes(term)) scoreA += 2;
      if ((a.description || '').toLowerCase().includes(term)) scoreA += 1;
    }
    
    // 计算b的匹配分数
    let scoreB = 0;
    for (const term of searchTerms) {
      if ((b.title || '').toLowerCase().includes(term)) scoreB += 3;
      if ((b.categories || []).map(cat => cat.toLowerCase()).join(' ').includes(term)) scoreB += 2;
      if ((b.description || '').toLowerCase().includes(term)) scoreB += 1;
    }
    
    // 按分数降序排序
    return scoreB - scoreA;
  });
}

/**
 * 获取游戏的URL
 * @param {string} slugOrTitle 游戏的slug或标题
 * @returns {string} 游戏页面URL
 */
export function getGameUrl(slugOrTitle) {
  if (!slugOrTitle) return '#';
  
  const slug = typeof slugOrTitle === 'string' ? 
    slugify(slugOrTitle) : 
    (slugOrTitle.slug ? slugOrTitle.slug : slugify(slugOrTitle.title || ''));
  
  return `/games/${slug}.html`;
}

/**
 * 获取游戏图片路径
 * @param {string} slugOrTitle 游戏的slug或标题
 * @returns {string} 游戏图片URL
 */
export function getGameImagePath(slugOrTitle) {
  if (!slugOrTitle) return '/images/game-placeholder.jpg';
  
  const slug = typeof slugOrTitle === 'string' ? 
    slugify(slugOrTitle) : 
    (slugOrTitle.slug ? slugOrTitle.slug : slugify(slugOrTitle.title || ''));
  
  return `/images/games/${slug}.jpg`;
}

/**
 * 随机获取游戏
 * @param {number} count 游戏数量
 * @param {Array} excludeGames 要排除的游戏
 * @returns {Promise<Array>} 随机游戏数组
 */
export async function getRandomGames(count = 4, excludeGames = []) {
  const games = await loadGameData();
  
  if (games.length === 0) return [];
  
  // 过滤掉排除的游戏
  let availableGames = games;
  
  if (excludeGames && excludeGames.length > 0) {
    const excludeSlugs = excludeGames.map(game => 
      typeof game === 'string' ? slugify(game) : (game.slug || slugify(game.title || ''))
    );
    
    availableGames = games.filter(game => 
      !excludeSlugs.includes(game.slug || slugify(game.title || ''))
    );
  }
  
  if (availableGames.length === 0) return [];
  
  // 如果可用游戏数量小于请求数量，返回所有可用游戏
  if (availableGames.length <= count) {
    return availableGames;
  }
  
  // 随机抽取指定数量的游戏
  const result = [];
  const indices = new Set();
  
  while (result.length < count && result.length < availableGames.length) {
    const randomIndex = Math.floor(Math.random() * availableGames.length);
    
    if (!indices.has(randomIndex)) {
      indices.add(randomIndex);
      result.push(availableGames[randomIndex]);
    }
  }
  
  return result;
}

/**
 * 获取相似游戏
 * @param {Object|string} game 游戏对象或slug
 * @param {number} count 游戏数量
 * @returns {Promise<Array>} 相似游戏数组
 */
export async function getSimilarGames(game, count = 4) {
  // 加载所有游戏
  const allGames = await loadGameData();
  
  // 如果没有游戏数据，返回空数组
  if (!allGames || allGames.length === 0) {
    return [];
  }
  
  // 如果是字符串，假设是slug或title，查找对应的游戏对象
  let targetGame = game;
  if (typeof game === 'string') {
    const slug = slugify(game);
    targetGame = allGames.find(g => g.slug === slug || slugify(g.title) === slug);
    
    // 如果找不到对应的游戏，返回随机游戏
    if (!targetGame) {
      return getRandomGames(count);
    }
  }
  
  // 如果没有分类信息，返回随机游戏
  if (!targetGame.categories || !Array.isArray(targetGame.categories) || targetGame.categories.length === 0) {
    return getRandomGames(count, [targetGame]);
  }
  
  // 过滤掉当前游戏
  const availableGames = allGames.filter(g => 
    g.slug !== targetGame.slug && 
    g.title !== targetGame.title
  );
  
  // 如果没有其他游戏，返回空数组
  if (availableGames.length === 0) {
    return [];
  }
  
  // 计算每个游戏与目标游戏的相似度
  const scoredGames = availableGames.map(g => {
    let score = 0;
    
    // 分类匹配
    if (g.categories && Array.isArray(g.categories)) {
      targetGame.categories.forEach(category => {
        if (g.categories.includes(category)) {
          score += 3; // 每个匹配的分类加3分
        }
      });
    }
    
    return { game: g, score };
  });
  
  // 按相似度降序排序
  scoredGames.sort((a, b) => b.score - a.score);
  
  // 如果没有相似游戏，返回随机游戏
  if (scoredGames[0].score === 0) {
    return getRandomGames(count, [targetGame]);
  }
  
  // 从相似度最高的游戏中选择前count个
  const result = scoredGames
    .filter(sg => sg.score > 0) // 只选择有相似度的游戏
    .slice(0, count)
    .map(sg => sg.game);
  
  // 如果相似游戏不足，补充随机游戏
  if (result.length < count) {
    const existingSlugs = result.map(g => g.slug);
    const randomGames = await getRandomGames(count - result.length, [...existingSlugs, targetGame]);
    result.push(...randomGames);
  }
  
  return result;
} 