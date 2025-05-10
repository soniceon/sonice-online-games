/**
 * 游戏页面生成器
 * 用于从游戏数据生成静态HTML页面
 */

/**
 * 生成单个游戏页面
 * @param {string|number} gameId 游戏ID
 */
function generateGamePage(gameId) {
    if (!gameId) return;
    
    // 获取游戏数据
    const games = window.adminGamesData || [];
    const game = games.find(g => g.id.toString() === gameId.toString());
    
    if (!game) {
        alert('未找到游戏数据');
        return;
    }
    
    // 显示进度模态框
    const modal = document.getElementById('generate-progress-modal');
    if (modal) {
        // 重置进度
        document.getElementById('generate-progress-bar').style.width = '0%';
        document.getElementById('generate-progress-text').textContent = '0%';
        document.getElementById('generate-status').textContent = `正在生成: ${game.title}`;
        document.getElementById('generate-count').textContent = '';
        document.getElementById('generate-log').innerHTML = '';
        document.getElementById('close-progress-btn').disabled = true;
        
        // 显示模态框
        modal.style.display = 'block';
    }
    
    // 添加日志
    addToGenerateLog(`开始生成: ${game.title} (ID: ${game.id})`);
    updateGenerateProgress(10);
    
    // 获取模板
    fetch('/game-template.html')
        .then(response => {
            if (!response.ok) throw new Error('获取模板失败');
            updateGenerateProgress(30);
            addToGenerateLog('模板获取成功');
            return response.text();
        })
        .then(template => {
            // 生成HTML
            updateGenerateProgress(50);
            addToGenerateLog('开始替换模板内容');
            
            const html = generateHtml(template, game);
            
            updateGenerateProgress(70);
            addToGenerateLog('内容生成完成');
            
            // 保存HTML到服务器
            const filename = `game-${game.id}.html`;
            return fetch('/api/save-game-page.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    filename: filename,
                    content: html
                })
            });
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(error => {
                    throw new Error(error.error || '保存失败');
                });
            }
            
            updateGenerateProgress(90);
            addToGenerateLog('页面保存成功');
            
            return response.json();
        })
        .then(result => {
            // 更新状态
            updateGenerateProgress(100);
            addToGenerateLog(`生成完成: ${result.path}`);
            
            // 标记游戏为已生成页面
            game.pageGenerated = true;
            
            // 启用关闭按钮
            document.getElementById('close-progress-btn').disabled = false;
            
            // 更新游戏列表
            const gamesList = document.getElementById('games-list');
            if (gamesList) {
                const gameItem = gamesList.querySelector(`[data-game-id="${game.id}"]`);
                if (gameItem) {
                    const statusBadge = gameItem.querySelector('.game-meta .badge');
                    if (statusBadge && !statusBadge.classList.contains('success')) {
                        statusBadge.classList.add('success');
                        statusBadge.textContent = '已生成页面';
                    }
                    
                    const generateBtn = gameItem.querySelector('.generate-page-btn');
                    if (generateBtn) {
                        generateBtn.textContent = '重新生成';
                    }
                }
            }
        })
        .catch(error => {
            console.error('生成页面错误:', error);
            updateGenerateProgress(100);
            addToGenerateLog(`生成失败: ${error.message}`);
            document.getElementById('close-progress-btn').disabled = false;
        });
}

/**
 * 从模板生成HTML
 * @param {string} template 模板HTML
 * @param {Object} game 游戏数据对象
 * @returns {string} 生成的HTML
 */
