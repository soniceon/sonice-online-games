import { Toast } from './Toast.js';

export class GameLoader {
    constructor() {
        this.container = this.createContainer();
        this.currentGame = null;
        document.body.appendChild(this.container);
    }

    createContainer() {
        const container = document.createElement('div');
        container.className = 'fixed inset-0 bg-black bg-opacity-90 hidden z-50 flex-col';
        container.innerHTML = `
            <div class="flex justify-between items-center p-4 bg-dark-lighter">
                <h2 class="text-xl font-bold text-white game-title"></h2>
                <div class="flex items-center space-x-4">
                    <button class="fullscreen-btn p-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0 0l-5-5m-7 11h4m-4 0v4m0-4l5 5m11-5h-4m4 0v4m0-4l-5 5"/>
                        </svg>
                    </button>
                    <button class="close-btn p-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="flex-1 relative">
                <div class="absolute inset-0 flex items-center justify-center loading-indicator">
                    <div class="w-16 h-16 border-4 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
                <iframe class="w-full h-full" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
            </div>
        `;

        // 添加事件监听
        container.querySelector('.close-btn').addEventListener('click', () => this.hideGame());
        container.querySelector('.fullscreen-btn').addEventListener('click', () => this.toggleFullscreen());

        return container;
    }

    loadGame(gameId, gameTitle) {
        this.currentGame = { id: gameId, title: gameTitle };
        this.container.querySelector('.game-title').textContent = gameTitle;
        this.container.classList.remove('hidden');
        this.container.classList.add('flex');

        // 显示加载动画
        const loadingIndicator = this.container.querySelector('.loading-indicator');
        loadingIndicator.style.display = 'flex';

        // 模拟游戏加载
        setTimeout(() => {
            const iframe = this.container.querySelector('iframe');
            iframe.src = `/games/${gameId}/index.html`;
            
            // 监听iframe加载完成
            iframe.onload = () => {
                loadingIndicator.style.display = 'none';
                Toast.show(`${gameTitle} 已加载完成`, 'success');
            };
        }, 1000);
    }

    hideGame() {
        this.container.classList.add('hidden');
        this.container.classList.remove('flex');
        const iframe = this.container.querySelector('iframe');
        iframe.src = '';
        this.currentGame = null;
    }

    toggleFullscreen() {
        if (!document.fullscreenElement) {
            this.container.requestFullscreen();
        } else {
            document.exitFullscreen();
        }
    }
}

// 创建全局实例
const gameLoader = new GameLoader();
export default gameLoader; 