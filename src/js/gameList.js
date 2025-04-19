/**
 * 游戏列表组件
 * 处理游戏列表的显示、加载和交互
 */

import { 
  loadGameData, 
  getGamesByCategory, 
  searchGames, 
  getGameImagePath, 
  getGameUrl 
} from './gameData.js';
import { getCategoryDisplayName } from './categoryList.js';

/**
 * 初始化游戏列表
 * @param {string} containerId 游戏列表容器ID
 * @param {Object} options 配置选项
 * @returns {Promise<Object>} 游戏数据
 */
export async function initGameList(containerId = 'game-list', options = {}) {
  try {
    const container = document.getElementById(containerId);
    if (!container) {
      console.error(`Game list container with ID '${containerId}' not found`);
      return { games: [] };
    }

    const defaultOptions = {
      category: null,
      searchQuery: '',
      sortBy: 'alphabetical',
      itemsPerPage: 24,
      currentPage: 1,
      showPagination: true
    };

    const settings = { ...defaultOptions, ...options };
    
    // 显示加载状态
    showLoading(container);
    
    // 加载游戏数据
    const allGames = await loadGameData();
    
    // 根据条件过滤游戏
    let filteredGames = allGames;
    
    if (settings.category) {
      filteredGames = getGamesByCategory(allGames, settings.category);
    }
    
    if (settings.searchQuery) {
      filteredGames = searchGames(filteredGames, settings.searchQuery);
    }
    
    // 排序游戏
    sortGames(filteredGames, settings.sortBy);
    
    // 分页处理
    const totalPages = Math.ceil(filteredGames.length / settings.itemsPerPage);
    const startIndex = (settings.currentPage - 1) * settings.itemsPerPage;
    const paginatedGames = filteredGames.slice(startIndex, startIndex + settings.itemsPerPage);
    
    // 渲染游戏列表
    renderGameList(container, paginatedGames);
    
    // 显示分页控件
    if (settings.showPagination && totalPages > 1) {
      renderPagination(container, settings.currentPage, totalPages, settings.category, settings.searchQuery);
    }
    
    return { 
      games: filteredGames,
      displayedGames: paginatedGames,
      totalGames: filteredGames.length, 
      totalPages 
    };
  } catch (error) {
    console.error('Failed to initialize game list:', error);
    return { games: [], totalGames: 0, totalPages: 0 };
  }
}

/**
 * 显示加载状态
 * @param {HTMLElement} container 容器元素
 */
function showLoading(container) {
  container.innerHTML = '<div class="loading-spinner"><span>加载中...</span></div>';
}

/**
 * 渲染游戏列表
 * @param {HTMLElement} container 容器元素
 * @param {Array} games 游戏数组
 */
function renderGameList(container, games) {
  // 清空容器
  container.innerHTML = '';
  
  // 如果没有游戏，显示提示信息
  if (!games || games.length === 0) {
    container.innerHTML = '<p class="empty-list">没有找到符合条件的游戏</p>';
    return;
  }
  
  // 创建游戏列表容器
  const gameGrid = document.createElement('div');
  gameGrid.className = 'game-grid';
  
  // 为每个游戏创建卡片
  games.forEach(game => {
    const gameCard = createGameCard(game);
    gameGrid.appendChild(gameCard);
  });
  
  container.appendChild(gameGrid);
}

/**
 * 创建游戏卡片
 * @param {Object} game 游戏对象
 * @returns {HTMLElement} 游戏卡片元素
 */
function createGameCard(game) {
  // 创建卡片容器
  const card = document.createElement('div');
  card.className = 'game-card';
  
  // 创建游戏链接
  const link = document.createElement('a');
  link.href = getGameUrl(game.slug || game.title);
  link.className = 'game-card-link';
  
  // 创建游戏图片
  const imageContainer = document.createElement('div');
  imageContainer.className = 'game-image-container';
  
  const image = document.createElement('img');
  image.src = getGameImagePath(game.slug || game.title);
  image.alt = game.title;
  image.loading = 'lazy';
  image.onerror = "this.onerror=null; this.src='/images/game-placeholder.jpg';";
  
  imageContainer.appendChild(image);
  
  // 创建游戏标题
  const title = document.createElement('h3');
  title.className = 'game-title';
  title.textContent = game.title;
  
  // 创建游戏分类标签（如果有）
  const categories = document.createElement('div');
  categories.className = 'game-categories';
  
  if (game.categories && game.categories.length > 0) {
    const firstCategory = game.categories[0];
    const categoryTag = document.createElement('span');
    categoryTag.className = 'category-tag';
    categoryTag.textContent = getCategoryDisplayName(firstCategory);
    categories.appendChild(categoryTag);
  }
  
  // 组装卡片
  link.appendChild(imageContainer);
  link.appendChild(title);
  link.appendChild(categories);
  card.appendChild(link);
  
  return card;
}

/**
 * 渲染分页控件
 * @param {HTMLElement} container 容器元素
 * @param {number} currentPage 当前页码
 * @param {number} totalPages 总页数
 * @param {string} category 当前分类
 * @param {string} searchQuery 搜索查询
 */