function generateHtml(template, game) {
    // 替换游戏数据
    let html = template;
    
    // 基本信息替换
    html = html.replace(/{{gameId}}/g, game.id)
               .replace(/{{gameTitle}}/g, game.title || '')
               .replace(/{{gameDescription}}/g, game.description || '')
               .replace(/{{gameImage}}/g, game.image || '/images/placeholder.jpg')
               .replace(/{{gameUrl}}/g, game.url || '#')
               .replace(/{{gameProvider}}/g, game.provider || '');
    
    // 替换元标签
    html = html.replace(/{{metaTitle}}/g, `${game.title} - 在线游戏`)
               .replace(/{{metaDescription}}/g, game.description || `畅玩${game.title}在线游戏`)
               .replace(/{{metaKeywords}}/g, `${game.title}, 在线游戏, ${game.categories?.join(', ') || ''}`);
    
    // 替换分类标签
    if (game.categories && game.categories.length > 0) {
        const categoriesHtml = game.categories.map(cat => 
            `<a href="/pages/categories/${cat.toLowerCase().replace(/\s+/g, '-')}.html" class="category-tag">${cat}</a>`
        ).join('');
        html = html.replace('{{gameCategories}}', categoriesHtml);
    } else {
        html = html.replace('{{gameCategories}}', '');
    }
    
    // 替换评分
    const rating = game.rating || Math.floor(Math.random() * 3) + 3; // 3-5之间的随机评分
    const ratingStars = generateRatingStars(rating);
    html = html.replace('{{gameRating}}', rating.toString())
               .replace('{{gameRatingStars}}', ratingStars);
    
    // 使用高级iframe代理解决方案替换游戏框架
    if (game.type === 'iframe' || game.url) {
        // 创建一个占位符div替代iframe标签,使用data-url属性存储游戏URL
        html = html.replace(/<iframe.*?{{gameFrame}}.*?><\/iframe>/g, 
            `<div id="game-frame" class="game-iframe game-frame-container" data-url="${game.url}"></div>`);
        html = html.replace('{{gameFrame}}', 
            `<div id="game-frame" class="game-iframe game-frame-container" data-url="${game.url}"></div>`);
        
        // 添加代理加载器脚本引用
        const scriptReference = '<script src="/js/game-proxy-loader.js"></script>';
        if (!html.includes('game-proxy-loader.js')) {
            html = html.replace('</head>', scriptReference + '</head>');
        }

        // 自定义初始化脚本
        const customInitScript = `
<script>
// 初始化游戏加载代理
document.addEventListener('DOMContentLoaded', function() {
    const gameProxyLoader = new GameProxyLoader({
        frameId: 'game-frame',
        fullscreenBtnId: 'fullscreen-btn',
        debug: false
    });
    
    gameProxyLoader.init();
    gameProxyLoader.loadGame("${game.url}").catch(error => {
        console.error("游戏加载失败:", error);
    });
});
</script>`;
        
        // 注入自定义脚本
        html = html.replace('</body>', customInitScript + '</body>');
    } else {
        // 非iframe游戏，使用外部链接
        html = html.replace('{{gameFrame}}', `<div class="game-placeholder">
            <a href="${game.url}" target="_blank" class="play-external-btn">在新窗口中打开游戏</a>
        </div>`);
    }
    
    // 替换时间戳
    const timestamp = new Date().toISOString();
    html = html.replace('{{generatedTime}}', timestamp);
    
    return html;
}

/**
 * 生成星级评分HTML
 * @param {number} rating 1-5之间的评分
 * @returns {string} 星级HTML
 */
function generateRatingStars(rating) {
    let stars = '';
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    
    // 添加全星
    for (let i = 0; i < fullStars; i++) {
        stars += '<i class="fas fa-star"></i>';
    }
    
    // 添加半星
    if (hasHalfStar) {
        stars += '<i class="fas fa-star-half-alt"></i>';
    }
    
    // 添加空星
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    for (let i = 0; i < emptyStars; i++) {
        stars += '<i class="far fa-star"></i>';
    }
    
    return stars;
}

/**
 * 批量生成游戏页面
 * @param {Array} gameIds 游戏ID数组，如果不提供则生成所有游戏
 */
