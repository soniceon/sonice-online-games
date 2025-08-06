/**
 * 分类列表组件
 * 处理分类导航的显示、加载和交互
 */

import { loadGameData, getAllCategories } from './gameData.js';

/**
 * 初始化分类列表
 * @param {string} containerId 分类列表容器ID
 * @param {Function} onCategoryClick 分类点击回调函数
 */
export async function initCategoryList(containerId = 'category-list', onCategoryClick = null) {
  try {
    // 获取容器元素
    const container = document.getElementById(containerId);
    if (!container) {
      console.error(`Category list container with ID '${containerId}' not found`);
      return;
    }

    // 加载游戏数据
    const games = await loadGameData();
    
    // 获取所有分类
    const categories = getAllCategories(games);
    
    // 构建分类列表
    renderCategoryList(container, categories, onCategoryClick);
    
    // 突出显示当前分类（如果在分类页面）
    highlightCurrentCategory();
    
    return { games, categories };
  } catch (error) {
    console.error('Failed to initialize category list:', error);
    return { games: [], categories: [] };
  }
}

/**
 * 渲染分类列表
 * @param {HTMLElement} container 容器元素
 * @param {Array} categories 分类数组
 * @param {Function} onCategoryClick 点击回调
 */
function renderCategoryList(container, categories, onCategoryClick) {
  // 清空容器
  container.innerHTML = '';
  
  // 如果没有分类，显示提示信息
  if (!categories || categories.length === 0) {
    container.innerHTML = '<p class="empty-list">没有可用的游戏分类</p>';
    return;
  }
  
  // 创建分类列表
  const ul = document.createElement('ul');
  ul.className = 'category-list';
  
  // 添加"全部游戏"选项
  const allGamesItem = document.createElement('li');
  const allGamesLink = document.createElement('a');
  allGamesLink.href = '/all-games.html';
  allGamesLink.textContent = '全部游戏';
  allGamesLink.dataset.category = 'all';
  allGamesItem.appendChild(allGamesLink);
  ul.appendChild(allGamesItem);
  
  // 为每个分类创建列表项
  categories.forEach(category => {
    const li = document.createElement('li');
    const a = document.createElement('a');
    
    // 设置链接属性
    a.href = `/category/${encodeURIComponent(category.toLowerCase())}.html`;
    a.textContent = category;
    a.dataset.category = category.toLowerCase();
    
    // 添加点击事件处理
    if (onCategoryClick) {
      a.addEventListener('click', (e) => {
        e.preventDefault();
        onCategoryClick(category);
      });
    }
    
    li.appendChild(a);
    ul.appendChild(li);
  });
  
  container.appendChild(ul);
}

/**
 * 突出显示当前分类
 */
function highlightCurrentCategory() {
  // 从URL获取当前分类
  const currentPath = window.location.pathname;
  
  // 检查是否在分类页面
  if (currentPath.includes('/category/')) {
    const categoryMatch = currentPath.match(/\/category\/([^/]+)\.html/);
    if (categoryMatch && categoryMatch[1]) {
      const currentCategory = decodeURIComponent(categoryMatch[1]).toLowerCase();
      
      // 查找并高亮当前分类链接
      const categoryLinks = document.querySelectorAll('.category-list a');
      categoryLinks.forEach(link => {
        if (link.dataset.category === currentCategory) {
          link.classList.add('active');
        } else {
          link.classList.remove('active');
        }
      });
    }
  } else if (currentPath.includes('/all-games.html')) {
    // 如果是"全部游戏"页面
    const allGamesLink = document.querySelector('.category-list a[data-category="all"]');
    if (allGamesLink) {
      allGamesLink.classList.add('active');
    }
  }
}

/**
 * 获取分类的中文名称（可根据需要扩展）
 * @param {string} category 分类英文名
 * @returns {string} 分类中文名
 */
export function getCategoryDisplayName(category) {
  if (!category) return '';
  
  // 这里可以添加分类名称的映射
  const categoryMap = {
    'action': '动作游戏',
    'puzzle': '益智游戏',
    'arcade': '街机游戏',
    'racing': '赛车游戏',
    'sports': '体育游戏',
    'strategy': '策略游戏',
    'simulation': '模拟游戏',
    'adventure': '冒险游戏',
    'card': '卡牌游戏',
    'board': '棋盘游戏',
    'shooting': '射击游戏',
    'fighting': '格斗游戏',
    'tower defense': '塔防游戏',
    'platform': '平台游戏',
    'rpg': '角色扮演',
    'clicker': '点击游戏',
    'multiplayer': '多人游戏',
    'io': 'IO游戏',
    'html5': 'HTML5游戏',
    'classic': '经典游戏',
    // 可以根据需要添加更多映射
  };
  
  return categoryMap[category.toLowerCase()] || category;
}

/**
 * 更新分类导航的活跃状态
 * @param {string} category 当前分类
 */
export function updateActiveCategoryInNav(category) {
  if (!category) return;
  
  const categoryLinks = document.querySelectorAll('.category-list a');
  categoryLinks.forEach(link => {
    if (link.dataset.category === category.toLowerCase()) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });
} 