/**
 * 游戏管理页面JavaScript
 * 用于处理管理后台游戏列表和批量生成游戏页面功能
 */

document.addEventListener('DOMContentLoaded', function() {
    // 检查是否在管理页面
    if (!document.querySelector('.admin-section')) return;
    
    // 初始化界面元素
    initAdminInterface();
    
    // 加载游戏列表
    loadAdminGamesList();
    
    // 加载分类筛选器选项
    loadCategoryFilterOptions();
});

/**
 * 初始化管理界面元素和事件
 */
function initAdminInterface() {
    // 添加游戏按钮
    const addGameBtn = document.getElementById('add-game-btn');
    if (addGameBtn) {
        addGameBtn.addEventListener('click', function() {
            openGameEditModal();
        });
    }
    
    // 批量生成按钮
    const bulkGenerateBtn = document.getElementById('bulk-generate-btn');
    if (bulkGenerateBtn) {
        bulkGenerateBtn.addEventListener('click', function() {
            openBulkGenerateModal();
        });
    }
    
    // 搜索框
    const searchInput = document.getElementById('search-games');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterGames();
        });
    }
    
    // 分类筛选器
    const categoryFilter = document.getElementById('category-filter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            filterGames();
        });
    }
    
    // 状态筛选器
    const statusFilter = document.getElementById('status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            filterGames();
        });
    }
    
    // 已生成页面筛选器
    const showGeneratedCheckbox = document.getElementById('show-generated');
    if (showGeneratedCheckbox) {
        showGeneratedCheckbox.addEventListener('change', function() {
            filterGames();
        });
    }
    
    // 模态框关闭按钮
    const closeModalButtons = document.querySelectorAll('.close-modal');
    closeModalButtons.forEach(button => {
        button.addEventListener('click', function() {
            closeAllModals();
        });
    });
    
    // 游戏编辑表单提交
    const gameEditForm = document.getElementById('game-edit-form');
    if (gameEditForm) {
        gameEditForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveGameData();
        });
    }
    
    // 取消按钮
    const cancelButtons = document.querySelectorAll('.cancel-btn');
    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            closeAllModals();
        });
    });
    
    // 进度模态框关闭按钮
    const closeProgressBtn = document.getElementById('close-progress-btn');
    if (closeProgressBtn) {
        closeProgressBtn.addEventListener('click', function() {
            closeProgressModal();
        });
    }
    
    // 分页按钮
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');
    
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', function() {
            changePage(-1);
        });
    }
    
    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', function() {
            changePage(1);
        });
    }
}

/**
 * 加载管理页面的游戏列表
 */
function loadAdminGamesList() {
    const gamesList = document.getElementById('games-list');
    if (!gamesList) return;
    
    // 显示加载中
    gamesList.innerHTML = '<div class="loading">加载中...</div>';
    
    // 获取游戏数据
    fetch('/data/games.json')
        .then(response => {
            if (!response.ok) throw new Error('获取游戏数据失败');
            return response.json();
        })
        .then(games => {
            // 保存游戏数据到全局变量
            window.adminGamesData = games;
            
            // 检查games/目录中已生成的页面
            checkGeneratedPages(games)
                .then(() => {
                    // 更新游戏列表
                    renderAdminGamesList(games);
                });
        })
        .catch(error => {
            console.error('加载游戏列表失败:', error);
            gamesList.innerHTML = '<div class="error">加载游戏列表失败</div>';
        });
}

/**
 * 检查哪些游戏已经生成了页面
 */
async function checkGeneratedPages(games) {
    try {
        // 使用fetch检查pages/games/目录
        const response = await fetch('/api/check-generated-pages.php');
        if (!response.ok) return;
        
        const generatedPages = await response.json();
        const generatedIds = new Set();
        
        // 从文件名提取ID
        generatedPages.forEach(page => {
            if (page.match(/^game-(\d+)\.html$/)) {
                const id = page.match(/^game-(\d+)\.html$/)[1];
                generatedIds.add(id);
            }
        });
        
        // 更新游戏数据
        games.forEach(game => {
            game.pageGenerated = generatedIds.has(game.id.toString());
        });
    } catch (error) {
        console.error('检查生成页面失败:', error);
    }
}

/**
 * 渲染管理页面的游戏列表
 */
