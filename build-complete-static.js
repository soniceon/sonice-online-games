const fs = require('fs');
const path = require('path');

// 读取CSV文件
function loadGamesFromCSV() {
    const csvFile = path.join(__dirname, '游戏iframe.CSV');
    if (!fs.existsSync(csvFile)) {
        return [];
    }
    
    const games = [];
    const content = fs.readFileSync(csvFile, 'utf-8');
    const lines = content.split('\n');
    
    // 跳过标题行
    for (let i = 1; i < lines.length; i++) {
        const line = lines[i].trim();
        if (!line) continue;
        
        const parts = line.split(',');
        if (parts.length >= 3) {
            const title = parts[0].replace(/"/g, '');
            const iframeUrl = parts[1].replace(/"/g, '');
            const categories = parts[2].replace(/"/g, '').split(',').map(cat => cat.trim());
            
            // 生成slug
            const slug = title.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            
            games.push({
                slug: slug,
                title: title,
                iframeUrl: iframeUrl,
                categories: categories
            });
        }
    }
    
    return games;
}

// 分类配置
const categoryConfig = {
    'idle': { icon: 'fa-solid fa-hourglass', color: '#06d6a0', name: 'Idle' },
    'tycoon': { icon: 'fa-solid fa-building', color: '#4361ee', name: 'Tycoon' },
    'farm': { icon: 'fa-solid fa-seedling', color: '#06d6a0', name: 'Farm' },
    'clicker': { icon: 'fa-solid fa-mouse-pointer', color: '#f72585', name: 'Clicker' },
    'mining': { icon: 'fa-solid fa-gem', color: '#ffd700', name: 'Mining' },
    'card': { icon: 'fa-solid fa-chess', color: '#a259fa', name: 'Card' },
    'monster': { icon: 'fa-solid fa-dragon', color: '#ff6b6b', name: 'Monster' },
    'merge': { icon: 'fa-solid fa-object-group', color: '#4ecdc4', name: 'Merge' },
    'simulator': { icon: 'fa-solid fa-cogs', color: '#a259fa', name: 'Simulator' },
    'defense': { icon: 'fa-solid fa-shield-alt', color: '#ffd166', name: 'Defense' },
    'adventure': { icon: 'fa-solid fa-map', color: '#ffb703', name: 'Adventure' },
    'block': { icon: 'fa-solid fa-cube', color: '#7209b7', name: 'Block' },
    'factory': { icon: 'fa-solid fa-industry', color: '#4361ee', name: 'Factory' },
    'fishing': { icon: 'fa-solid fa-fish', color: '#06d6a0', name: 'Fishing' },
    'runner': { icon: 'fa-solid fa-running', color: '#ff7f50', name: 'Runner' },
    'shooter': { icon: 'fa-solid fa-crosshairs', color: '#ffd166', name: 'Shooter' },
    'fish': { icon: 'fa-solid fa-fish', color: '#06d6a0', name: 'Fish' },
    'treasure': { icon: 'fa-solid fa-gem', color: '#ffd700', name: 'Treasure' },
    'racing': { icon: 'fa-solid fa-car', color: '#ff7f50', name: 'Racing' },
    'dance': { icon: 'fa-solid fa-music', color: '#a259fa', name: 'Dance' },
    'crafting': { icon: 'fa-solid fa-hammer', color: '#7209b7', name: 'Crafting' }
};

// 组织游戏按分类
function organizeGamesByCategory(games) {
    const categories = {};
    
    games.forEach(game => {
        const category = game.categories[0] || 'Other';
        const categorySlug = category.toLowerCase().replace(/\s+/g, '-');
        
        if (!categories[categorySlug]) {
            const config = categoryConfig[categorySlug] || { icon: 'fa-solid fa-gamepad', color: '#888888', name: category };
            categories[categorySlug] = {
                name: config.name,
                slug: categorySlug,
                icon: config.icon,
                color: config.color,
                games: []
            };
        }
        
        categories[categorySlug].games.push(game);
    });
    
    return Object.values(categories);
}

// 生成游戏卡片HTML
function generateGameCard(game) {
    const category = game.categories[0] || 'Other';
    const categorySlug = category.toLowerCase().replace(/\s+/g, '-');
    const config = categoryConfig[categorySlug] || { color: '#888888' };
    
    return `
        <div class="game-card-home">
            <a href="/game/${game.slug}">
                <img src="/assets/images/games/${game.slug}.webp" alt="${game.title}" 
                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDIwMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMTIwIiBmaWxsPSIjMjIyIi8+Cjx0ZXh0IHg9IjEwMCIgeT0iNjAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iI2ZmZiIgdGV4dC1hbmNob3I9Im1pZGRsZSI+${encodeURIComponent(game.title)}</text>
Cjwvc3ZnPg=='">
            </a>
        </div>`;
}

// 生成分类HTML
function generateCategoryHTML(category) {
    const gamesHTML = category.games.slice(0, 7).map(game => generateGameCard(game)).join('');
    
    return `
        <div class="category-block mb-4 group" id="cat-block-${category.slug}">
            <div class="flex items-center mb-2">
                <h2 class="text-xl font-bold text-white mr-2 flex items-center">
                    <span class="inline-block align-middle mr-2">
                        <svg width="22" height="22" fill="white" viewBox="0 0 24 24">
                            <path d="M7 15v2a1 1 0 0 1-2 0v-2H3a1 1 0 0 1 0-2h2v-2a1 1 0 0 1 2 0v2h2a1 1 0 0 1 0 2H7zm10.293-7.707a1 1 0 0 0-1.414 0l-2.586 2.586a1 1 0 0 0 1.414 1.414l2.586-2.586a1 1 0 0 0 0-1.414z"/>
                        </svg>
                    </span>
                    ${category.name} Games
                </h2>
                <a href="/category/${category.slug}" class="ml-2 text-blue-300 hover:text-blue-400 text-base font-medium px-2 py-0.5 rounded transition bg-blue-900/40">More Games</a>
            </div>
            <div class="relative px-2 flex items-center">
                <button class="carousel-arrow carousel-arrow-left absolute left-0 top-1/2" aria-label="Left arrow" onclick="scrollCategoryPage('${category.slug}', -1)"></button>
                <div class="game-grid grid grid-cols-7 gap-2 w-full" id="cat-grid-${category.slug}">
                    ${gamesHTML}
                </div>
                <button class="carousel-arrow carousel-arrow-right absolute right-0 top-1/2" aria-label="Right arrow" onclick="scrollCategoryPage('${category.slug}', 1)"></button>
            </div>
        </div>`;
}

// 生成侧边栏HTML
function generateSidebarHTML(categories) {
    const categoryItems = categories.map(cat => `
        <li>
            <a href="/category/${cat.slug}" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                    <i class="${cat.icon} text-2xl" style="color: ${cat.color};"></i>
                </span>
                <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">${cat.name}</span>
            </a>
        </li>
    `).join('');
    
    return `
        <nav id="sidebar" class="group fixed left-0 top-16 bottom-0 h-[calc(100vh-4rem)] w-14 hover:w-56 bg-sidebar-blue flex flex-col z-20 transition-all duration-300 ease-in-out overflow-hidden">
            <div class="flex-1 py-2 overflow-y-auto" style="scrollbar-width:none; -ms-overflow-style:none; overflow-y:scroll;">
                <style>.overflow-y-auto::-webkit-scrollbar { display:none!important; width:0!important; height:0!important; background:transparent!important; }</style>
                <ul class="mt-2">
                    <li>
                        <a href="/" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                            <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                                <i class="fa-solid fa-home text-2xl" style="color:#3b82f6;"></i>
                            </span>
                            <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="/favorites" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                            <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                                <i class="fa-solid fa-heart text-2xl" style="color:#ef476f;"></i>
                            </span>
                            <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Favorites</span>
                        </a>
                    </li>
                    <li>
                        <a href="/recently-played" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                            <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                                <i class="fa-solid fa-history text-2xl" style="color:#06d6a0;"></i>
                            </span>
                            <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Recently Played</span>
                        </a>
                    </li>
                </ul>
                <div class="mt-2">
                    <h3 class="px-2 text-xs font-semibold text-gray-300 uppercase tracking-wider opacity-0 group-hover:opacity-100 transition-opacity duration-200">Categories</h3>
                    <ul class="mt-2">
                        ${categoryItems}
                    </ul>
                </div>
                <div class="w-full py-4 flex flex-col items-center justify-center gap-2">
                    <a href="/" class="flex items-center justify-center mb-2">
                        <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;">
                            <img src="/assets/images/icons/logo.png" alt="Sonice Games" class="w-8 h-8 transition-all duration-200" />
                        </span>
                    </a>
                    <div class="flex flex-col items-center w-full opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <span class="mt-2 text-lg font-bold text-white whitespace-nowrap">
                            Sonice<span class="text-blue-400">.Games</span>
                        </span>
                        <p class="text-xs text-gray-200 text-center whitespace-nowrap mb-2">
                            Play the best online games for free. New games added daily!
                        </p>
                        <div class="flex space-x-3 mt-1 mb-2">
                            <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-discord"></i></a>
                        </div>
                        <div class="w-full mt-2">
                            <h3 class="text-base font-semibold mb-2 text-white text-center">Quick Links</h3>
                            <ul class="space-y-1 text-center">
                                <li><a href="/about" class="hover:text-blue-300 text-gray-300">About Us</a></li>
                                <li><a href="/contact" class="hover:text-blue-300 text-gray-300">Contact</a></li>
                                <li><a href="/privacy" class="hover:text-blue-300 text-gray-300">Privacy Policy</a></li>
                                <li><a href="/terms" class="hover:text-blue-300 text-gray-300">Terms of Service</a></li>
                            </ul>
                        </div>
                        <div class="w-full mt-4">
                            <h3 class="text-base font-semibold mb-2 text-white text-center">Newsletter</h3>
                            <p class="text-gray-300 mb-2 text-xs text-center">Subscribe to get updates on new games and features.</p>
                            <form class="flex flex-col space-y-2 items-center">
                                <input type="email" placeholder="Your email address" class="px-3 py-2 rounded bg-white/20 text-white placeholder-gray-300 focus:outline-none focus:bg-white/30 w-full" />
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded w-full">Subscribe</button>
                            </form>
                        </div>
                        <span class="text-xs text-gray-300 mt-4 whitespace-nowrap block text-center">
                            © 2024 Sonice.Games. All rights reserved.
                        </span>
                    </div>
                </div>
            </div>
        </nav>`;
}

// 生成完整的HTML
function generateCompleteHTML(games, categories) {
    const categoriesHTML = categories.map(cat => generateCategoryHTML(cat)).join('');
    const sidebarHTML = generateSidebarHTML(categories);
    
    // 生成游戏数据JavaScript
    const gamesDataJS = categories.map(cat => 
        `window.categoryGames['${cat.slug}'] = [${cat.games.map(game => 
            `{ slug: "${game.slug}", title: "${game.title.replace(/"/g, '\\"')}" }`
        ).join(',')}];`
    ).join('\n');
    
    return `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Sonice Online Games</title>
    <meta name="description" content="Play free online games at Sonice.Games">
    <meta name="keywords" content="online games, free games, browser games, HTML5 games, action games, racing games, puzzle games, strategy games">
    <meta name="author" content="Sonice.Games">
    <meta name="robots" content="index, follow">
    <meta name="language" content="en">
    <meta name="revisit-after" content="7 days">
    <meta name="distribution" content="global">
    <meta name="rating" content="general">
    
    <link rel="canonical" href="https://sonice.games/">
    <link rel="sitemap" type="application/xml" title="Sitemap" href="https://sonice.games/sitemap.xml">
    <link rel="icon" type="image/png" href="/assets/images/icons/logo.png">
    <link rel="apple-touch-icon" href="/assets/images/icons/logo.png">
    
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://sonice.online/">
    <meta property="og:title" content="Home - Sonice Online Games">
    <meta property="og:description" content="Play free online games at Sonice.Games">
    <meta property="og:image" content="https://sonice.online/assets/images/icons/logo.png">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://sonice.online/">
    <meta property="twitter:title" content="Home - Sonice Online Games">
    <meta property="twitter:description" content="Play free online games at Sonice.Games">
    <meta property="twitter:image" content="https://sonice.online/assets/images/icons/logo.png">

    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/auth.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-C6DQJE930Z"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-C6DQJE930Z', {
            'page_title': 'Home',
            'page_location': 'https://sonice.games/',
            'custom_map': {
                'dimension1': 'user_type',
                'dimension2': 'game_category'
            }
        });
        
        function trackGamePlay(gameTitle, gameCategory) {
            gtag('event', 'game_play', {
                'event_category': 'Games',
                'event_label': gameTitle,
                'custom_map': {
                    'dimension2': gameCategory
                }
            });
        }
        
        function trackGameSearch(searchTerm) {
            gtag('event', 'search', {
                'search_term': searchTerm,
                'event_category': 'Search'
            });
        }
        
        function trackCategoryView(categoryName) {
            gtag('event', 'category_view', {
                'event_category': 'Navigation',
                'event_label': categoryName
            });
        }
    </script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'dark': '#0F1012',
                        'dark-lighter': '#1A1B1F',
                        'blue-primary': '#0EA5E9',
                        'blue-secondary': '#38BDF8',
                        'blue-bright': '#7DD3FC',
                        'purple-primary': '#7C3AED',
                        'gray-custom': '#2A2B31',
                        'sidebar-blue': '#152a69',
                        'sidebar-hover': '#1d3a8f'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%) !important;
            color: #ffffff;
        }
        .content-wrapper {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
        .game-card-home { 
            aspect-ratio: 16/9; 
            height: auto; 
            min-height: 100px; 
            background: transparent; 
            border-radius: 12px; 
            overflow: hidden; 
            display: flex; 
            flex-direction: column; 
            align-items: stretch; 
            justify-content: flex-start; 
        }
        .game-card-home img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            border-radius: 8px 8px 0 0; 
            background: #222; 
        }
        .category-block { margin-bottom: 0.75rem; }
        .game-grid { margin-bottom: 0.25rem; }
        .carousel-arrow {
            width: 48px;
            height: 100%;
            min-height: 40px;
            border-radius: 8px;
            background: rgba(30, 64, 175, 0.25);
            border: none;
            display: none;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2rem;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s, opacity 0.2s;
            z-index: 20;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 2px 8px 0 rgba(0,0,0,0.10);
            position: absolute;
            transform: translateY(-50%);
        }
        .carousel-arrow[disabled], .carousel-arrow.disabled {
            opacity: 0.4;
            pointer-events: none;
        }
        .carousel-arrow:hover {
            background: rgba(30, 64, 175, 0.45);
            transform: translateY(-50%) scale(1.08);
        }
        .carousel-arrow-left::before {
            content: '‹';
            font-size: 2rem;
            display: block;
            line-height: 1;
        }
        .carousel-arrow-right::before {
            content: '›';
            font-size: 2rem;
            display: block;
            line-height: 1;
        }
        .category-block:hover .carousel-arrow {
            display: flex !important;
        }
        @media (max-width: 900px) {
            .carousel-arrow { width: 36px; min-height: 32px; }
        }
        #loginModal { display: none; }
        #loginModal.flex { display: flex; }
        #loginModal .bg-white { animation: popIn .2s cubic-bezier(.4,2,.6,1) both; }
        @keyframes popIn { from { transform: scale(.8); opacity:0; } to { transform: scale(1); opacity:1; } }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-dark text-white">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-30 bg-dark-lighter bg-opacity-90 backdrop-blur-sm border-b border-gray-800">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-2">
                <img src="/assets/images/icons/logo.png" alt="Sonice.Games" class="h-10 w-10 rounded-full object-cover">
                <span class="text-2xl font-bold text-white">Sonice<span class="text-blue-500">.Games</span></span>
            </a>
            <!-- Search Bar -->
            <div class="flex-1 max-w-2xl mx-8">
                <div class="relative">
                    <form id="searchForm" action="/search" method="get" class="relative">
                        <input type="search" name="q" id="searchInput" placeholder="Search games..." class="w-full px-5 py-2 bg-[#233a6b] border-none rounded-full text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent shadow-inner">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-blue-500 hover:bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
            <!-- Login/Register Button -->
            <button id="navLoginBtn" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded">Login/Register</button>
        </div>
    </header>
    
    <div class="flex flex-1 min-h-0 pt-16">
        ${sidebarHTML}
        <div id="mainContent" class="flex-1 flex flex-col min-h-0 ml-14 transition-all duration-300">
            <main class="flex-1 gradient-blue">
                <div class="w-full px-0 py-4">
                    <div class="pl-8">
                        ${categoriesHTML}
                    </div>
                </div>
            </main>
            <footer class="bg-dark-lighter bg-opacity-90 backdrop-blur-sm border-t border-gray-800 py-8">
                <div class="container mx-auto px-4 text-center">
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                        <div class="flex items-center space-x-2">
                            <img src="/assets/images/icons/logo.png" alt="Sonice.Games" class="h-8 w-8 rounded-full object-cover">
                            <span class="text-xl font-bold text-white">Sonice<span class="text-blue-500">.Games</span></span>
                        </div>
                        <div class="flex space-x-6">
                            <a href="/about" class="text-gray-300 hover:text-white transition">About Us</a>
                            <a href="/contact" class="text-gray-300 hover:text-white transition">Contact</a>
                            <a href="/privacy" class="text-gray-300 hover:text-white transition">Privacy Policy</a>
                            <a href="/terms" class="text-gray-300 hover:text-white transition">Terms of Service</a>
                        </div>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-300 hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-gray-300 hover:text-white transition"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-gray-300 hover:text-white transition"><i class="fab fa-discord"></i></a>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-700">
                        <p class="text-gray-400 text-sm">&copy; 2024 Sonice.Games. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-60 hidden">
        <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-xs relative">
            <button id="closeLoginModal" class="absolute top-3 right-3 text-gray-400 hover:text-blue-600 text-2xl">&times;</button>
            <h2 class="text-2xl font-bold mb-6 text-center text-blue-700">Login</h2>
            <div id="loginError" class="text-red-500 text-center mb-2 hidden"></div>
            <input id="loginUsername" type="text" placeholder="Username/Email" class="w-full mb-3 p-2 border rounded text-gray-900 focus:ring-2 focus:ring-blue-400" required>
            <input id="loginPassword" type="password" placeholder="Password" class="w-full mb-4 p-2 border rounded text-gray-900 focus:ring-2 focus:ring-blue-400" required>
            <button id="loginSubmit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-semibold transition">Login</button>
        </div>
    </div>
    
    <script>
        window.categoryGames = {};
        ${gamesDataJS}
        
        window.categoryPages = {};
        function renderCategoryPage(slug) {
            const games = window.categoryGames[slug] || [];
            const page = window.categoryPages[slug] || 0;
            const grid = document.getElementById('cat-grid-' + slug);
            const leftBtn = document.querySelector('#cat-block-' + slug + ' .carousel-arrow-left');
            const rightBtn = document.querySelector('#cat-block-' + slug + ' .carousel-arrow-right');
            if (!grid) return;
            grid.innerHTML = '';
            const start = page * 7;
            const end = start + 7;
            const pageGames = games.slice(start, end);
            pageGames.forEach(game => {
                const card = document.createElement('div');
                card.className = 'game-card-home';
                card.innerHTML = \`<a href="\${window.baseUrl || ''}/game/\${game.slug}"><img src="\${window.baseUrl || ''}/assets/images/games/\${game.slug}.webp" alt="\${game.title}" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDIwMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMTIwIiBmaWxsPSIjMjIyIi8+Cjx0ZXh0IHg9IjEwMCIgeT0iNjAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iI2ZmZiIgdGV4dC1hbmNob3I9Im1pZGRsZSI+\${encodeURIComponent(game.title)}</text>
Cjwvc3ZnPg=='"></a>\`;
                grid.appendChild(card);
            });
            if (leftBtn) leftBtn.disabled = !(page > 0);
            if (rightBtn) rightBtn.disabled = !(end < games.length);
        }
        function scrollCategoryPage(slug, dir) {
            const games = window.categoryGames[slug] || [];
            const maxPage = Math.floor((games.length - 1) / 7);
            window.categoryPages[slug] = (window.categoryPages[slug] || 0) + dir;
            if (window.categoryPages[slug] < 0) window.categoryPages[slug] = 0;
            if (window.categoryPages[slug] > maxPage) window.categoryPages[slug] = maxPage;
            renderCategoryPage(slug);
        }
        window.baseUrl = '';
        document.addEventListener('DOMContentLoaded', function() {
            Object.keys(window.categoryGames).forEach(slug => {
                window.categoryPages[slug] = 0;
                renderCategoryPage(slug);
            });
        });
        
        // Modal control
        const loginModal = document.getElementById('loginModal');
        const showLoginModal = () => { loginModal.classList.remove('hidden'); loginModal.classList.add('flex'); };
        const hideLoginModal = () => { loginModal.classList.add('hidden'); loginModal.classList.remove('flex'); };
        document.getElementById('navLoginBtn').onclick = showLoginModal;
        document.getElementById('closeLoginModal').onclick = hideLoginModal;
        loginModal.addEventListener('click', e => { if (e.target === loginModal) hideLoginModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') hideLoginModal(); });
        
        // Login logic
        document.getElementById('loginSubmit').onclick = async function() {
            const username = document.getElementById('loginUsername').value.trim();
            const password = document.getElementById('loginPassword').value;
            const errorDiv = document.getElementById('loginError');
            errorDiv.classList.add('hidden');
            errorDiv.innerText = '';
            if (!username || !password) {
                errorDiv.innerText = 'Please enter username and password';
                errorDiv.classList.remove('hidden');
                return;
            }
            alert('Login functionality not available in static version');
        };
        
        document.getElementById('searchForm').onsubmit = function(e) {
            e.preventDefault();
            const q = document.getElementById('searchInput').value.trim();
            if (q) {
                // Simple search - filter visible games
                const allGames = document.querySelectorAll('.game-card-home');
                allGames.forEach(card => {
                    const title = card.querySelector('img').alt.toLowerCase();
                    if (title.includes(q.toLowerCase())) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            } else {
                // Show all games
                const allGames = document.querySelectorAll('.game-card-home');
                allGames.forEach(card => {
                    card.style.display = 'block';
                });
            }
        };
        
        // Sidebar hover effect
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        if (sidebar && mainContent) {
            sidebar.addEventListener('mouseenter', () => {
                mainContent.classList.remove('ml-14');
                mainContent.classList.add('ml-56');
            });
            sidebar.addEventListener('mouseleave', () => {
                mainContent.classList.remove('ml-56');
                mainContent.classList.add('ml-14');
            });
        }
    </script>
</body>
</html>`;
}

// 主函数
function main() {
    console.log('🚀 开始生成完整的静态网站...');
    
    // 加载游戏数据
    const games = loadGamesFromCSV();
    console.log(`📊 加载了 ${games.length} 个游戏`);
    
    // 组织游戏按分类
    const categories = organizeGamesByCategory(games);
    console.log(`📁 生成了 ${categories.length} 个分类`);
    
    // 生成HTML
    const html = generateCompleteHTML(games, categories);
    
    // 写入文件
    const outputPath = path.join(__dirname, 'public', 'index.html');
    fs.writeFileSync(outputPath, html, 'utf-8');
    
    console.log('✅ 静态网站生成完成！');
    console.log(`📄 输出文件: ${outputPath}`);
    console.log(`🌐 访问地址: https://sonice-onlinegames11.pages.dev/`);
}

// 运行
main();
