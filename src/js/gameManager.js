// Game Manager Module
class GameManager {
    constructor() {
        this.games = {
            featured: [],
            action: [],
            racing: [],
            sports: [],
            shooter: [],
            cards: [],
            adventure: [],
            puzzle: [],
            strategy: [],
            mining: [],      // 新增挖矿类别
            idle: [],        // 新增放置类别
            clicker: [],     // 新增点击类别
            simulation: [],  // 新增模拟经营类别
            tycoon: [],      // 新增经营类别
            merge: [],        // 新增合并类别
            weapon: [],       // 新增武器测试类别
            notebook: [],    // 新增笔记本世界类别
            cyber: [],        // 新增未来农场类别
            epic: [],         // 新增史诗游戏类别
            grow: [],         // 新增成长防御类别
            knights: [],      // 新增骑士类别
            lunar: [],        // 新增月球原子类别
            fish: []          // 新增鱼类合并类别
        };
        
        // 添加图片预加载功能
        this.preloadImages = new Map();
    }

    // 新增：预加载图片
    async preloadImage(src) {
        if (this.preloadImages.has(src)) {
            return this.preloadImages.get(src);
        }

        const promise = new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => resolve(src);
            img.onerror = () => reject(new Error(`Failed to load image: ${src}`));
            img.src = src;
        });

        this.preloadImages.set(src, promise);
        return promise;
    }

    // 新增：获取可用的图片路径
    async getValidImagePath(gameId, originalPath) {
        const basePaths = [
            `/assets/images/games/${gameId}-360-240.webp`,
            `./assets/images/games/${gameId}-360-240.webp`,
            `../assets/images/games/${gameId}-360-240.webp`,
            originalPath
        ];

        for (const path of basePaths) {
            try {
                await this.preloadImage(path);
                return path;
            } catch (error) {
                console.log(`Failed to load image: ${path}`);
                continue;
            }
        }

        return './assets/images/default-game.webp';
    }

    async loadGames() {
        try {
            const gamesData = await this.fetchGamesData();
            this.categorizeGames(gamesData);
            return true;
        } catch (error) {
            console.error('Error loading games:', error);
            return false;
        }
    }

    async fetchGamesData() {
        console.log('Fetching games data...');
        const games = [
            // 射击游戏
            {
                id: 'aim-trainer-idle',
                title: 'Aim Trainer Idle',
                image: './assets/images/games/aim-trainer-idle-360-240.webp',
                categories: ['shooter', 'featured'],
                description: 'Train your aim with this idle game'
            },
            {
                id: 'alchemy-merge-clicker',
                title: 'Alchemy Merge City',
                image: './assets/images/games/alchemy-merge-clicker-360-240.webp',
                categories: ['merge', 'featured'],
                description: 'Merge items to build your alchemy city'
            },
            {
                id: 'angles',
                title: 'Angles',
                image: './assets/images/games/angles-360-240.webp',
                categories: ['puzzle'],
                description: 'Solve angle-based puzzles'
            },
            {
                id: 'animal-zoo-clicker',
                title: 'Animal Zoo Clicker',
                image: './assets/images/games/animal-zoo-clicker-360-240.webp',
                categories: ['clicker', 'simulation'],
                description: 'Build and manage your own zoo'
            },
            {
                id: 'ants-fruits',
                title: 'Ants Fruits',
                image: './assets/images/games/ants-fruits-360-240.webp',
                categories: ['action'],
                description: 'Help ants collect fruits'
            },
            {
                id: 'art-tycoon',
                title: 'Art Tycoon',
                image: './assets/images/games/art-tycoon-360-240.webp',
                categories: ['tycoon', 'simulation'],
                description: 'Build your art empire'
            },
            {
                id: 'cs-online',
                title: 'CS Online',
                image: './assets/images/games/cs-online-360-240.webp',
                categories: ['shooter', 'featured'],
                description: 'Classic counter-strike style online shooting game'
            },

            // 竞速游戏
            {
                id: 'drift-hunters-pro',
                title: 'Drift Hunters Pro',
                image: './assets/images/games/drift-hunters-pro-360-240.webp',
                categories: ['racing'],
                description: 'Advanced drift racing experience'
            },
            {
                id: 'drift-king',
                title: 'Drift King',
                image: './assets/images/games/drift-king-360-240.webp',
                categories: ['racing'],
                description: 'Become the king of drift racing'
            },
            {
                id: 'crazy-parking-fury',
                title: 'Crazy Parking Fury',
                image: './assets/images/games/crazy-parking-fury-360-240.webp',
                categories: ['racing'],
                description: 'Challenging parking simulation game'
            },
            {
                id: 'offroad-rally',
                title: 'Offroad Rally',
                image: './assets/images/games/offroad-rally-360-240.webp',
                categories: ['racing'],
                description: 'Extreme off-road racing adventure'
            },
            {
                id: 'fort-drifter',
                title: 'Fort Drifter',
                image: './assets/images/games/fort-drifter-360-240.webp',
                categories: ['racing'],
                description: 'Drift racing in fortress environments'
            },

            // 体育游戏
            {
                id: 'basketball-superstars',
                title: 'Basketball Superstars',
                image: './assets/images/games/basketball-superstars-360-240.webp',
                categories: ['sports'],
                description: 'Become a basketball superstar'
            },
            {
                id: 'hoop-world-3d',
                title: 'Hoop World 3D',
                image: './assets/images/games/hoop-world-3d-360-240.webp',
                categories: ['sports'],
                description: '3D basketball world adventure'
            },

            // 冒险游戏
            {
                id: 'fireboy-and-watergirl-3',
                title: 'Fireboy and Watergirl 3',
                image: './assets/images/games/fireboy-and-watergirl-3-360-240.webp',
                categories: ['adventure', 'puzzle'],
                description: 'Cooperative elemental adventure'
            },
            {
                id: 'snow-rider-3d',
                title: 'Snow Rider 3D',
                image: './assets/images/games/snow-rider-3d-360-240.webp',
                categories: ['adventure', 'sports'],
                description: '3D snow boarding adventure'
            },

            // 从Excel表格添加的新游戏
            {
                id: 'falling-fruits',
                title: 'Falling Fruits',
                image: './assets/images/games/falling-fruits-360-240.webp',
                categories: ['action', 'featured'],
                description: 'Catch falling fruits in this fun arcade game'
            },
            {
                id: 'magic-chop-idle',
                title: 'Magic Chop Idle',
                image: './assets/images/games/magic-chop-idle-360-240.webp',
                categories: ['idle', 'featured'],
                description: 'Magical chopping idle game'
            },
            {
                id: 'my-sugar-factory-2',
                title: 'My Sugar Factory 2',
                image: './assets/images/games/my-sugar-factory-2-360-240.webp',
                categories: ['simulation', 'featured'],
                description: 'Build and manage your sugar factory'
            },
            {
                id: 'slime-farm-remake',
                title: 'Slime Farm Remake',
                image: './assets/images/games/slime-farm-remake-360-240.webp',
                categories: ['simulation', 'featured'],
                description: 'Manage your slime farm'
            },
            {
                id: 'cupcake-clicker',
                title: 'Cupcake Clicker',
                image: './assets/images/games/cupcake-clicker-360-240.webp',
                categories: ['clicker', 'featured'],
                description: 'Sweet clicking adventure'
            },
            {
                id: 'haste-miner',
                title: 'Haste Miner',
                image: './assets/images/games/haste-miner-360-240.webp',
                categories: ['mining', 'featured'],
                description: 'Fast-paced mining game'
            },
            {
                id: 'doggo-clicker',
                title: 'Doggo Clicker',
                image: './assets/images/games/doggo-clicker-360-240.webp',
                categories: ['clicker', 'featured'],
                description: 'Click to help cute dogs'
            },
            {
                id: 'idle-miner',
                title: 'Idle Miner',
                image: './assets/images/games/idle-miner-360-240.webp',
                categories: ['idle', 'mining'],
                description: 'Automated mining simulation'
            },
            {
                id: 'merge-pickaxe',
                title: 'Merge Pickaxe',
                image: './assets/images/games/merge-pickaxe-360-240.webp',
                categories: ['merge'],
                description: 'Merge tools to mine resources'
            },
            {
                id: 'weapon-tester',
                title: 'Weapon Tester',
                image: './assets/images/games/weapon-tester-360-240.webp',
                categories: ['action'],
                description: 'Test various weapons'
            },
            {
                id: 'mining-in-notebook',
                title: 'Mining in Notebook',
                image: './assets/images/games/mining-in-notebook-360-240.webp',
                categories: ['mining'],
                description: 'Mine in a notebook world'
            },
            {
                id: 'cyber-farm',
                title: 'Cyber Farm',
                image: './assets/images/games/cyber-farm-360-240.webp',
                categories: ['simulation'],
                description: 'Build a futuristic farm'
            },
            {
                id: 'grow-defense',
                title: 'Grow Defense',
                image: './assets/images/games/grow-defense-360-240.webp',
                categories: ['strategy'],
                description: 'Grow and defend your base'
            },
            {
                id: 'lunar-atoms-tycoon',
                title: 'Lunar Atoms Tycoon',
                image: './assets/images/games/lunar-atoms-tycoon-360-240.webp',
                categories: ['tycoon'],
                description: 'Build an atomic empire on the moon'
            },
            {
                id: 'fish-merge-frvr',
                title: 'Fish Merge FRVR',
                image: './assets/images/games/fish-merge-frvr-360-240.webp',
                categories: ['merge'],
                description: 'Merge fish to evolve them'
            },
            {
                id: 'colorful-city-of-cards',
                title: 'Colorful City of Cards',
                image: './assets/images/games/colorful-city-of-cards-360-240.webp',
                categories: ['cards'],
                description: 'Build a colorful card city'
            },
            {
                id: 'my-sugar-factory',
                title: 'My Sugar Factory',
                image: './assets/images/games/my-sugar-factory-360-240.webp',
                categories: ['simulation'],
                description: 'Build your sugar empire'
            }
        ];
        
        // 删除重复的游戏条目
        const uniqueGames = [];
        const seenIds = new Set();
        
        games.forEach(game => {
            if (!seenIds.has(game.id)) {
                // 只处理不能显示的游戏卡片的图片路径
                if (!game.image.includes('-360-240.webp')) {
                    game.image = `./assets/images/games/${game.id}-360-240.webp`;
                }
                seenIds.add(game.id);
                uniqueGames.push(game);
            }
        });
        
        console.log(`Fetched ${uniqueGames.length} unique games`);
        return uniqueGames;
    }

    categorizeGames(games) {
        console.log('Categorizing games...');
        // Reset all categories
        Object.keys(this.games).forEach(category => {
            this.games[category] = [];
        });

        // Categorize games
        games.forEach(game => {
            game.categories.forEach(category => {
                if (this.games.hasOwnProperty(category)) {
                    this.games[category].push(game);
                }
            });
        });

        // Log category counts
        Object.entries(this.games).forEach(([category, games]) => {
            console.log(`Category ${category}: ${games.length} games`);
        });
    }

    createGameCard(game) {
        const defaultImage = './assets/images/default-game.webp';
        
        // 使用数据属性存储所有可能的图片路径
        const imagePathsData = JSON.stringify([
            `/assets/images/games/${game.id}-360-240.webp`,
            `../../assets/images/games/${game.id}-360-240.webp`,
            `../../../assets/images/games/${game.id}-360-240.webp`,
            game.image
        ]);
        
        // 使用相对路径生成游戏链接
        const gameLink = `./pages/games/${game.id}.html`;
        
        return `
            <div class="game-card bg-dark-lighter rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 w-full" 
                data-game-id="${game.id}"
                data-image-paths='${imagePathsData}'>
                <a href="${gameLink}" class="block relative group">
                    <div class="relative w-full aspect-[16/10] overflow-hidden bg-gray-800">
                        <img 
                            src="../../assets/images/games/${game.id}-360-240.webp"
                            alt="${game.title}" 
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-300"
                            loading="lazy"
                            onerror="
                                const paths = JSON.parse(this.closest('.game-card').dataset.imagePaths);
                                const currentIndex = paths.indexOf(this.src);
                                if (currentIndex < paths.length - 1) {
                                    this.src = paths[currentIndex + 1];
                                } else {
                                    this.src = '${defaultImage}';
                                    this.closest('.game-card').classList.add('image-load-failed');
                                }
                            ">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <button onclick="event.preventDefault(); window.location.href='${gameLink}'" class="px-6 py-2 bg-purple-600 text-white rounded-full transform -translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                                Play Now
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-white font-semibold text-lg mb-2 line-clamp-1 group-hover:text-purple-400 transition-colors duration-200">
                            ${game.title}
                        </h3>
                        <p class="text-gray-400 text-sm line-clamp-2 mb-3 group-hover:text-gray-300 transition-colors duration-200">
                            ${game.description}
                        </p>
                        <div class="flex flex-wrap gap-2">
                            ${game.categories.map(category => 
                                `<span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-600/10 text-purple-400 capitalize">
                                    ${category}
                                </span>`
                            ).join('')}
                        </div>
                    </div>
                </a>
            </div>
        `;
    }

    displayGamesInCategory(category) {
        console.log(`Displaying games for category: ${category}`);
        const section = document.querySelector(`section[data-category="${category}"]`);
        if (!section) {
            console.warn(`Section not found for category: ${category}`);
            return;
        }

        const gamesContainer = section.querySelector('.grid');
        if (!gamesContainer) {
            console.warn(`Grid container not found in section for category: ${category}`);
            return;
        }

        const games = this.games[category];
        if (!games || games.length === 0) {
            console.log(`No games found for category: ${category}`);
            section.style.display = 'none';
            return;
        }

        console.log(`Found ${games.length} games for category: ${category}`);
        
        // 直接渲染游戏卡片
        const gameCardsHtml = games.map(game => this.createGameCard(game)).join('');
        gamesContainer.innerHTML = gameCardsHtml;
        
        // 确保网格布局正确
        gamesContainer.classList.add('grid', 'grid-cols-1', 'sm:grid-cols-2', 'md:grid-cols-3', 'lg:grid-cols-4', 'xl:grid-cols-5', 'gap-4', 'p-4');
        
        section.style.display = 'block';
        
        // 添加图片加载后的处理
        section.querySelectorAll('.game-card img').forEach(img => {
            img.addEventListener('load', () => {
                console.log(`Image loaded successfully: ${img.alt}`);
            });
            img.addEventListener('error', () => {
                console.error(`Failed to load image: ${img.alt}, src: ${img.src}`);
            });
        });
    }

    displayAllCategories() {
        console.log('Starting to display all categories...');
        Object.keys(this.games).forEach(category => {
            this.displayGamesInCategory(category);
        });
        console.log('Finished displaying all categories');
    }
}

// Export the GameManager class
export { GameManager }; 