function renderAdminGamesList(games, page = 1) {
    const gamesList = document.getElementById('games-list');
    if (!gamesList) return;
    
    // 清空列表
    gamesList.innerHTML = '';
    
    // 分页设置
    const itemsPerPage = 20;
    const startIndex = (page - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const totalPages = Math.ceil(games.length / itemsPerPage);
    
    // 更新分页信息
    document.getElementById('page-info').textContent = `第 ${page} 页 / 共 ${totalPages} 页`;
    document.getElementById('prev-page').disabled = page <= 1;
    document.getElementById('next-page').disabled = page >= totalPages;
    
    // 保存当前页码到全局变量
    window.currentPage = page;
    
    // 获取当前页的游戏
    const currentPageGames = games.slice(startIndex, endIndex);
    
    // 如果没有游戏
    if (currentPageGames.length === 0) {
        gamesList.innerHTML = '<div class="no-results">没有找到游戏</div>';
        return;
    }
    
    // 创建游戏列表项
    currentPageGames.forEach(game => {
        const gameItem = document.createElement('div');
        gameItem.className = 'game-item admin-game-item';
        gameItem.dataset.gameId = game.id;
        
        // 设置游戏状态类
        if (game.status === 'inactive') {
            gameItem.classList.add('inactive');
        }
        
        // 生成页面状态标记
        let pageStatusBadge = '';
        if (game.pageGenerated) {
            pageStatusBadge = '<span class="badge success">已生成页面</span>';
        } else {
            pageStatusBadge = '<span class="badge">未生成</span>';
        }
        
        // 游戏元素HTML
        gameItem.innerHTML = `
            <div class="game-thumbnail">
                <img src="${game.image || '/images/no-image.png'}" alt="${game.title}">
            </div>
            <div class="game-info">
                <h3 class="game-title">${game.title}</h3>
                <div class="game-meta">
                    <span class="game-id">ID: ${game.id}</span>
                    ${pageStatusBadge}
                    ${game.status === 'inactive' ? '<span class="badge warning">未激活</span>' : ''}
                </div>
                <p class="game-description">${game.description || '无描述'}</p>
                <div class="game-categories">
                    ${(game.categories || []).map(cat => `<span class="category">${cat}</span>`).join('')}
                </div>
            </div>
            <div class="game-actions">
                <button class="edit-btn" data-id="${game.id}">编辑</button>
                <button class="generate-page-btn" data-id="${game.id}">${game.pageGenerated ? '重新生成' : '生成页面'}</button>
                <button class="delete-btn" data-id="${game.id}">删除</button>
            </div>
        `;
        
        gamesList.appendChild(gameItem);
        
        // 添加编辑按钮事件
        gameItem.querySelector('.edit-btn').addEventListener('click', function() {
            openGameEditModal(game.id);
        });
        
        // 添加生成页面按钮事件
        gameItem.querySelector('.generate-page-btn').addEventListener('click', function() {
            generateGamePage(game.id);
        });
        
        // 添加删除按钮事件
        gameItem.querySelector('.delete-btn').addEventListener('click', function() {
            if (confirm(`确认删除游戏"${game.title}"吗？`)) {
                deleteGame(game.id);
            }
        });
    });
}

/**
 * 加载分类筛选器选项
 */
function loadCategoryFilterOptions() {
    const categoryFilter = document.getElementById('category-filter');
    if (!categoryFilter) return;
    
    // 获取分类数据
    fetch('/data/categories.json')
        .then(response => {
            if (!response.ok) throw new Error('获取分类数据失败');
            return response.json();
        })
        .then(categories => {
            // 清空现有选项（除了第一个）
            while (categoryFilter.options.length > 1) {
                categoryFilter.remove(1);
            }
            
            // 添加分类选项
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categoryFilter.appendChild(option);
            });
        })
        .catch(error => {
            console.error('加载分类选项失败:', error);
        });
}

/**
 * 根据筛选条件过滤游戏列表
 */
function filterGames() {
    // 获取筛选条件
    const searchText = document.getElementById('search-games').value.toLowerCase();
    const categoryValue = document.getElementById('category-filter').value;
    const statusValue = document.getElementById('status-filter').value;
    const showGeneratedOnly = document.getElementById('show-generated').checked;
    
    // 获取游戏数据
    const games = window.adminGamesData || [];
    
    // 过滤游戏
    const filteredGames = games.filter(game => {
        // 搜索文本筛选
        const matchesSearch = searchText === '' || 
                             game.title.toLowerCase().includes(searchText) || 
                             (game.description && game.description.toLowerCase().includes(searchText));
        
        // 分类筛选
        const matchesCategory = categoryValue === '' || 
                               (game.categories && game.categories.includes(categoryValue));
        
        // 状态筛选
        const matchesStatus = statusValue === '' || game.status === statusValue;
        
        // 已生成页面筛选
        const matchesGenerated = !showGeneratedOnly || game.pageGenerated;
        
        return matchesSearch && matchesCategory && matchesStatus && matchesGenerated;
    });
    
    // 渲染过滤后的游戏列表
    renderAdminGamesList(filteredGames, 1);
}

