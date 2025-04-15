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
                throw new Error('Failed to fetch game data');
            }
            
            const data = await response.json();
            this.gameData = data.games.find(g => g.id === gameId) || 
                          (data.featured && data.featured.id === gameId ? data.featured : null);

            if (!this.gameData) {
                throw new Error('Game not found');
            }

            // Initialize UI and events
            this.updateGameInfo();
            this.setupEventListeners();
        } catch (error) {
            console.error('Error initializing game:', error);
            this.showError('Failed to load game data');
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
        if (this.isUsingPrimary && this.gameData.game_urls.backup) {
            console.log('Primary URL failed, trying backup URL...');
            this.isUsingPrimary = false;
            this.tryLoadGameUrl(this.gameData.game_urls.backup);
        } else {
            this.showError('Failed to load game. Please try again later.');
        }
    }

    showError(message = 'An error occurred while loading the game') {
        this.loading.style.display = 'none';
        this.frame.style.display = 'none';
        this.start.style.display = 'none';
        this.error.style.display = 'flex';

        const errorMessage = this.error.querySelector('p');
        if (errorMessage) {
            errorMessage.textContent = message;
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