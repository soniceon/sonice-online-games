/**
 * 游戏页面加载器
 * 根据URL参数加载游戏数据并填充游戏页面内容
 */

document.addEventListener('DOMContentLoaded', function() {
    // 初始化游戏页面
    initGamePage();
});

/**
 * 初始化游戏页面
 */
function initGamePage() {
    // 从URL获取游戏ID
    const gameId = getGameIdFromUrl();
    
    if (!gameId) {
        showErrorMessage('未找到游戏ID，请返回游戏列表重新选择。');
        return;
    }
    
    // 加载游戏数据
    loadGameData(gameId);
}

/**
 * 从URL获取游戏ID
 * @returns {string|null} 游戏ID或null
 */
function getGameIdFromUrl() {
    // 尝试从查询参数获取
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    
    if (id) return id;
    
    // 尝试从路径获取 (/games/game-id.html)
    const pathMatch = window.location.pathname.match(/\/games\/([^\/]+)\.html$/);
    return pathMatch ? pathMatch[1] : null;
}

/**
 * 加载游戏数据
 * @param {string} gameId 游戏ID
 */
function loadGameData(gameId) {
    fetch('/data/games.json')
        .then(response => {
            if (!response.ok) {
                throw new Error('无法加载游戏数据');
            }
            return response.json();
        })
        .then(games => {
            // 查找对应ID的游戏
            const game = games.find(g => g.id === gameId);
            
            if (!game) {
                throw new Error(`未找到ID为${gameId}的游戏`);
            }
            
            // 填充游戏数据到页面
            populateGameData(game);
            
            // 加载相关游戏
            loadRelatedGames(game, games);
        })
        .catch(error => {
            console.error('加载游戏数据失败:', error);
            showErrorMessage('无法加载游戏数据，请稍后再试。');
        });
}

/**
 * 将游戏数据填充到页面
 * @param {Object} game 游戏数据对象
 */
function populateGameData(game) {
    // 更新页面标题
    document.title = `${game.title} - Sonice.online`;
    document.getElementById('page-title').textContent = `${game.title} - Sonice.online`;
    
    // 更新元数据
    const metaDescription = document.getElementById('meta-description');
    if (metaDescription) {
        metaDescription.content = game.description || `Play ${game.title} online for free on Sonice.online`;
    }
    
    // 更新游戏标题
    const gameTitle = document.getElementById('game-title');
    if (gameTitle) {
        gameTitle.textContent = game.title;
    }
    
    // 更新游戏描述
    const gameDescription = document.getElementById('game-description');
    if (gameDescription) {
        gameDescription.innerHTML = `<p>${game.description || '暂无游戏描述'}</p>`;
        
        // 如果有游戏说明，添加到描述下方
        if (game.instructions) {
            gameDescription.innerHTML += `
                <div class="game-instructions">
                    <h3>游戏说明</h3>
                    <p>${game.instructions}</p>
                </div>
            `;
        }
    }
    
    // 更新游戏分类
    updateGameCategories(game.categories);
    
    // 加载游戏框架
    loadGameFrame(game);
}

/**
 * 更新游戏分类标签
 * @param {Array} categories 分类数组
 */
function updateGameCategories(categories) {
    const categoriesContainer = document.getElementById('game-categories');
    if (!categoriesContainer || !categories || !Array.isArray(categories)) return;
    
    let html = '';
    categories.forEach(category => {
        const categorySlug = category.toLowerCase().replace(/\s+/g, '-');
        html += `<a href="/pages/categories/${categorySlug}.html" class="category-tag">${category}</a>`;
    });
    
    categoriesContainer.innerHTML = html;
}

/**
 * 加载游戏框架
 * @param {Object} game 游戏数据
 */
function loadGameFrame(game) {
    const gameFrame = document.getElementById('game-frame');
    if (!gameFrame) return;
    
    if (game.url) {
        // 设置iframe源
        gameFrame.src = game.url;
        
        // 添加错误处理
        gameFrame.onerror = function() {
            console.error('游戏加载失败');
            gameFrame.parentNode.innerHTML = `
                <div class="game-error">
                    <p>游戏加载失败。请刷新页面或稍后再试。</p>
                    <button onclick="window.location.reload()" class="btn btn-primary">刷新页面</button>
                </div>
            `;
        };
    } else {
        // 没有游戏URL，显示错误信息
        gameFrame.parentNode.innerHTML = `
            <div class="game-error">
                <p>无法加载游戏。游戏链接不可用。</p>
            </div>
        `;
    }
}

/**
 * 加载相关游戏
 * @param {Object} currentGame 当前游戏
 * @param {Array} allGames 所有游戏数组
 */
function loadRelatedGames(currentGame, allGames) {
    const relatedGamesContainer = document.getElementById('related-games');
    if (!relatedGamesContainer) return;
    
    // 筛选相关游戏（同类别的其他游戏）
    let relatedGames = [];
    
    if (currentGame.categories && currentGame.categories.length > 0) {
        // 获取同类别的游戏
        relatedGames = allGames.filter(game => 
            game.id !== currentGame.id && // 排除当前游戏
            game.categories && 
            game.categories.some(cat => currentGame.categories.includes(cat))
        );
    }
    
    // 如果相关游戏不足6个，添加随机游戏
    if (relatedGames.length < 6) {
        const randomGames = allGames
            .filter(game => 
                game.id !== currentGame.id && 
                !relatedGames.some(relatedGame => relatedGame.id === game.id)
            )
            .sort(() => 0.5 - Math.random())
            .slice(0, 6 - relatedGames.length);
        
        relatedGames = [...relatedGames, ...randomGames];
    }
    
    // 限制为最多6个相关游戏
    relatedGames = relatedGames.slice(0, 6);
    
    // 渲染相关游戏
    let html = '';
    relatedGames.forEach(game => {
        html += `
            <div class="game-card">
                <a href="/pages/game.html?id=${game.id}">
                    <div class="game-image">
                        <img src="${game.image || '/images/placeholder.jpg'}" alt="${game.title}">
                    </div>
                    <div class="game-info">
                        <h3>${game.title}</h3>
                    </div>
                </a>
            </div>
        `;
    });
    
    relatedGamesContainer.innerHTML = html;
}

/**
 * 显示错误信息
 * @param {string} message 错误信息
 */
function showErrorMessage(message) {
    const gameContainer = document.querySelector('.game-container');
    if (gameContainer) {
        gameContainer.innerHTML = `
            <div class="error-message">
                <h2>出错了！</h2>
                <p>${message}</p>
                <a href="/pages/games.html" class="btn btn-primary">返回游戏列表</a>
            </div>
        `;
    } else {
        alert(message);
    }
} 