/**
 * 切换页码
 */
function changePage(direction) {
    const currentPage = window.currentPage || 1;
    const newPage = currentPage + direction;
    
    // 获取过滤后的游戏数据
    const filteredGames = getFilteredGames();
    
    // 检查页码范围
    const itemsPerPage = 20;
    const totalPages = Math.ceil(filteredGames.length / itemsPerPage);
    
    if (newPage >= 1 && newPage <= totalPages) {
        renderAdminGamesList(filteredGames, newPage);
    }
}

/**
 * 获取当前过滤条件下的游戏列表
 */
function getFilteredGames() {
    // 获取筛选条件
    const searchText = document.getElementById('search-games').value.toLowerCase();
    const categoryValue = document.getElementById('category-filter').value;
    const statusValue = document.getElementById('status-filter').value;
    const showGeneratedOnly = document.getElementById('show-generated').checked;
    
    // 获取游戏数据
    const games = window.adminGamesData || [];
    
    // 过滤游戏
    return games.filter(game => {
        // 搜索文本筛选
        const matchesSearch = searchText === '' || 
                             game.title.toLowerCase().includes(searchText) || 
                             (game.description && game.description.toLowerCase().includes(searchText));
        
        // 分类筛选
        const matchesCategory = categoryValue === '' || 
                               (game.categories && game.categories.includes(categoryValue));
        
        // 状态筛选
        const matchesStatus = statusValue === '' || game.status === statusValue;
        
        // 已生成页面筛选
        const matchesGenerated = !showGeneratedOnly || game.pageGenerated;
        
        return matchesSearch && matchesCategory && matchesStatus && matchesGenerated;
    });
}

/**
 * 打开游戏编辑模态框
 */
function openGameEditModal(gameId = null) {
    const modal = document.getElementById('game-edit-modal');
    if (!modal) return;
    
    // 清空表单
    document.getElementById('game-edit-form').reset();
    
    // 设置标题
    modal.querySelector('h2').textContent = gameId ? '编辑游戏' : '添加游戏';
    
    // 如果是编辑现有游戏
    if (gameId) {
        const games = window.adminGamesData || [];
        const game = games.find(g => g.id.toString() === gameId.toString());
        
        if (game) {
            // 填充表单数据
            document.getElementById('edit-game-id').value = game.id;
            document.getElementById('edit-title').value = game.title || '';
            document.getElementById('edit-url').value = game.url || '';
            document.getElementById('edit-description').value = game.description || '';
            document.getElementById('edit-image').value = game.image || '';
            document.getElementById('edit-tags').value = game.tags ? game.tags.join(', ') : '';
            document.getElementById('edit-status').value = game.status || 'active';
            
            // 选择分类（需要异步加载分类选项）
            loadEditCategoriesOptions(game.categories || []);
        }
    } else {
        // 设置新游戏默认值
        document.getElementById('edit-game-id').value = '';
        
        // 加载分类选项
        loadEditCategoriesOptions([]);
    }
    
    // 显示模态框
    modal.style.display = 'block';
}

/**
 * 加载编辑框的分类选项
 */
function loadEditCategoriesOptions(selectedCategories = []) {
    const categoriesSelect = document.getElementById('edit-categories');
    if (!categoriesSelect) return;
    
    // 获取分类数据
    fetch('/data/categories.json')
        .then(response => {
            if (!response.ok) throw new Error('获取分类数据失败');
            return response.json();
        })
        .then(categories => {
            // 清空现有选项
            categoriesSelect.innerHTML = '';
            
            // 添加分类选项
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                option.selected = selectedCategories.includes(category.id);
                categoriesSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('加载分类选项失败:', error);
        });
}

/**
 * 保存游戏数据
 */