function bulkGeneratePages(gameIds) {
    // 获取游戏数据
    const allGames = window.adminGamesData || [];
    
    // 确定要生成的游戏
    let gamesToGenerate = [];
    if (gameIds && gameIds.length > 0) {
        gamesToGenerate = allGames.filter(g => gameIds.includes(g.id.toString()));
    } else {
        gamesToGenerate = [...allGames];
    }
    
    if (gamesToGenerate.length === 0) {
        alert('没有找到需要生成页面的游戏');
        return;
    }
    
    // 显示进度模态框
    const modal = document.getElementById('generate-progress-modal');
    if (modal) {
        // 重置进度
        document.getElementById('generate-progress-bar').style.width = '0%';
        document.getElementById('generate-progress-text').textContent = '0%';
        document.getElementById('generate-status').textContent = `批量生成游戏页面`;
        document.getElementById('generate-count').textContent = `0/${gamesToGenerate.length}`;
        document.getElementById('generate-log').innerHTML = '';
        document.getElementById('close-progress-btn').disabled = true;
        
        // 显示模态框
        modal.style.display = 'block';
    }
    
    // 添加日志
    addToGenerateLog(`开始批量生成 ${gamesToGenerate.length} 个游戏页面`);
    
    // 获取模板
    fetch('/game-template.html')
        .then(response => {
            if (!response.ok) throw new Error('获取模板失败');
            addToGenerateLog('模板获取成功');
            return response.text();
        })
        .then(template => {
            // 逐个生成页面
            let processed = 0;
            let successful = 0;
            
            // 使用Promise序列处理
            return gamesToGenerate.reduce((promise, game) => {
                return promise.then(() => {
                    // 更新状态
                    processed++;
                    const percent = Math.floor((processed / gamesToGenerate.length) * 100);
                    updateGenerateProgress(percent);
                    document.getElementById('generate-count').textContent = `${processed}/${gamesToGenerate.length}`;
                    
                    addToGenerateLog(`处理: ${game.title} (ID: ${game.id})`);
                    
                    // 生成HTML
                    const html = generateHtml(template, game);
                    const filename = `game-${game.id}.html`;
                    
                    // 保存HTML到服务器
                    return fetch('/api/save-game-page.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            filename: filename,
                            content: html
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(error => {
                                throw new Error(error.error || '保存失败');
                            });
                        }
                        return response.json();
                    })
                    .then(result => {
                        successful++;
                        addToGenerateLog(`完成: ${game.title} -> ${result.path}`);
                        
                        // 标记游戏为已生成页面
                        game.pageGenerated = true;
                        
                        // 更新游戏列表中的状态
                        updateGameItemStatus(game.id);
                    })
                    .catch(error => {
                        addToGenerateLog(`失败: ${game.title} - ${error.message}`, true);
                    });
                });
            }, Promise.resolve())
            .then(() => {
                // 所有游戏处理完成
                addToGenerateLog(`批量生成完成，成功: ${successful}/${gamesToGenerate.length}`);
                document.getElementById('close-progress-btn').disabled = false;
            });
        })
        .catch(error => {
            console.error('批量生成错误:', error);
            addToGenerateLog(`批量生成失败: ${error.message}`, true);
            document.getElementById('close-progress-btn').disabled = false;
        });
}

/**
 * 更新游戏项状态
 * @param {string|number} gameId 游戏ID
 */
function updateGameItemStatus(gameId) {
    const gamesList = document.getElementById('games-list');
    if (!gamesList) return;
    
    const gameItem = gamesList.querySelector(`[data-game-id="${gameId}"]`);
    if (!gameItem) return;
    
    const statusBadge = gameItem.querySelector('.game-meta .badge');
    if (statusBadge && !statusBadge.classList.contains('success')) {
        statusBadge.classList.add('success');
        statusBadge.textContent = '已生成页面';
    }
    
    const generateBtn = gameItem.querySelector('.generate-page-btn');
    if (generateBtn) {
        generateBtn.textContent = '重新生成';
    }
}

/**
 * 添加生成日志条目
 * @param {string} message 日志消息
 * @param {boolean} isError 是否为错误消息
 */
function addToGenerateLog(message, isError = false) {
    const logContainer = document.getElementById('generate-log');
    if (!logContainer) return;
    
    const logEntry = document.createElement('div');
    logEntry.className = `log-entry ${isError ? 'log-error' : ''}`;
    
    const timestamp = new Date().toLocaleTimeString();
    logEntry.innerHTML = `<span class="log-time">[${timestamp}]</span> ${message}`;
    
    logContainer.appendChild(logEntry);
    logContainer.scrollTop = logContainer.scrollHeight;
}