function renderPagination(container, currentPage, totalPages, category, searchQuery) {
  const pagination = document.createElement('div');
  pagination.className = 'pagination';
  
  // 上一页按钮
  const prevButton = document.createElement('a');
  prevButton.className = 'pagination-button prev';
  prevButton.textContent = '上一页';
  prevButton.href = createPageUrl(category, searchQuery, Math.max(1, currentPage - 1));
  prevButton.disabled = currentPage <= 1;
  if (currentPage <= 1) {
    prevButton.classList.add('disabled');
  }
  
  // 下一页按钮
  const nextButton = document.createElement('a');
  nextButton.className = 'pagination-button next';
  nextButton.textContent = '下一页';
  nextButton.href = createPageUrl(category, searchQuery, Math.min(totalPages, currentPage + 1));
  nextButton.disabled = currentPage >= totalPages;
  if (currentPage >= totalPages) {
    nextButton.classList.add('disabled');
  }
  
  // 页码指示器
  const pageIndicator = document.createElement('span');
  pageIndicator.className = 'page-indicator';
  pageIndicator.textContent = `${currentPage} / ${totalPages}`;
  
  // 添加事件处理
  prevButton.addEventListener('click', (e) => {
    if (currentPage > 1) {
      e.preventDefault();
      navigateToPage(category, searchQuery, currentPage - 1);
    }
  });
  
  nextButton.addEventListener('click', (e) => {
    if (currentPage < totalPages) {
      e.preventDefault();
      navigateToPage(category, searchQuery, currentPage + 1);
    }
  });
  
  // 组装分页控件
  pagination.appendChild(prevButton);
  pagination.appendChild(pageIndicator);
  pagination.appendChild(nextButton);
  
  container.appendChild(pagination);
}

/**
 * 创建分页链接
 * @param {string} category 分类
 * @param {string} searchQuery 搜索查询
 * @param {number} page 页码
 * @returns {string} 分页URL
 */
function createPageUrl(category, searchQuery, page) {
  const urlParams = new URLSearchParams(window.location.search);
  
  if (page) {
    urlParams.set('page', page);
  }
  
  if (category) {
    urlParams.set('category', category);
  }
  
  if (searchQuery) {
    urlParams.set('search', searchQuery);
  }
  
  return `${window.location.pathname}?${urlParams.toString()}`;
}

/**
 * 导航到指定页码
 * @param {string} category 分类
 * @param {string} searchQuery 搜索查询
 * @param {number} page 页码
 */
function navigateToPage(category, searchQuery, page) {
  window.location.href = createPageUrl(category, searchQuery, page);
}

/**
 * 排序游戏列表
 * @param {Array} games 游戏数组
 * @param {string} sortBy 排序方式
 */
function sortGames(games, sortBy = 'alphabetical') {
  switch (sortBy) {
    case 'alphabetical':
      games.sort((a, b) => a.title.localeCompare(b.title));
      break;
    case 'popularity':
      // 如果有流行度数据，可以在这里实现
      games.sort((a, b) => (b.popularity || 0) - (a.popularity || 0));
      break;
    case 'newest':
      // 如果有添加日期数据，可以在这里实现
      games.sort((a, b) => new Date(b.dateAdded || 0) - new Date(a.dateAdded || 0));
      break;
    default:
      // 默认按字母排序
      games.sort((a, b) => a.title.localeCompare(b.title));
  }
}

/**
 * 设置游戏列表排序方式
 * @param {Array} games 游戏数组
 * @param {string} sortBy 排序方式
 * @param {string} containerId 容器ID
 */
export function setSortOrder(games, sortBy, containerId = 'game-list') {
  if (!games || games.length === 0) return;
  
  // 排序游戏
  sortGames(games, sortBy);
  
  // 更新URL参数
  const urlParams = new URLSearchParams(window.location.search);
  urlParams.set('sort', sortBy);
  
  // 更新排序按钮的活跃状态
  const sortButtons = document.querySelectorAll('.sort-button');
  if (sortButtons) {
    sortButtons.forEach(button => {
      if (button.dataset.sort === sortBy) {
        button.classList.add('active');
      } else {
        button.classList.remove('active');
      }
    });
  }
  
  // 重新渲染游戏列表
  const container = document.getElementById(containerId);
  if (container) {
    renderGameList(container, games);
  }
  
  // 更新URL，但不刷新页面
  window.history.replaceState(
    {}, 
    '', 
    `${window.location.pathname}?${urlParams.toString()}`
  );
}

/**
 * 添加搜索功能
 * @param {string} searchInputId 搜索输入框ID
 * @param {string} gameListId 游戏列表容器ID
 */
export function setupSearch(searchInputId = 'search-input', gameListId = 'game-list') {
  const searchInput = document.getElementById(searchInputId);
  if (!searchInput) return;
  
  // 添加输入事件处理
  searchInput.addEventListener('input', debounce(async () => {
    const query = searchInput.value.trim();
    
    // 更新URL参数
    const urlParams = new URLSearchParams(window.location.search);
    if (query) {
      urlParams.set('search', query);
    } else {
      urlParams.delete('search');
    }
    
    // 更新URL，但不刷新页面
    window.history.replaceState(
      {}, 
      '', 
      `${window.location.pathname}?${urlParams.toString()}`
    );
    
    // 获取当前分类
    const category = urlParams.get('category');
    
    // 重新初始化游戏列表
    await initGameList(gameListId, {
      category,
      searchQuery: query,
      currentPage: 1
    });
  }, 300));
}

/**
 * 防抖函数
 * @param {Function} func 要执行的函数
 * @param {number} wait 等待时间（毫秒）
 * @returns {Function} 防抖处理后的函数
 */
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
} 