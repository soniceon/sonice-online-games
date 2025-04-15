import LoadingSpinner from '../components/LoadingSpinner.js';
import ErrorToast from '../components/ErrorToast.js';
import GameCard from '../components/GameCard.js';
import { debounce, setupLazyLoading, storage } from '../utils/helpers.js';

class App {
    constructor() {
        this.loadingSpinner = new LoadingSpinner();
        this.errorToast = new ErrorToast();
        this.setupEventListeners();
        this.initializeApp();
    }

    setupEventListeners() {
        // 移动端菜单切换
        const menuToggle = document.getElementById('menu-toggle');
        if (menuToggle) {
            menuToggle.addEventListener('click', this.handleMenuToggle.bind(this));
        }

        // 搜索防抖
        const searchInput = document.querySelector('input[name="query"]');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(this.handleSearch.bind(this), 300));
        }

        // 监听滚动以实现无限加载
        window.addEventListener('scroll', debounce(this.handleScroll.bind(this), 100));
    }

    async initializeApp() {
        try {
            this.loadingSpinner.show();
            await this.loadGames();
            setupLazyLoading();
        } catch (error) {
            console.error('Error initializing app:', error);
            this.errorToast.show('Failed to initialize the application');
        } finally {
            this.loadingSpinner.hide();
        }
    }

    async loadGames() {
        try {
            const cachedGames = storage.get('games');
            if (cachedGames) {
                this.displayGames(cachedGames);
                // 在后台更新缓存
                this.updateGamesCache();
                return;
            }

            const response = await fetch('data/games.json');
            if (!response.ok) {
                throw new Error(`Failed to fetch games: ${response.status}`);
            }

            const data = await response.json();
            this.displayGames(data);
            storage.set('games', data);
        } catch (error) {
            console.error('Error loading games:', error);
            this.errorToast.show('Failed to load games. Please try again later.');
            throw error;
        }
    }

    displayGames(data) {
        // 显示特色游戏
        if (data.featured) {
            this.displayFeaturedGame(data.featured);
        }

        // 显示热门游戏
        if (data.games && data.games.length > 0) {
            GameCard.createGameGrid(data.games.slice(0, 8), 'popular-games-container');
        }
    }

    displayFeaturedGame(game) {
        const featuredSection = document.querySelector('section[aria-label="Featured game"]');
        if (!featuredSection) return;

        const gameCard = new GameCard(game);
        featuredSection.innerHTML = `
            <div class="md:flex">
                <div class="md:w-1/2">
                    ${gameCard.render()}
                </div>
                <div class="md:w-1/2 p-6">
                    <div class="bg-apple-red text-white inline-block px-3 py-1 rounded-full text-sm font-bold mb-4">Featured Game</div>
                    <h2 class="text-3xl font-bold mb-4 text-apple-blue">${game.title}</h2>
                    <p class="text-gray-700 mb-6">${game.description}</p>
                    <a href="games/${game.id}.html" 
                       class="bg-apple-green hover:bg-opacity-90 text-white font-bold py-3 px-6 rounded-lg inline-block transition"
                       aria-label="Play ${game.title}">Play Now</a>
                </div>
            </div>
        `;
    }

    handleMenuToggle() {
        const menu = document.getElementById('menu');
        const openIcon = document.getElementById('menu-open');
        const closedIcon = document.getElementById('menu-closed');
        const menuToggle = document.getElementById('menu-toggle');

        if (!menu || !openIcon || !closedIcon || !menuToggle) return;

        menu.classList.toggle('hidden');
        const isExpanded = !menu.classList.contains('hidden');

        if (isExpanded) {
            openIcon.classList.remove('hidden');
            closedIcon.classList.add('hidden');
            menuToggle.setAttribute('aria-expanded', 'true');
        } else {
            openIcon.classList.add('hidden');
            closedIcon.classList.remove('hidden');
            menuToggle.setAttribute('aria-expanded', 'false');
        }
    }

    async handleSearch(event) {
        const query = event.target.value.toLowerCase();
        if (query.length < 2) return;

        try {
            const games = storage.get('games');
            if (!games) return;

            const results = games.games.filter(game => 
                game.title.toLowerCase().includes(query) ||
                game.description.toLowerCase().includes(query) ||
                game.categories.some(cat => cat.toLowerCase().includes(query))
            );

            GameCard.createGameGrid(results, 'popular-games-container');
        } catch (error) {
            console.error('Error searching games:', error);
            this.errorToast.show('Error searching games');
        }
    }

    async handleScroll() {
        // 实现无限滚动加载
        const {scrollTop, scrollHeight, clientHeight} = document.documentElement;
        if (scrollTop + clientHeight >= scrollHeight - 5) {
            // 加载更多游戏
            // TODO: 实现分页加载
        }
    }

    async updateGamesCache() {
        try {
            const response = await fetch('data/games.json');
            if (!response.ok) return;

            const data = await response.json();
            storage.set('games', data);
        } catch (error) {
            console.error('Error updating games cache:', error);
        }
    }
}

// 初始化应用
document.addEventListener('DOMContentLoaded', () => {
    new App();
}); 