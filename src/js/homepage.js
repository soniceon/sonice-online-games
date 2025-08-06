/**
 * 首页游戏卡片管理和显示
 */
import { loadGameData, getGamesByCategory, getAllCategories, getGameImagePath, getGameUrl } from './gameData.js';

// 主要分类
const FEATURED_CATEGORIES = ['热门', '益智', '休闲', '射击', '经营', '竞速', '动作'];

// 初始化首页
async function initHomepage() {
  try {
    // 加载游戏数据
    const games = await loadGameData();
    if (!games || games.length === 0) {
      console.error('No games loaded');
      return;
    }
    
    // 显示特色分类
    displayFeaturedCategories(games);
    
    // 显示所有分类
    const allCategories = getAllCategories(games);
    displayCategoryNavigation(allCategories);
    
    // 初始化搜索功能
    initSearch(games);
  } catch (error) {
    console.error('Error initializing homepage:', error);
  }
}

// 显示特色分类
function displayFeaturedCategories(games) {
  FEATURED_CATEGORIES.forEach(category => {
    const categoryGames = getGamesByCategory(games, category, 8);
    displayCategorySection(category, categoryGames);
  });
}

// 显示单个分类区块
function displayCategorySection(category, games) {
  if (!games || games.length === 0) return;
  
  // 创建分类区块
  const section = document.createElement('section');
  section.className = 'game-category-section';
  section.id = `category-${category}`;
  
  // 创建标题
  const heading = document.createElement('h2');
  heading.textContent = category;
  section.appendChild(heading);
  
  // 创建游戏卡片容器
  const gameGrid = document.createElement('div');
  gameGrid.className = 'game-grid';
  
  // 添加游戏卡片
  games.forEach(game => {
    const gameCard = createGameCard(game);
    gameGrid.appendChild(gameCard);
  });
  
  section.appendChild(gameGrid);
  
  // 添加查看更多按钮
  const moreButton = document.createElement('a');
  moreButton.className = 'view-more-btn';
  moreButton.href = `/category/${category}.html`;
  moreButton.textContent = '查看更多';
  section.appendChild(moreButton);
  
  // 添加到页面
  const mainContent = document.querySelector('#main-content');
  if (mainContent) {
    mainContent.appendChild(section);
  }
}

// 创建游戏卡片
function createGameCard(game) {
  const card = document.createElement('div');
  card.className = 'game-card';
  card.dataset.id = game.id;
  
  const link = document.createElement('a');
  link.href = getGameUrl(game.id);
  
  // 游戏封面图
  const imgContainer = document.createElement('div');
  imgContainer.className = 'game-card-img';
  
  const img = document.createElement('img');
  img.src = getGameImagePath(game.id);
  img.alt = game.title;
  img.loading = 'lazy';
  // 添加图片加载失败时的处理
  img.onerror = "this.onerror=null; this.src='/assets/images/placeholder.webp';";
  
  imgContainer.appendChild(img);
  
  // 游戏标题
  const title = document.createElement('h3');
  title.className = 'game-card-title';
  title.textContent = game.title;
  
  // 游戏描述
  const description = document.createElement('p');
  description.className = 'game-card-description';
  description.textContent = game.description.substring(0, 80) + (game.description.length > 80 ? '...' : '');
  
  // 组装卡片
  link.appendChild(imgContainer);
  link.appendChild(title);
  card.appendChild(link);
  card.appendChild(description);
  
  return card;
}

// 显示分类导航
function displayCategoryNavigation(categories) {
  const categoryNav = document.querySelector('#category-nav');
  if (!categoryNav) return;
  
  categories.forEach(category => {
    const categoryLink = document.createElement('a');
    categoryLink.href = `/category/${category}.html`;
    categoryLink.textContent = category;
    categoryNav.appendChild(categoryLink);
  });
}

// 初始化搜索功能
function initSearch(games) {
  const searchInput = document.querySelector('#search-input');
  const searchResults = document.querySelector('#search-results');
  
  if (!searchInput || !searchResults) return;
  
  searchInput.addEventListener('input', () => {
    const query = searchInput.value.trim().toLowerCase();
    
    // 清空结果
    searchResults.innerHTML = '';
    
    if (query.length < 2) {
      searchResults.style.display = 'none';
      return;
    }
    
    // 搜索游戏
    const matchedGames = games.filter(game => 
      game.title.toLowerCase().includes(query) || 
      (game.description && game.description.toLowerCase().includes(query))
    ).slice(0, 5);
    
    // 显示结果
    if (matchedGames.length > 0) {
      matchedGames.forEach(game => {
        const resultItem = document.createElement('div');
        resultItem.className = 'search-result-item';
        
        const link = document.createElement('a');
        link.href = getGameUrl(game.id);
        link.textContent = game.title;
        
        resultItem.appendChild(link);
        searchResults.appendChild(resultItem);
      });
      
      searchResults.style.display = 'block';
    } else {
      searchResults.style.display = 'none';
    }
  });
  
  // 点击外部关闭搜索结果
  document.addEventListener('click', (event) => {
    if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
      searchResults.style.display = 'none';
    }
  });
}

// 当文档加载完成时执行初始化
document.addEventListener('DOMContentLoaded', initHomepage); 