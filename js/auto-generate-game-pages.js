/**
 * Auto Game Page Generator - Client-side script
 * 自动生成所有游戏的HTML页面
 */

document.addEventListener('DOMContentLoaded', function() {
    // 元素引用
    const generateAllButton = document.getElementById('generate-all-button');
    const statusDisplay = document.getElementById('generation-status');
    const progressBar = document.getElementById('progress-bar');
    const progressContainer = document.getElementById('progress-container');
    const resultsList = document.getElementById('generation-results');
    
    // 检查页面上是否存在生成按钮
    if (generateAllButton) {
        generateAllButton.addEventListener('click', function() {
            // 开始生成前的初始化
            statusDisplay.textContent = "正在准备生成所有游戏页面...";
            progressContainer.style.display = "block";
            progressBar.style.width = "0%";
            resultsList.innerHTML = "";
            
            // 启动生成过程
            generateAllGamePages();
        });
    }
    
    /**
     * 生成所有游戏页面的主函数
     */
    async function generateAllGamePages() {
        try {
            // 获取游戏数据
            statusDisplay.textContent = "正在加载游戏数据...";
            const gamesData = await fetchGamesData();
            
            if (!gamesData || !Array.isArray(gamesData)) {
                throw new Error("无法获取游戏数据或数据格式不正确");
            }
            
            // 获取游戏模板
            statusDisplay.textContent = "正在加载页面模板...";
            const template = await fetchTemplate();
            
            // 准备生成
            const totalGames = gamesData.length;
            statusDisplay.textContent = `准备生成 ${totalGames} 个游戏页面...`;
            
            // 生成游戏页面
            let successCount = 0;
            let failCount = 0;
            
            for (let i = 0; i < gamesData.length; i++) {
                const game = gamesData[i];
                // 更新进度
                const progress = Math.round((i / totalGames) * 100);
                progressBar.style.width = `${progress}%`;
                statusDisplay.textContent = `正在生成游戏页面 (${i+1}/${totalGames}): ${game.title}`;
                
                try {
                    // 生成游戏页面
                    await generateGamePage(game, template);
                    // 更新结果列表
                    addResult(game.title, true);
                    successCount++;
                } catch (error) {
                    console.error(`生成游戏页面失败 [${game.title}]: ${error.message}`);
                    addResult(game.title, false, error.message);
                    failCount++;
                }
            }
            
            // 完成
            progressBar.style.width = "100%";
            statusDisplay.textContent = `生成完成! 成功: ${successCount}, 失败: ${failCount}`;
        } catch (error) {
            console.error('自动生成游戏页面时出错:', error);
            statusDisplay.textContent = `生成过程中发生错误: ${error.message}`;
        }
    }
    
    /**
     * 获取游戏数据
     */
    async function fetchGamesData() {
        const response = await fetch('/data/games.json');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    }
    
    /**
     * 获取游戏页面模板
     */
    async function fetchTemplate() {
        const response = await fetch('/templates/game-template.html');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.text();
    }
    
    /**
     * 生成单个游戏页面
     */
    async function generateGamePage(game, template) {
        // 验证游戏数据
        if (!game.id || !game.title) {
            throw new Error('游戏数据缺少必要字段 (id, title)');
        }
        
        try {
            // 创建游戏页面的内容
            let content = template;
            
            // 替换基本内容
            content = content.replace(/{{game\.title}}/g, game.title);
            content = content.replace(/{{game\.id}}/g, game.id);
            content = content.replace(/{{game\.description}}/g, game.description || '');
            content = content.replace(/{{game\.thumbnailUrl}}/g, game.thumbnailUrl || '');
            content = content.replace(/{{game\.instructions}}/g, game.instructions || '');
            
            // 处理分类
            if (game.categories && Array.isArray(game.categories)) {
                content = content.replace(/{{game\.categories}}/g, game.categories.join(', '));
            } else {
                content = content.replace(/{{game\.categories}}/g, '');
            }
            
            // 处理iframe URL
            const originalIframeUrl = game.iframeUrl || '';
            let iframeUrl = originalIframeUrl;
            
            // 使用代理模式
            if (originalIframeUrl) {
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
            
            // 替换iframe URL
            content = content.replace(/{{game\.iframeUrl}}/g, iframeUrl);
            
            // 创建游戏页面的文件
            const filename = `${game.id}.html`;
            
            // 保存游戏页面
            await saveGamePage(filename, content);
            
            return { success: true, filename: filename };
        } catch (error) {
            throw new Error(`处理游戏页面时出错 [${game.title}]: ${error.message}`);
        }
    }
    
    /**
     * 保存游戏页面到服务器
     */
    async function saveGamePage(filename, content) {
        const response = await fetch('/api/save-game-page', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                filename: filename,
                content: content
            })
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `保存失败，HTTP状态: ${response.status}`);
        }
        
        return await response.json();
    }
    
    /**
     * 添加生成结果到结果列表
     */
    function addResult(title, success, errorMessage = '') {
        const resultItem = document.createElement('li');
        resultItem.className = success ? 'success' : 'error';
        
        const icon = document.createElement('span');
        icon.className = 'result-icon';
        icon.textContent = success ? '✓' : '✗';
        
        const text = document.createElement('span');
        text.className = 'result-text';
        text.textContent = `${title} ${success ? '生成成功' : '生成失败: ' + errorMessage}`;
        
        resultItem.appendChild(icon);
        resultItem.appendChild(text);
        resultsList.appendChild(resultItem);
    }
}); 