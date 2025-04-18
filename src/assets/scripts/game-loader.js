class GameLoader {
    constructor() {
        this.container = document.getElementById('game-container');
        this.frame = document.getElementById('game-frame');
        this.loading = document.getElementById('game-loading');
        this.start = document.getElementById('game-start');
        this.error = document.getElementById('game-error');
        this.isUsingPrimary = true;

        // Bind methods
        this.retryGame = this.retryGame.bind(this);
        this.loadGame = this.loadGame.bind(this);
        this.handleLoadError = this.handleLoadError.bind(this);
    }

    async init(gameId) {
        this.gameId = gameId;
        try {
            const response = await fetch('../data/games.json');
            if (!response.ok) {
                throw new Error(`Failed to fetch game data: ${response.status}`);
            }
            
            const data = await response.json();
            this.gameData = data.games.find(g => g.id === gameId) || 
                          (data.featured && data.featured.id === gameId ? data.featured : null);

            if (!this.gameData) {
                throw new Error(`Game not found: ${gameId}`);
            }

            // Initialize UI and events
            this.updateGameInfo();
            this.setupEventListeners();
            this.loadingAttempts = 0; // 重置加载尝试次数
        } catch (error) {
            console.error('Error initializing game:', error);
            this.showError(`无法加载游戏数据: ${error.message}`);
        }
    }

    updateGameInfo() {
        // Update page metadata
        document.title = `${this.gameData.title} - Sonice Online Games`;
        const metaDesc = document.querySelector('meta[name="description"]');
        if (metaDesc) {
            metaDesc.content = this.gameData.description;
        }

        // Update game info elements
        const titleEl = document.getElementById('game-title');
        const descEl = document.getElementById('game-description');
        const ratingEl = document.getElementById('game-rating');
        const reviewsEl = document.getElementById('game-reviews');
        
        if (titleEl) titleEl.textContent = this.gameData.title;
        if (descEl) descEl.textContent = this.gameData.description;
        if (ratingEl) ratingEl.textContent = this.gameData.rating;
        if (reviewsEl) reviewsEl.textContent = `${this.gameData.reviews} reviews`;
    }

    setupEventListeners() {
        if (this.start) {
            this.start.onclick = this.loadGame;
        }

        const retryButton = document.querySelector('#game-error button');
        if (retryButton) {
            retryButton.onclick = this.retryGame;
        }

        this.frame.onload = () => {
            this.loading.style.display = 'none';
            this.frame.style.display = 'block';
            this.trackGameStart();
        };

        this.frame.onerror = this.handleLoadError;
    }

    loadGame() {
        if (!this.gameData) return;

        this.start.style.display = 'none';
        this.loading.style.display = 'flex';
        this.frame.style.display = 'none';
        this.error.style.display = 'none';
        
        // Try primary URL first
        this.isUsingPrimary = true;
        this.tryLoadGameUrl(this.gameData.game_urls.primary);
    }

    tryLoadGameUrl(url) {
        this.frame.src = url;
    }

    handleLoadError() {
        this.loadingAttempts = (this.loadingAttempts || 0) + 1;
        
        if (this.isUsingPrimary && this.gameData.game_urls.backup) {
            console.log('Primary URL failed, trying backup URL...');
            this.isUsingPrimary = false;
            this.tryLoadGameUrl(this.gameData.game_urls.backup);
        } else if (this.loadingAttempts < 3) { // 最多尝试3次
            console.log(`Retry attempt ${this.loadingAttempts}...`);
            setTimeout(() => {
                this.tryLoadGameUrl(this.isUsingPrimary ? 
                    this.gameData.game_urls.primary : 
                    this.gameData.game_urls.backup);
            }, 1000 * this.loadingAttempts); // 递增延迟
        } else {
            this.showError('游戏加载失败。请检查您的网络连接并稍后重试。');
        }
    }

    showError(message = '加载游戏时发生错误') {
        this.loading.style.display = 'none';
        this.frame.style.display = 'none';
        this.start.style.display = 'none';
        this.error.style.display = 'flex';

        const errorMessage = this.error.querySelector('p');
        if (errorMessage) {
            errorMessage.textContent = message;
        }

        // 添加错误详情和帮助提示
        const errorDetails = this.error.querySelector('.error-details');
        if (!errorDetails) {
            const details = document.createElement('div');
            details.className = 'error-details text-sm text-gray-400 mt-2';
            details.innerHTML = `
                <p>可能的原因：</p>
                <ul class="list-disc list-inside mt-1">
                    <li>网络连接不稳定</li>
                    <li>游戏服务器暂时不可用</li>
                    <li>浏览器设置可能阻止了游戏加载</li>
                </ul>
            `;
            this.error.appendChild(details);
        }
    }

    retryGame() {
        this.error.style.display = 'none';
        this.loadGame();
    }

    trackGameStart() {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'game_start', {
                'event_category': 'games',
                'event_label': this.gameData.title,
                'game_id': this.gameId,
                'game_category': this.gameData.categories.join(',')
            });
        }
    }
}

// Make GameLoader available globally
window.GameLoader = GameLoader;