function saveGameData() {
    // 获取表单数据
    const gameId = document.getElementById('edit-game-id').value;
    const isNewGame = !gameId;
    
    // 构建游戏数据对象
    const gameData = {
        id: isNewGame ? generateNewGameId() : gameId,
        title: document.getElementById('edit-title').value,
        url: document.getElementById('edit-url').value,
        description: document.getElementById('edit-description').value,
        image: document.getElementById('edit-image').value,
        status: document.getElementById('edit-status').value
    };
    
    // 处理分类
    const categoriesSelect = document.getElementById('edit-categories');
    gameData.categories = Array.from(categoriesSelect.selectedOptions).map(option => option.value);
    
    // 处理标签
    const tagsText = document.getElementById('edit-tags').value;
    if (tagsText) {
        gameData.tags = tagsText.split(',').map(tag => tag.trim()).filter(tag => tag);
    } else {
        gameData.tags = [];
    }
    
    // 向API发送请求保存数据
    fetch('/api/save-game.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(gameData)
    })
    .then(response => {
        if (!response.ok) throw new Error('保存游戏数据失败');
        return response.json();
    })
    .then(result => {
        if (result.success) {
            // 关闭模态框
            closeAllModals();
            
            // 更新游戏列表
            loadAdminGamesList();
            
            // 显示成功消息
            alert(isNewGame ? '游戏添加成功' : '游戏更新成功');
        } else {
            alert('保存失败: ' + result.error);
        }
    })
    .catch(error => {
        console.error('保存游戏数据失败:', error);
        alert('保存失败: ' + error.message);
    });
}

/**
 * 生成新游戏ID
 */
function generateNewGameId() {
    const games = window.adminGamesData || [];
    if (games.length === 0) return 1;
    
    // 找出最大ID值
    const maxId = Math.max(...games.map(game => parseInt(game.id)));
    return maxId + 1;
}

/**
 * 删除游戏
 */
function deleteGame(gameId) {
    // 向API发送请求删除游戏
    fetch('/api/delete-game.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: gameId
        })
    })
    .then(response => {
        if (!response.ok) throw new Error('删除游戏失败');
        return response.json();
    })
    .then(result => {
        if (result.success) {
            // 更新游戏列表
            loadAdminGamesList();
            
            // 显示成功消息
            alert('游戏删除成功');
        } else {
            alert('删除失败: ' + result.error);
        }
    })
    .catch(error => {
        console.error('删除游戏失败:', error);
        alert('删除失败: ' + error.message);
    });
}

/**
 * 关闭所有模态框
 */
function closeAllModals() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.style.display = 'none';
    });
}

/**
 * 打开批量生成模态框
 */
function openBulkGenerateModal() {
    const modal = document.getElementById('generate-progress-modal');
    if (!modal) return;
    
    // 重置进度
    document.getElementById('generate-progress-bar').style.width = '0%';
    document.getElementById('generate-progress-text').textContent = '0%';
    document.getElementById('generate-status').textContent = '准备中...';
    document.getElementById('generate-count').textContent = '0/0 完成';
    document.getElementById('generate-log').innerHTML = '';
    document.getElementById('close-progress-btn').disabled = true;
    
    // 显示模态框
    modal.style.display = 'block';
    
    // 开始批量生成
    startBulkGeneration();
}

/**
 * 开始批量生成游戏页面
 */
function startBulkGeneration() {
    // 获取游戏数据
    const games = getFilteredGames();
    const totalGames = games.length;
    
    if (totalGames === 0) {
        document.getElementById('generate-status').textContent = '没有找到符合条件的游戏';
        document.getElementById('close-progress-btn').disabled = false;
        return;
    }
    
    // 更新状态
    document.getElementById('generate-status').textContent = `开始生成，共 ${totalGames} 个游戏页面`;
    document.getElementById('generate-count').textContent = `0/${totalGames} 完成`;
    
    // 逐个生成页面
    let completedCount = 0;
    let successCount = 0;
    let failCount = 0;
    
    // 使用Promise序列处理
    games.reduce((promise, game, index) => {
        return promise.then(() => {
            // 更新日志
            addToGenerateLog(`[${index + 1}/${totalGames}] 正在生成: ${game.title} (ID: ${game.id})`);
            
            // 生成单个游戏页面
            return generateSingleGamePage(game)
                .then(result => {
                    completedCount++;
                    
                    // 更新进度
                    const progress = Math.round((completedCount / totalGames) * 100);
                    document.getElementById('generate-progress-bar').style.width = `${progress}%`;
                    document.getElementById('generate-progress-text').textContent = `${progress}%`;
                    document.getElementById('generate-count').textContent = `${completedCount}/${totalGames} 完成`;
                    
                    if (result.success) {
                        successCount++;
                        addToGenerateLog(`✓ 生成成功: ${game.title} - ${result.path}`);
                    } else {
                        failCount++;
                        addToGenerateLog(`✗ 生成失败: ${game.title} - ${result.error}`);
                    }
                    
                    // 检查是否完成所有生成
                    if (completedCount === totalGames) {
                        finishBulkGeneration(successCount, failCount);
                    }
                });
        });
    }, Promise.resolve());
}

