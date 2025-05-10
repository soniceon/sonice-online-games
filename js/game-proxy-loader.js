/**
 * 游戏代理加载器
 * 用于解决跨域问题、管理游戏iframe的加载和全屏切换
 */
class GameProxyLoader {
    /**
     * 构造函数
     * @param {Object} options 配置选项
     * @param {string} options.frameId 游戏iframe的ID
     * @param {string} options.fullscreenBtnId 全屏按钮的ID
     * @param {boolean} options.debug 是否启用调试模式
     */
    constructor(options = {}) {
        this.frameId = options.frameId || 'game-frame';
        this.fullscreenBtnId = options.fullscreenBtnId || 'fullscreen-btn';
        this.debug = options.debug || false;
        this.frameElement = null;
        this.fullscreenBtn = null;
        this.gameUrl = '';
        this.isFullscreen = false;
        this.proxyUrl = '/game-proxy.html'; // 代理页面路径
        
        // 绑定方法到实例
        this.loadGame = this.loadGame.bind(this);
        this.toggleFullscreen = this.toggleFullscreen.bind(this);
        this._log = this._log.bind(this);
        this._handleMessage = this._handleMessage.bind(this);
    }
    
    /**
     * 初始化加载器
     */
    init() {
        this._log('初始化游戏加载器');
        
        // 获取框架元素
        this.frameElement = document.getElementById(this.frameId);
        if (!this.frameElement) {
            this._log('错误: 找不到游戏框架元素 #' + this.frameId, 'error');
            return;
        }
        
        // 获取全屏按钮
        this.fullscreenBtn = document.getElementById(this.fullscreenBtnId);
        if (this.fullscreenBtn) {
            this.fullscreenBtn.addEventListener('click', this.toggleFullscreen);
        } else {
            this._log('警告: 找不到全屏按钮 #' + this.fullscreenBtnId, 'warn');
        }
        
        // 添加消息监听
        window.addEventListener('message', this._handleMessage);
    }
    
    /**
     * 加载游戏
     * @param {string} gameUrl 游戏URL
     * @returns {Promise} 加载完成的Promise
     */
    loadGame(gameUrl) {
        return new Promise((resolve, reject) => {
            if (!this.frameElement) {
                reject(new Error('游戏框架元素未初始化'));
                return;
            }
            
            this.gameUrl = gameUrl;
            this._log(`加载游戏: ${gameUrl}`);
            
            // 创建iframe
            const iframe = document.createElement('iframe');
            iframe.classList.add('game-iframe');
            iframe.setAttribute('allowfullscreen', 'true');
            iframe.setAttribute('webkitallowfullscreen', 'true');
            iframe.setAttribute('mozallowfullscreen', 'true');
            iframe.setAttribute('allow', 'autoplay; fullscreen *; geolocation; microphone; camera; midi; monetization; xr-spatial-tracking; gamepad; gyroscope; accelerometer; xr; cross-origin-isolated');
            
            // 设置加载和错误处理
            let loadTimeout = null;
            
            // 加载超时处理
            loadTimeout = setTimeout(() => {
                this._log('游戏加载超时', 'warn');
                loadTimeout = null;
                // 超时不一定意味着失败，可能游戏正在加载中
                resolve();
            }, 8000);
            
            // 加载完成
            iframe.addEventListener('load', () => {
                if (loadTimeout) {
                    clearTimeout(loadTimeout);
                    loadTimeout = null;
                }
                
                this._log('游戏框架已加载');
                
                // 如果是代理URL，发送消息加载实际游戏
                if (iframe.src.includes(this.proxyUrl)) {
                    this._sendGameUrl(gameUrl);
                }
                
                resolve();
            });
            
            // 加载错误
            iframe.addEventListener('error', (error) => {
                if (loadTimeout) {
                    clearTimeout(loadTimeout);
                    loadTimeout = null;
                }
                
                this._log(`游戏加载失败: ${error}`, 'error');
                reject(error);
            });
            
            // 清空容器
            while (this.frameElement.firstChild) {
                this.frameElement.removeChild(this.frameElement.firstChild);
            }
            
            // 添加iframe到容器
            this.frameElement.appendChild(iframe);
            
            // 设置iframe源
            if (this._shouldUseProxy(gameUrl)) {
                // 使用代理加载
                iframe.src = this.proxyUrl;
            } else {
                // 直接加载
                iframe.src = gameUrl;
            }
        });
    }
    