/**
 * 更新生成进度
 * @param {number} percent 进度百分比
 */
function updateGenerateProgress(percent) {
    const progressBar = document.getElementById('generate-progress-bar');
    const progressText = document.getElementById('generate-progress-text');
    
    if (progressBar) {
        progressBar.style.width = `${percent}%`;
    }
    
    if (progressText) {
        progressText.textContent = `${percent}%`;
    }
}

/**
 * 初始化生成功能
 */
function initGenerateFeature() {
    // 添加到DOMContentLoaded事件
    document.addEventListener('DOMContentLoaded', function() {
        // 只在管理页面检查和初始化
        if (!document.querySelector('.admin-section')) return;
        
        // 检查API可用性
        checkServerAPI();
        
        // 初始化生成按钮事件监听
        initGenerateButtons();
        
        // 创建进度模态框（如果不存在）
        createProgressModal();
    });
}

/**
 * 初始化生成按钮
 */
function initGenerateButtons() {
    // 单个游戏生成按钮
    document.querySelectorAll('.generate-page-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const gameId = this.closest('[data-game-id]').dataset.gameId;
            generateGamePage(gameId);
        });
    });
    
    // 批量生成按钮
    const bulkGenerateBtn = document.getElementById('bulk-generate-btn');
    if (bulkGenerateBtn) {
        bulkGenerateBtn.addEventListener('click', function(e) {
            e.preventDefault();
            bulkGeneratePages();
        });
    }
    
    // 模态框关闭按钮
    document.querySelectorAll('.close-modal-btn, #close-progress-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('generate-progress-modal').style.display = 'none';
        });
    });
}

/**
 * 创建进度模态框
 */
function createProgressModal() {
    // 如果已经存在，则不创建
    if (document.getElementById('generate-progress-modal')) return;
    
    const modal = document.createElement('div');
    modal.id = 'generate-progress-modal';
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>生成页面进度</h3>
                <button class="close-modal-btn">&times;</button>
            </div>
            <div class="modal-body">
                <div class="progress-status">
                    <div id="generate-status">准备中...</div>
                    <div id="generate-count"></div>
                </div>
                <div class="progress-container">
                    <div class="progress-bar-wrapper">
                        <div id="generate-progress-bar" class="progress-bar"></div>
                    </div>
                    <div id="generate-progress-text">0%</div>
                </div>
                <div class="log-container">
                    <div id="generate-log" class="log"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="close-progress-btn" disabled>关闭</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

/**
 * 检测服务器API
 * 检查是否支持游戏页面生成功能
 */
function checkServerAPI() {
    // Check API availability
    fetch('/api/check-api.php')
        .then(response => response.json())
        .then(data => {
            if (!data || !data.status) {
                // API is not available, show warning
                showAPIWarning();
            }
        })
        .catch(error => {
            showAPIWarning();
        });
    
    setTimeout(() => {
        if (!serverAPIChecked) {
            console.error('API check failed:', error);
            showAPIWarning();
        }
    }, 3000);
}

/**
 * Show API unavailable warning
 */
function showAPIWarning() {
    serverAPIChecked = true;
    const warningBox = document.createElement('div');
    warningBox.className = 'api-warning';
    warningBox.innerHTML = `
    <div class="api-warning-inner">
        <button class="close-btn">×</button>
        <h3>Server API Unavailable</h3>
        <p>The game page generation feature requires server API support. Please ensure your server is configured correctly and the PHP API is available.</p>
        <ul>
            <li>Check if the <code>/api/</code> directory exists</li>
            <li>Ensure PHP is running on your server</li>
            <li>Check server error logs for more information</li>
        </ul>
    </div>
    `;
    
    document.body.appendChild(warningBox);
    
    warningBox.querySelector('.close-btn').addEventListener('click', () => {
        warningBox.remove();
    });
    
    // Disable save button
    const button = document.querySelector('#generate-game-page');
    if (button) {
        button.disabled = true;
        button.title = 'API unavailable, cannot generate page';
    }
}

// 初始化生成功能
initGenerateFeature(); 