/**
 * 生成单个游戏页面
 */
async function generateSingleGamePage(game) {
    try {
        // 1. 获取页面模板
        const templateResponse = await fetch('/game-template.html');
        if (!templateResponse.ok) throw new Error('获取模板失败');
        
        const template = await templateResponse.text();
        
        // 2. 生成HTML内容
        const html = generateHtml(template, game);
        
        // 3. 保存HTML到服务器
        const filename = `game-${game.id}.html`;
        const saveResponse = await fetch('/api/save-game-page.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                filename: filename,
                content: html
            })
        });
        
        if (!saveResponse.ok) {
            const error = await saveResponse.json();
            throw new Error(error.error || '保存失败');
        }
        
        const result = await saveResponse.json();
        
        // 4. 标记游戏为已生成页面
        game.pageGenerated = true;
        
        return {
            success: true,
            path: result.path
        };
    } catch (error) {
        console.error('生成页面错误:', error);
        return {
            success: false,
            error: error.message
        };
    }
}

/**
 * 根据模板和游戏数据生成HTML
 */
function generateHtml(template, gameData) {
    // 替换模板中的占位符
    let html = template;
    
    // 基本信息替换
    html = html.replace(/{{GAME_ID}}/g, gameData.id);
    html = html.replace(/{{GAME_TITLE}}/g, gameData.title);
    html = html.replace(/{{GAME_DESCRIPTION}}/g, gameData.description || '');
    html = html.replace(/{{GAME_URL}}/g, gameData.url || '');
    html = html.replace(/{{GAME_IMAGE}}/g, gameData.image || '/images/no-image.png');
    
    // 分类处理
    if (gameData.categories && Array.isArray(gameData.categories)) {
        const categoriesHtml = gameData.categories.map(category => 
            `<a href="/pages/categories/${category}.html" class="category-tag">${category}</a>`
        ).join('');
        html = html.replace(/{{GAME_CATEGORIES}}/g, categoriesHtml);
    } else {
        html = html.replace(/{{GAME_CATEGORIES}}/g, '');
    }
    
    // 标签处理
    if (gameData.tags && Array.isArray(gameData.tags)) {
        const tagsHtml = gameData.tags.map(tag => 
            `<span class="game-tag">${tag}</span>`
        ).join('');
        html = html.replace(/{{GAME_TAGS}}/g, tagsHtml);
    } else {
        html = html.replace(/{{GAME_TAGS}}/g, '');
    }
    
    // 如果有指令，则添加指令部分
    if (gameData.instructions) {
        html = html.replace(/{{GAME_INSTRUCTIONS}}/g, gameData.instructions);
    } else {
        // 移除整个指令部分
        html = html.replace(/<div class="game-instructions">[\s\S]*?<\/div>/g, '');
    }
    
    // 处理时间戳
    html = html.replace(/{{GENERATED_TIME}}/g, new Date().toISOString());
    
    return html;
}

/**
 * 完成批量生成
 */
function finishBulkGeneration(successCount, failCount) {
    // 更新状态
    document.getElementById('generate-status').textContent = '生成完成';
    document.getElementById('close-progress-btn').disabled = false;
    
    // 添加摘要到日志
    addToGenerateLog('');
    addToGenerateLog(`===== 生成摘要 =====`);
    addToGenerateLog(`总计: ${successCount + failCount} 个游戏页面`);
    addToGenerateLog(`成功: ${successCount} 个`);
    addToGenerateLog(`失败: ${failCount} 个`);
    
    // 刷新游戏列表以更新状态
    loadAdminGamesList();
}

/**
 * 向生成日志添加消息
 */
function addToGenerateLog(message) {
    const logElement = document.getElementById('generate-log');
    if (!logElement) return;
    
    const logItem = document.createElement('div');
    logItem.className = 'log-item';
    logItem.textContent = message;
    
    logElement.appendChild(logItem);
    
    // 滚动到底部
    logElement.scrollTop = logElement.scrollHeight;
}

/**
 * 关闭进度模态框
 */
function closeProgressModal() {
    const modal = document.getElementById('generate-progress-modal');
    if (modal) {
        modal.style.display = 'none';
    }
} 