    /**
     * 切换全屏状态
     */
    toggleFullscreen() {
        if (!this.frameElement) return;
        
        // 获取iframe
        const iframe = this.frameElement.querySelector('iframe');
        if (!iframe) return;
        
        this.isFullscreen = !this.isFullscreen;
        
        if (this.isFullscreen) {
            // 进入全屏
            this._enterFullscreen(iframe);
        } else {
            // 退出全屏
            this._exitFullscreen();
        }
    }
    
    /**
     * 判断是否需要使用代理
     * @param {string} url 游戏URL
     * @returns {boolean} 是否使用代理
     */
    _shouldUseProxy(url) {
        if (!url) return false;
        
        try {
            // 解析URL
            const gameUrlObj = new URL(url);
            const currentUrlObj = new URL(window.location.href);
            
            // 检查是否跨域
            const isCrossDomain = gameUrlObj.origin !== currentUrlObj.origin;
            
            // 某些域名需要特殊处理 - 更新域名列表，添加所有已知会限制嵌入的游戏站点
            const specialDomains = [
                'crazygames.com',
                'poki.com',
                'y8.com',
                'gamedistribution.com',
                'crazygames.jp',
                'yad.com',
                'games.io',
                'igames.io',
                'crazyshooters2.io',
                'yandex.ru',
                'gamepix.com',
                'silvergames.com',
                'addictinggames.com',
                'miniclip.com',
                'kongregate.com',
                'agame.com',
                'newgrounds.com',
                'kizi.com',
                'games.crazygames',
                'unity3d.com',
                'unity.com',
                'unityads',
                'io' // 通常io结尾的游戏需要特殊处理
            ];
            
            // 更严格的检查
            const isSpecialDomain = specialDomains.some(domain => 
                gameUrlObj.hostname.includes(domain) || 
                gameUrlObj.pathname.includes(domain)
            );
            
            this._log(`URL分析: ${url}, 跨域: ${isCrossDomain}, 特殊域名: ${isSpecialDomain}`);
            
            // 始终使用代理模式 - 更保险的做法
            return true; // 返回true将使所有游戏都使用代理加载
        } catch (error) {
            this._log(`URL分析错误: ${error}`, 'error');
            return true; // 出错时默认使用代理
        }
    }
    
    /**
     * 向代理页面发送游戏URL
     * @param {string} gameUrl 游戏URL
     */
    _sendGameUrl(gameUrl) {
        const iframe = this.frameElement.querySelector('iframe');
        if (!iframe) return;
        
        this._log(`向代理发送游戏URL: ${gameUrl}`);
        
        // 代理准备好后再发送消息
        const sendMessage = () => {
            iframe.contentWindow.postMessage({
                type: 'loadGame',
                gameUrl: gameUrl
            }, '*');
        };
        
        // 如果iframe已加载完成，直接发送
        if (iframe.contentDocument && iframe.contentDocument.readyState === 'complete') {
            sendMessage();
        } else {
            // 否则等待加载完成
            setTimeout(sendMessage, 500);
        }
    }
    
    /**
     * 进入全屏
     * @param {HTMLElement} element 要全屏的元素
     */
    _enterFullscreen(element) {
        this._log('请求全屏');
        
        // 尝试所有全屏方法
        const requestFullscreen = element.requestFullscreen || 
            element.webkitRequestFullscreen || 
            element.mozRequestFullScreen || 
            element.msRequestFullscreen;
        
        if (requestFullscreen) {
            requestFullscreen.call(element).catch(error => {
                this._log(`全屏请求失败: ${error}`, 'error');
                
                // 备用方法：如果是内部iframe，可以发送消息让其请求全屏
                this._sendFullscreenRequest();
            });
        } else {
            this._log('浏览器不支持全屏API', 'warn');
            // 使用CSS模拟全屏
            this._simulateFullscreen(element);
        }
    }
    
