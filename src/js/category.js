/**
 * 分类页面游戏显示和管理
 */
import { loadGameData, getGamesByCategory, getAllCategories, getGameImagePath, getGameUrl } from './gameData.js';

// 每页显示的游戏数量
const GAMES_PER_PAGE = 12;

// 初始化分类页面
async function initCategoryPage() {
  try {
    // 获取当前页面分类
    const categoryName = getCategoryFromUrl();
    if (!categoryName) {
      console.error('无法确定当前分类');
      return;
    }
    
    // 加载游戏数据
    const games = await loadGameData();
    if (!games || games.length === 0) {
      console.error('No games loaded');
      return;
    }
    
    // 显示分类标题
    displayCategoryTitle(categoryName);
    
    // 显示该分类下的游戏
    const categoryGames = getGamesByCategory(games, categoryName);
    displayCategoryGames(categoryGames);
    
    // 显示分页
    if (categoryGames.length > GAMES_PER_PAGE) {
      setupPagination(categoryGames);
    }
    
    // 显示所有分类导航
    const allCategories = getAllCategories(games);
    updateCategoryNavigation(allCategories, categoryName);
  } catch (error) {
    console.error('Error initializing category page:', error);
  }
}

// 从URL获取当前分类
function getCategoryFromUrl() {
  // 通常URL格式为 /category/分类名.html
  const pathname = window.location.pathname;
  const match = pathname.match(/\/category\/([^\/]+)\.html/);
  return match ? decodeURIComponent(match[1]) : null;
}

// 显示分类标题
function displayCategoryTitle(categoryName) {
  const titleElement = document.querySelector('.category-title');
  if (titleElement) {
    titleElement.textContent = categoryName;
  }
  
  // 更新页面标题
  document.title = `${categoryName} 游戏 - Sonice Online Games`;
}

// 显示分类游戏
function displayCategoryGames(games, page = 1) {
  const gameContainer = document.querySelector('.category-games');
  if (!gameContainer) return;
  
  // 清空容器
  gameContainer.innerHTML = '';
  
  if (games.length === 0) {
    const noGames = document.createElement('p');
    noGames.className = 'no-games';
    noGames.textContent = '该分类下暂无游戏';
    gameContainer.appendChild(noGames);
    return;
  }
  
  // 计算当前页面显示的游戏
  const startIndex = (page - 1) * GAMES_PER_PAGE;
  const endIndex = Math.min(startIndex + GAMES_PER_PAGE, games.length);
  const pageGames = games.slice(startIndex, endIndex);
  
  // 创建游戏网格
  const gameGrid = document.createElement('div');
  gameGrid.className = 'game-grid';
  
  // 添加游戏卡片
  pageGames.forEach(game => {
    const gameCard = createGameCard(game);
    gameGrid.appendChild(gameCard);
  });
  
  gameContainer.appendChild(gameGrid);
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

// 设置分页
function setupPagination(games) {
  const paginationContainer = document.querySelector('.pagination');
  if (!paginationContainer) return;
  
  // 清空容器
  paginationContainer.innerHTML = '';
  
  // 计算总页数
  const totalPages = Math.ceil(games.length / GAMES_PER_PAGE);
  if (totalPages <= 1) return;
  
  // 获取当前页码
  const currentPage = parseInt(new URLSearchParams(window.location.search).get('page')) || 1;
  
  // 创建分页控件
  // 上一页按钮
  if (currentPage > 1) {
    const prevButton = createPageButton(currentPage - 1, '上一页', 'prev-page');
    paginationContainer.appendChild(prevButton);
  }
  
  // 页码按钮
  let startPage = Math.max(1, currentPage - 2);
  let endPage = Math.min(totalPages, startPage + 4);
  
  // 调整起始页，确保显示5个页码按钮（如果有足够的页数）
  if (endPage - startPage < 4 && totalPages > 5) {
    startPage = Math.max(1, endPage - 4);
  }
  
  for (let i = startPage; i <= endPage; i++) {
    const pageButton = createPageButton(i, i.toString(), i === currentPage ? 'current-page' : '');
    paginationContainer.appendChild(pageButton);
  }
  
  // 下一页按钮
  if (currentPage < totalPages) {
    const nextButton = createPageButton(currentPage + 1, '下一页', 'next-page');
    paginationContainer.appendChild(nextButton);
  }
  
  // 显示当前页面的游戏
  displayCategoryGames(games, currentPage);
}

// 创建分页按钮
function createPageButton(pageNum, text, className = '') {
  const button = document.createElement('a');
  button.href = `?page=${pageNum}`;
  button.textContent = text;
  if (className) {
    button.className = className;
  }
  
  button.addEventListener('click', (event) => {
    event.preventDefault();
    
    // 更新URL，不刷新页面
    const url = new URL(window.location);
    url.searchParams.set('page', pageNum);
    window.history.pushState({}, '', url);
    
    // 获取当前分类的游戏并更新显示
    const categoryName = getCategoryFromUrl();
    loadGameData().then(games => {
      const categoryGames = getGamesByCategory(games, categoryName);
      displayCategoryGames(categoryGames, pageNum);
      setupPagination(categoryGames);
    });
  });
  
  return button;
}

// 更新分类导航
function updateCategoryNavigation(categories, currentCategory) {
  const categoryNav = document.querySelector('#category-nav');
  if (!categoryNav) return;
  
  // 清空导航
  categoryNav.innerHTML = '';
  
  // 添加首页链接
  const homeLink = document.createElement('a');
  homeLink.href = '/';
  homeLink.textContent = '首页';
  categoryNav.appendChild(homeLink);
  
  // 添加所有分类链接
  categories.forEach(category => {
    const categoryLink = document.createElement('a');
    categoryLink.href = `/category/${category}.html`;
    categoryLink.textContent = category;
    
    // 高亮当前分类
    if (category === currentCategory) {
      categoryLink.classList.add('active');
    }
    
    categoryNav.appendChild(categoryLink);
  });
}

// 当文档加载完成时执行初始化
document.addEventListener('DOMContentLoaded', initCategoryPage); 