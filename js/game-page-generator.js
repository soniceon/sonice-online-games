/**
 * Game Page Generator - Client-side script
 * Generates individual HTML pages for each game in the games.json file
 */

document.addEventListener('DOMContentLoaded', function() {
    const generateButton = document.getElementById('generate-button');
    const generatorResult = document.getElementById('generator-result');
    
    if (generateButton) {
        generateButton.addEventListener('click', startGeneration);
    }
    
    /**
     * Starts the game page generation process
     */
    async function startGeneration() {
        try {
            updateLog('Starting game page generation process...', 'info');
            
            // Get options from the form
            const overwriteExisting = document.getElementById('overwrite-existing')?.checked || false;
            const includeCategories = document.getElementById('include-categories')?.checked || false;
            const generateSEO = document.getElementById('generate-seo')?.checked || false;
            const generateZip = document.getElementById('generate-zip')?.checked || false;
            const includeAssets = document.getElementById('include-assets')?.checked || false;
            const useProxy = document.getElementById('use-proxy')?.checked || false;
            
            // Gather and validate options
            const options = {
                overwriteExisting,
                includeCategories,
                generateSEO,
                generateZip,
                includeAssets,
                useProxy
            };
            
            // Start generation with loading indicator
            generateButton.disabled = true;
            generateButton.innerHTML = '<span class="spinner"></span> Generating...';
            
            // Step 1: Load game data
            updateLog('Loading game data...', 'info');
            const gamesData = await loadGameData();
            
            if (!gamesData || !gamesData.games || gamesData.games.length === 0) {
                throw new Error('No games found in the database.');
            }
            
            const games = gamesData.games;
            
            updateLog(`Found ${games.length} games in the database.`, 'success');
            
            // Step 2: Load template
            updateLog('Loading game page template...', 'info');
            const template = await loadTemplate();
            
            if (!template) {
                throw new Error('Failed to load game page template.');
            }
            
            updateLog('Template loaded successfully.', 'success');
            
            // Step 3: Generate pages for each game
            updateLog('Generating individual game pages...', 'info');
            
            const generatedPages = [];
            let successCount = 0;
            let errorCount = 0;
            
            for (let i = 0; i < games.length; i++) {
                const game = games[i];
                try {
                    const progress = Math.round(((i + 1) / games.length) * 100);
                    updateLog(`Generating page for "${game.title}" (${i + 1}/${games.length}, ${progress}%)...`, 'info');
                    
                    // Skip games with no slug if needed
                    if (!game.slug) {
                        updateLog(`Skipping "${game.title}" - No slug defined`, 'error');
                        errorCount++;
                        continue;
                    }
                    
                    // Check if game has an iframe URL
                    if (!game.iframeUrl) {
                        updateLog(`Warning for "${game.title}" - No iframe URL defined, using placeholder`, 'error');
                    }
                    
                    // Generate the page content
                    const pageContent = populateTemplate(template, game, options);
                    
                    // Create downloadable file
                    const fileName = `${game.slug}.html`;
                    await downloadFile(fileName, pageContent);
                    
                    generatedPages.push(fileName);
                    successCount++;
                    
                    updateLog(`Successfully generated: ${fileName}`, 'success');
                } catch (error) {
                    errorCount++;
                    updateLog(`Error generating page for "${game.title}": ${error.message}`, 'error');
                }
            }
            
            // Step 4: Create ZIP file if requested
            if (generateZip && generatedPages.length > 0) {
                updateLog('Note: ZIP file creation requires server-side processing.', 'info');
                updateLog('Download each file individually.', 'info');
            }
            
            // Show completion message
            updateLog(`Generation complete. ${successCount} pages generated, ${errorCount} errors.`, 'success');
            
            // Reset button
            generateButton.disabled = false;
            generateButton.innerHTML = 'Generate Game Pages';
            
        } catch (error) {
            updateLog(`Error: ${error.message}`, 'error');
            generateButton.disabled = false;
            generateButton.innerHTML = 'Generate Game Pages';
        }
    }
    
    /**
     * Loads game data from the database
     */
    async function loadGameData() {
        try {
            const response = await fetch('../data/games.json');
            if (!response.ok) {
                throw new Error(`Failed to load games data: ${response.statusText}`);
            }
            return await response.json();
        } catch (error) {
            throw new Error(`Error loading game data: ${error.message}`);
        }
    }
    
    /**
     * Loads the game page template
     */
    async function loadTemplate() {
        try {
            const response = await fetch('../game-template.html');
            if (!response.ok) {
                throw new Error(`Failed to load template: ${response.statusText}`);
            }
            return await response.text();
        } catch (error) {
            throw new Error(`Error loading template: ${error.message}`);
        }
    }
    
    /**
     * Creates a downloadable file
     */
    async function downloadFile(fileName, content) {
        // Create a downloadable blob
        const blob = new Blob([content], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        
        // Create a download link
        const downloadLink = document.createElement('a');
        downloadLink.href = url;
        downloadLink.download = fileName;
        
        // Trigger the download
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
        
        // Revoke the URL to free memory
        setTimeout(() => URL.revokeObjectURL(url), 100);
        
        return fileName;
    }
    
    /**
     * Populate template with game data
     */
    function populateTemplate(template, game, options) {
        let content = template;
        
        // Basic replacements
        content = content.replace(/{{game\.title}}/g, game.title || '');
        content = content.replace(/{{game\.description}}/g, game.description || '');
        content = content.replace(/{{game\.slug}}/g, game.slug || '');
        
        // Handle the iframe URL
        const originalIframeUrl = game.iframeUrl || '';
        let iframeUrl = originalIframeUrl;
        
        // If using proxy mode, wrap the URL in a proxy
        if (options.useProxy && originalIframeUrl) {
            // 使用更高级的代理方法 - 防止CORS限制
            const proxyTemplate = `
<script>
// 高级游戏加载代理解决方案 - 保持原始URL不变
document.addEventListener('DOMContentLoaded', function() {
    const gameFrame = document.getElementById('game-frame');
    const gameUrl = "${originalIframeUrl}"; // 保持URL完全一致
    
    try {
        // 创建一个新的iframe元素
        const iframe = document.createElement('iframe');
        iframe.style.width = '100%';
        iframe.style.height = '100%';
        iframe.style.border = 'none';
        iframe.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture";
        iframe.allowFullscreen = true;
        
        // 直接设置iframe源地址
        iframe.src = gameUrl;
        
        // 替换原有的iframe占位符
        if (gameFrame) {
            if (gameFrame.parentNode) {
                gameFrame.parentNode.replaceChild(iframe, gameFrame);
            } else {
                // 如果找不到父节点，则使用替代方法
                const container = document.querySelector('.game-frame-container');
                if (container) {
                    container.innerHTML = '';
                    container.appendChild(iframe);
                }
            }
        }
        
        // 添加全屏功能
        const fullscreenBtn = document.getElementById('fullscreen-btn');
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', function() {
                if (iframe.requestFullscreen) {
                    iframe.requestFullscreen();
                } else if (iframe.mozRequestFullScreen) {
                    iframe.mozRequestFullScreen();
                } else if (iframe.webkitRequestFullscreen) {
                    iframe.webkitRequestFullscreen();
                } else if (iframe.msRequestFullscreen) {
                    iframe.msRequestFullscreen();
                }
            });
        }
        
        // 监听iframe加载错误
        iframe.onerror = function() {
            console.error('iframe failed to load: ' + gameUrl);
            // 显示错误信息
            const errorMsg = document.createElement('div');
            errorMsg.className = 'game-error-message';
            errorMsg.innerHTML = '<p>游戏加载失败。请稍后再试。</p>';
            if (iframe.parentNode) {
                iframe.parentNode.appendChild(errorMsg);
            }
        };
    } catch (e) {
        console.error('Error creating iframe: ' + e.message);
        // 显示错误信息在页面上
        if (gameFrame) {
            gameFrame.innerHTML = '<div class="game-error-message"><p>游戏加载失败。请稍后再试。</p></div>';
        }
    }
});
</script>`;
            
            // 替换iframe标签为可被替换的占位符
            content = content.replace(/<iframe src="{{game\.iframeUrl}}" class="game-frame" id="game-frame" allowfullscreen><\/iframe>/g, 
                                     '<div id="game-frame" class="game-frame game-frame-container"></div>');
            // 同时支持第二种格式
            content = content.replace(/<iframe id="game-frame" class="game-frame" src="{{game\.iframeUrl}}" allowfullscreen><\/iframe>/g, 
                                     '<div id="game-frame" class="game-frame game-frame-container"></div>');
            // 第三种可能的格式
            content = content.replace(/<iframe.*?id="game-frame".*?src="{{game\.iframeUrl}}".*?><\/iframe>/g, 
                                     '<div id="game-frame" class="game-frame game-frame-container"></div>');
            
            // 注入代理脚本
            content = content.replace('</body>', proxyTemplate + '</body>');
            
            // 保持原始iframe URL不变
            iframeUrl = originalIframeUrl;
        }
        
        // 替换其他可能的iframeUrl引用
        content = content.replace(/{{game\.iframeUrl}}/g, iframeUrl);
        content = content.replace(/{{game\.imageUrl}}/g, game.imageUrl || '/images/placeholder.jpg');
        
        // Handle categories
        if (options.includeCategories) {
            // Single category handling
            if (game.category) {
                content = content.replace(/{{game\.category}}/g, game.category);
                content = content.replace(/{{categoryClass}}/g, `category-${game.category.toLowerCase().replace(/\s+/g, '-')}`);
            } 
            // Multiple category array handling
            else if (game.categories && game.categories.length > 0) {
                content = content.replace(/{{game\.category}}/g, game.categories[0]);
                
                // Category class names
                const categoryClasses = game.categories
                    .map(cat => `category-${cat.toLowerCase().replace(/\s+/g, '-')}`)
                    .join(' ');
                content = content.replace(/{{categoryClass}}/g, categoryClasses);
                
                // Category list
                const categoriesList = game.categories
                    .map(cat => `<span class="category-tag">${cat}</span>`)
                    .join('');
                content = content.replace(/{{game\.categoriesList}}/g, categoriesList);
            } else {
                content = content.replace(/{{game\.category}}/g, '');
                content = content.replace(/{{categoryClass}}/g, '');
                content = content.replace(/{{game\.categoriesList}}/g, '');
            }
        } else {
            content = content.replace(/{{game\.category}}/g, '');
            content = content.replace(/{{categoryClass}}/g, '');
            content = content.replace(/{{game\.categoriesList}}/g, '');
        }
        
        // Generate SEO metadata if needed
        if (options.generateSEO) {
            let gameCategories = '';
            if (game.categories && game.categories.length > 0) {
                gameCategories = game.categories.join(', ');
            } else if (game.category) {
                gameCategories = game.category;
            }
            
            const metaDescription = game.description || `Play ${game.title} online for free at sonice.online. Enjoy this fun ${gameCategories} game!`;
            content = content.replace(/{{metaDescription}}/g, metaDescription);
            
            const metaKeywords = [
                game.title,
                'online game',
                'free game',
                'browser game',
                ...(game.categories || [game.category]).filter(Boolean)
            ].join(', ');
            content = content.replace(/{{metaKeywords}}/g, metaKeywords);
        } else {
            content = content.replace(/{{metaDescription}}/g, '');
            content = content.replace(/{{metaKeywords}}/g, '');
        }
        
        return content;
    }
    
    /**
     * Updates the log display with a new message
     */
    function updateLog(message, type = 'info') {
        if (!generatorResult) return;
        
        const timestamp = new Date().toLocaleTimeString();
        let statusIndicator = '';
        
        switch (type) {
            case 'success':
                statusIndicator = '<span class="status-indicator status-success"></span>';
                break;
            case 'error':
                statusIndicator = '<span class="status-indicator status-error"></span>';
                break;
            default:
                statusIndicator = '<span class="status-indicator"></span>';
        }
        
        const logEntry = document.createElement('div');
        logEntry.innerHTML = `${statusIndicator}[${timestamp}] ${message}`;
        logEntry.className = `log-entry log-${type}`;
        
        generatorResult.appendChild(logEntry);
        generatorResult.scrollTop = generatorResult.scrollHeight;
    }
});

// Backward compatibility with old code
function runGenerator() {
    const generateButton = document.getElementById('generate-button');
    if (generateButton) {
        generateButton.click();
    }
}

window.runGenerator = runGenerator;

/**
 * 游戏页面生成器
 * 通过游戏ID动态生成单独的游戏页面
 */

class GamePageGenerator {
    constructor() {
        this.gameData = null;
        this.templateHtml = null;
        this.apiEndpoint = '/api/save-game-page.php';
        this.deleteEndpoint = '/api/delete-game-page.php';
        this.listEndpoint = '/api/list-game-pages.php';
        this.gamesJsonPath = '/data/games.json';
        this.templatePath = '/game-template.html';
    }

    /**
     * 初始化生成器
     */
    async init() {
        try {
            // 加载游戏数据
            await this.loadGameData();
            
            // 加载页面模板
            await this.loadTemplate();
            
            console.log('GamePageGenerator initialized successfully');
            return true;
        } catch (error) {
            console.error('Failed to initialize GamePageGenerator:', error);
            return false;
        }
    }

    /**
     * 加载游戏数据
     */
    async loadGameData() {
        try {
            const response = await fetch(this.gamesJsonPath);
            if (!response.ok) {
                throw new Error(`Failed to load games data: ${response.status} ${response.statusText}`);
            }
            this.gameData = await response.json();
            console.log(`Loaded ${this.gameData.length} games from JSON`);
        } catch (error) {
            console.error('Error loading game data:', error);
            throw error;
        }
    }

    /**
     * 加载HTML模板
     */
    async loadTemplate() {
        try {
            const response = await fetch(this.templatePath);
            if (!response.ok) {
                throw new Error(`Failed to load template: ${response.status} ${response.statusText}`);
            }
            this.templateHtml = await response.text();
            console.log('Template loaded successfully');
        } catch (error) {
            console.error('Error loading template:', error);
            throw error;
        }
    }

    /**
     * 根据游戏ID获取游戏数据
     * @param {string} gameId 游戏ID
     * @returns {Object|null} 游戏数据对象或null
     */
    getGameById(gameId) {
        if (!this.gameData) {
            console.error('Game data not loaded');
            return null;
        }

        const game = this.gameData.find(g => g.id === gameId);
        if (!game) {
            console.error(`Game with ID ${gameId} not found`);
            return null;
        }

        return game;
    }

    /**
     * 为游戏生成页面HTML
     * @param {string} gameId 游戏ID
     * @returns {string|null} 生成的HTML或null（如果失败）
     */
    generateGamePageHtml(gameId) {
        // 获取游戏数据
        const game = this.getGameById(gameId);
        if (!game) return null;

        // 确保模板已加载
        if (!this.templateHtml) {
            console.error('Template not loaded');
            return null;
        }

        let html = this.templateHtml;

        // 替换模板中的变量
        html = html.replace(/{{game\.id}}/g, game.id || '');
        html = html.replace(/{{game\.title}}/g, game.title || '');
        html = html.replace(/{{game\.description}}/g, game.description || '');
        
        // 处理游戏图片
        const imageUrl = game.image || `/assets/images/games/${game.id}.jpg`;
        html = html.replace(/{{game\.image}}/g, imageUrl);
        
        // 处理游戏网址
        html = html.replace(/{{game\.url}}/g, game.url || '');
        
        // 处理游戏分类
        const categories = Array.isArray(game.categories) ? game.categories.join(', ') : game.categories || '';
        html = html.replace(/{{game\.categories}}/g, categories);

        // 处理元数据
        html = html.replace(/{{meta\.title}}/g, `${game.title} - Sonice Games`);
        html = html.replace(/{{meta\.description}}/g, game.description || `Play ${game.title} online for free on Sonice Games`);
        html = html.replace(/{{meta\.keywords}}/g, `${game.title}, online games, free games, ${categories}`);

        // 处理生成的时间戳
        const timestamp = new Date().toISOString();
        html = html.replace(/{{generated\.timestamp}}/g, timestamp);

        return html;
    }

    /**
     * 保存游戏页面
     * @param {string} gameId 游戏ID
     * @returns {Promise<Object>} 保存结果
     */
    async saveGamePage(gameId) {
        try {
            // 生成页面HTML
            const html = this.generateGamePageHtml(gameId);
            if (!html) {
                throw new Error(`Failed to generate HTML for game ${gameId}`);
            }

            // 构建文件名
            const filename = `game-${gameId}.html`;

            // 发送到API保存
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    filename: filename,
                    content: html
                })
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`API error (${response.status}): ${errorText}`);
            }

            const result = await response.json();
            console.log(`Game page saved: ${result.filename}`);
            return result;
        } catch (error) {
            console.error('Error saving game page:', error);
            throw error;
        }
    }

    /**
     * 删除游戏页面
     * @param {string} gameId 游戏ID
     * @returns {Promise<Object>} 删除结果
     */
    async deleteGamePage(gameId) {
        try {
            // 构建文件名
            const filename = `game-${gameId}.html`;

            // 发送删除请求
            const response = await fetch(this.deleteEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    filename: filename,
                    _method: 'DELETE'
                })
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`API error (${response.status}): ${errorText}`);
            }

            const result = await response.json();
            console.log(`Game page deleted: ${result.filename}`);
            return result;
        } catch (error) {
            console.error('Error deleting game page:', error);
            throw error;
        }
    }

    /**
     * 获取已生成的游戏页面列表
     * @returns {Promise<Array>} 游戏页面列表
     */
    async getGeneratedPages() {
        try {
            const response = await fetch(this.listEndpoint);
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`API error (${response.status}): ${errorText}`);
            }

            const result = await response.json();
            return result.files || [];
        } catch (error) {
            console.error('Error fetching generated pages:', error);
            throw error;
        }
    }

    /**
     * 为所有游戏生成页面
     * @param {Function} progressCallback 进度回调函数
     * @returns {Promise<Object>} 生成结果
     */
    async generateAllPages(progressCallback = null) {
        if (!this.gameData || this.gameData.length === 0) {
            throw new Error('No game data available');
        }

        const results = {
            total: this.gameData.length,
            successful: 0,
            failed: 0,
            errors: []
        };

        for (let i = 0; i < this.gameData.length; i++) {
            const game = this.gameData[i];
            
            // 更新进度
            if (progressCallback) {
                progressCallback({
                    current: i + 1,
                    total: this.gameData.length,
                    percent: Math.round(((i + 1) / this.gameData.length) * 100),
                    currentGame: game.title
                });
            }

            try {
                await this.saveGamePage(game.id);
                results.successful++;
            } catch (error) {
                results.failed++;
                results.errors.push({
                    gameId: game.id,
                    title: game.title,
                    error: error.message
                });
            }
        }

        return results;
    }
}

// 导出生成器类
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { GamePageGenerator };
} else {
    window.GamePageGenerator = GamePageGenerator;
} 