    /**
     * 退出全屏
     */
    _exitFullscreen() {
        this._log('退出全屏');
        
        // 尝试所有退出全屏方法
        const exitFullscreen = document.exitFullscreen || 
            document.webkitExitFullscreen || 
            document.mozCancelFullScreen || 
            document.msExitFullscreen;
        
        if (exitFullscreen) {
            if (document.fullscreenElement || 
                document.webkitFullscreenElement || 
                document.mozFullScreenElement || 
                document.msFullscreenElement) {
                exitFullscreen.call(document).catch(error => {
                    this._log(`退出全屏失败: ${error}`, 'error');
                });
            }
        }
        
        // 移除CSS模拟全屏
        const iframe = this.frameElement.querySelector('iframe');
        if (iframe) {
            iframe.classList.remove('simulated-fullscreen');
        }
    }
    
    /**
     * 使用CSS模拟全屏
     * @param {HTMLElement} element 要全屏的元素
     */
    _simulateFullscreen(element) {
        this._log('使用CSS模拟全屏');
        element.classList.add('simulated-fullscreen');
        
        // 添加样式
        if (!document.getElementById('fullscreen-style')) {
            const style = document.createElement('style');
            style.id = 'fullscreen-style';
            style.textContent = `
                .simulated-fullscreen {
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    width: 100% !important;
                    height: 100% !important;
                    z-index: 9999 !important;
                    background: #000 !important;
                    border: none !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
            `;
            document.head.appendChild(style);
        }
        
        // 添加退出模拟全屏的按钮
        const exitBtn = document.createElement('button');
        exitBtn.textContent = '退出全屏';
        exitBtn.style.cssText = 'position:fixed;top:10px;right:10px;z-index:10000;padding:8px 12px;background:#e74c3c;color:#fff;border:none;border-radius:4px;cursor:pointer;';
        document.body.appendChild(exitBtn);
        
        exitBtn.addEventListener('click', () => {
            element.classList.remove('simulated-fullscreen');
            document.body.removeChild(exitBtn);
            this.isFullscreen = false;
        });
    }
    
    /**
     * 向内部iframe发送全屏请求
     */
    _sendFullscreenRequest() {
        const iframe = this.frameElement.querySelector('iframe');
        if (!iframe) return;
        
        this._log('向iframe发送全屏请求');
        
        iframe.contentWindow.postMessage({
            type: 'requestFullscreen'
        }, '*');
    }
    
    /**
     * 处理消息事件
     * @param {MessageEvent} event 消息事件
     */
    _handleMessage(event) {
        try {
            const data = event.data;
            
            // 确保消息是对象且有类型
            if (!data || typeof data !== 'object' || !data.type) return;
            
            this._log(`收到消息: ${data.type}`);
            
            switch (data.type) {
                case 'proxyReady':
                    // 代理页面已准备好，发送游戏URL
                    this._sendGameUrl(this.gameUrl);
                    break;
                    
                case 'gameLoaded':
                    // 游戏已加载
                    this._log('游戏已成功加载');
                    break;
                    
                case 'gameError':
                    // 游戏加载错误
                    this._log(`游戏加载错误: ${data.error}`, 'error');
                    break;
                    
                case 'exitFullscreen':
                    // 退出全屏请求
                    this.isFullscreen = false;
                    this._exitFullscreen();
                    break;
            }
        } catch (error) {
            this._log(`处理消息错误: ${error}`, 'error');
        }
    }
    
    /**
     * 记录日志
     * @param {string} message 日志消息
     * @param {string} level 日志级别
     */
    _log(message, level = 'info') {
        if (!this.debug && level !== 'error') return;
        
        const prefix = '[GameLoader]';
        
        switch (level) {
            case 'error':
                console.error(prefix, message);
                break;
            case 'warn':
                console.warn(prefix, message);
                break;
            default:
                console.log(prefix, message);
        }
    }
    
    /**
     * 销毁加载器实例
     */
    destroy() {
        this._log('销毁游戏加载器');
        
        // 移除事件监听
        window.removeEventListener('message', this._handleMessage);
        
        if (this.fullscreenBtn) {
            this.fullscreenBtn.removeEventListener('click', this.toggleFullscreen);
        }
        
        // 清除引用
        this.frameElement = null;
        this.fullscreenBtn = null;
    }
}

// 导出为全局变量
window.GameProxyLoader = GameProxyLoader;

// 自动初始化
document.addEventListener('DOMContentLoaded', function() {
    // 自动检测游戏URL和容器
    const gameEl = document.getElementById('game-frame');
    if (gameEl && gameEl.dataset.url) {
        const proxy = new GameProxyLoader({ debug: true });
        proxy.init();
        proxy.loadGame(gameEl.dataset.url);
    }
}); 