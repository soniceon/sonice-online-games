// Games loader utility for sonice.online
// This script handles fetching and displaying games across the site

const GAMES_DATA_URL = '/data/games-enhanced.json';
const CATEGORIES_DATA_URL = '/data/category-data.json';

// Main function to load all games for the homepage
async function loadAllGames(targetElementId = 'games-grid', limit = 12) {
  const targetElement = document.getElementById(targetElementId);
  if (!targetElement) return;
  
  try {
    const response = await fetch(GAMES_DATA_URL);
    if (!response.ok) throw new Error('Failed to fetch games data');
    
    const data = await response.json();
    const games = data.games.slice(0, limit);
    
    if (games.length === 0) {
      targetElement.innerHTML = '<p class="text-center col-span-full py-8">No games found. Check back later!</p>';
      return;
    }
    
    renderGames(games, targetElement);
  } catch (error) {
    console.error('Error loading games:', error);
    targetElement.innerHTML = renderStaticGameExamples();
  }
}

// Function to load games for a specific category
async function loadCategoryGames(category, targetElementId = 'category-games-grid', limit = 12) {
  const targetElement = document.getElementById(targetElementId);
  if (!targetElement) return;
  
  try {
    const response = await fetch(GAMES_DATA_URL);
    if (!response.ok) throw new Error('Failed to fetch games data');
    
    const data = await response.json();
    
    // Try to find games by category ID first
    let categoryGames = data.games.filter(game => 
      game.categories && game.categories.includes(category)
    );
    
    // If no games found by category ID, try to find games by tags
    if (categoryGames.length === 0) {
      categoryGames = data.games.filter(game => 
        game.tags && game.tags.some(tag => tag.toLowerCase() === category.toLowerCase())
      );
    }
    
    // If still no games found, try to find games by title match
    if (categoryGames.length === 0) {
      categoryGames = data.games.filter(game => 
        game.title.toLowerCase().includes(category.toLowerCase())
      );
    }
    
    // Limit the number of games shown
    const limitedGames = categoryGames.slice(0, limit);
    
    if (limitedGames.length === 0) {
      targetElement.innerHTML = '<p class="text-center col-span-full py-8">No games found in this category. Check back later!</p>';
      // Provide static examples as fallback
      targetElement.innerHTML = renderStaticGameExamples(category);
      return;
    }
    
    renderGames(limitedGames, targetElement);
  } catch (error) {
    console.error(`Error loading ${category} games:`, error);
    targetElement.innerHTML = renderStaticGameExamples(category);
  }
}

// Function to load featured games for the homepage
async function loadFeaturedGames(targetElementId = 'featured-games', limit = 4) {
  const targetElement = document.getElementById(targetElementId);
  if (!targetElement) return;
  
  try {
    const response = await fetch(GAMES_DATA_URL);
    if (!response.ok) throw new Error('Failed to fetch games data');
    
    const data = await response.json();
    // Get games with highest ratings
    const sortedGames = [...data.games].sort((a, b) => b.rating - a.rating);
    const featuredGames = sortedGames.slice(0, limit);
    
    if (featuredGames.length === 0) {
      targetElement.innerHTML = '<p class="text-center w-full py-8">No featured games available. Check back later!</p>';
      return;
    }
    
    renderFeaturedGames(featuredGames, targetElement);
  } catch (error) {
    console.error('Error loading featured games:', error);
    targetElement.innerHTML = renderStaticFeaturedGames();
  }
}

// Function to load all categories for the category navigation
async function loadCategories(targetElementId = 'categories-list') {
  const targetElement = document.getElementById(targetElementId);
  if (!targetElement) return;
  
  try {
    const response = await fetch(CATEGORIES_DATA_URL);
    if (!response.ok) throw new Error('Failed to fetch categories data');
    
    const data = await response.json();
    const categories = data.categories;
    
    if (categories.length === 0) {
      targetElement.innerHTML = '<p class="text-center w-full py-4">No categories available</p>';
      return;
    }
    
    renderCategories(categories, targetElement);
  } catch (error) {
    console.error('Error loading categories:', error);
    targetElement.innerHTML = renderStaticCategories();
  }
}

// Helper function to render games in a grid
function renderGames(games, targetElement) {
  targetElement.innerHTML = games.map(game => `
    <div class="game-card bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:scale-105">
      <a href="${game.path || `/games/${game.id || game.slug}.html`}" class="block">
        <img src="${game.thumbnail || game.image || '/assets/images/games/default-game.webp'}" alt="${game.title}" 
          class="w-full h-40 object-cover" loading="lazy"
          onerror="this.onerror=null; this.src='/assets/images/default-game.webp';">
        <div class="p-4">
          <h3 class="text-lg font-semibold text-gray-800 mb-1">${game.title}</h3>
          <p class="text-sm text-gray-600 mb-2 line-clamp-2">${game.description || 'No description available'}</p>
          <div class="flex items-center">
            <div class="flex">
              ${renderRatingStars(game.rating || 4.5)}
            </div>
            <span class="text-xs text-gray-500 ml-2">(${game.reviews || Math.floor(Math.random() * 100) + 10})</span>
          </div>
        </div>
      </a>
    </div>
  `).join('');
}

// Helper function to render featured games in a special format
function renderFeaturedGames(games, targetElement) {
  targetElement.innerHTML = games.map(game => `
    <div class="featured-game-card relative overflow-hidden rounded-lg shadow-lg">
      <a href="${game.path || `/games/${game.id || game.slug}.html`}" class="block">
        <img src="${game.image || game.thumbnail || '/assets/images/games/default-game.webp'}" alt="${game.title}" 
          class="w-full h-60 object-cover" loading="lazy"
          onerror="this.onerror=null; this.src='/assets/images/featured-placeholder.jpg';">
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
          <h3 class="text-xl font-bold text-white mb-1">${game.title}</h3>
          <div class="flex items-center mb-2">
            <div class="flex text-yellow-400">
              ${renderRatingStars(game.rating || 4.8)}
            </div>
            <span class="text-xs text-gray-300 ml-2">(${game.reviews || Math.floor(Math.random() * 500) + 100})</span>
          </div>
          <span class="inline-block bg-apple-red px-2 py-1 rounded text-xs text-white">Featured</span>
        </div>
      </a>
    </div>
  `).join('');
}

// Helper function to render categories
function renderCategories(categories, targetElement) {
  targetElement.innerHTML = categories.map(category => `
    <div class="category-card">
      <a href="${category.path}" class="flex flex-col items-center p-4 ${category.color} ${category.textColor} rounded-lg transition-transform hover:scale-105">
        <span class="text-3xl mb-2">${category.icon}</span>
        <h3 class="text-sm font-medium">${category.name}</h3>
        <span class="text-xs mt-1">${category.totalGames} games</span>
      </a>
    </div>
  `).join('');
}

// Helper function to render rating stars
function renderRatingStars(rating) {
  const fullStars = Math.floor(rating);
  const halfStar = rating % 1 >= 0.5;
  const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
  
  let starsHTML = '';
  
  // Full stars
  for (let i = 0; i < fullStars; i++) {
    starsHTML += '<svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
  }
  
  // Half star
  if (halfStar) {
    starsHTML += '<svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" clip-path="inset(0 50% 0 0)"></path></svg>';
  }
  
  // Empty stars
  for (let i = 0; i < emptyStars; i++) {
    starsHTML += '<svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
  }
  
  return starsHTML;
}

// Fallback content for when API fails - static examples
function renderStaticGameExamples(category = '') {
  // Customize examples based on category
  let gameExamples = [];
  
  switch(category.toLowerCase()) {
    case 'action':
      gameExamples = [
        {title: 'Super Hero Run', description: 'Run, jump, and fight your way through epic battles.', rating: 4.7, reviews: 128},
        {title: 'Ninja Warrior', description: 'Test your ninja skills in this fast-paced action game.', rating: 4.5, reviews: 96},
        {title: 'Robot Rampage', description: 'Control a giant robot and destroy everything in your path.', rating: 4.3, reviews: 85}
      ];
      break;
    case 'adventure':
      gameExamples = [
        {title: 'Lost Island', description: 'Explore a mysterious island filled with puzzles and treasures.', rating: 4.8, reviews: 156},
        {title: 'Medieval Quest', description: 'Embark on an epic journey through a fantasy medieval world.', rating: 4.6, reviews: 112},
        {title: 'Underwater Explorer', description: 'Dive deep and discover hidden secrets beneath the ocean.', rating: 4.4, reviews: 78}
      ];
      break;
    case 'shooter':
      gameExamples = [
        {title: 'Space Defender', description: 'Protect your space station from waves of alien invaders.', rating: 4.7, reviews: 145},
        {title: 'Zombie Shooter', description: 'Survive the zombie apocalypse with an arsenal of weapons.', rating: 4.6, reviews: 132},
        {title: 'Robot Wars', description: 'Battle against rogue robots in this futuristic shooter.', rating: 4.5, reviews: 98}
      ];
      break;
    case 'racing':
      gameExamples = [
        {title: 'Speed Racer', description: 'Race through challenging tracks at breakneck speeds.', rating: 4.6, reviews: 120},
        {title: 'Off-Road Challenge', description: 'Conquer difficult terrain in powerful off-road vehicles.', rating: 4.5, reviews: 95},
        {title: 'Drift King', description: 'Master the art of drifting in this stylish racing game.', rating: 4.4, reviews: 88}
      ];
      break;
    case 'puzzle':
      gameExamples = [
        {title: 'Brain Teaser', description: 'Challenge your mind with increasingly difficult puzzles.', rating: 4.8, reviews: 175},
        {title: 'Color Match', description: 'Match colors in this addictive and relaxing puzzle game.', rating: 4.7, reviews: 143},
        {title: 'Logic Master', description: 'Solve complex logic puzzles that will test your reasoning.', rating: 4.6, reviews: 129}
      ];
      break;
    case 'sports':
      gameExamples = [
        {title: 'Soccer Stars', description: 'Play soccer with simple controls and strategic gameplay.', rating: 4.8, reviews: 168},
        {title: 'Basketball Pro', description: 'Test your basketball skills with precision shooting.', rating: 4.7, reviews: 152},
        {title: 'Tennis Challenge', description: 'Compete in tennis tournaments against skilled opponents.', rating: 4.6, reviews: 124}
      ];
      break;
    case 'strategy':
      gameExamples = [
        {title: 'Tower Defense Master', description: 'Build defenses to protect against waves of enemies.', rating: 4.8, reviews: 194},
        {title: 'Empire Builder', description: 'Construct and expand your empire in this strategy game.', rating: 4.7, reviews: 167},
        {title: 'Battle Commander', description: 'Lead your troops to victory through tactical combat.', rating: 4.6, reviews: 148}
      ];
      break;
    case 'cards':
      gameExamples = [
        {title: 'Poker Master', description: 'Test your poker skills against challenging AI opponents.', rating: 4.7, reviews: 156},
        {title: 'Solitaire Classic', description: 'The timeless card game with beautiful graphics.', rating: 4.6, reviews: 142},
        {title: 'Card Battle Arena', description: 'Collect cards and battle in this strategic card game.', rating: 4.5, reviews: 118}
      ];
      break;
    default:
      gameExamples = [
        {title: 'Adventure Quest', description: 'Embark on an epic journey filled with challenges.', rating: 4.8, reviews: 187},
        {title: 'Space Shooter', description: 'Defend your galaxy from alien invaders.', rating: 4.7, reviews: 165},
        {title: 'Puzzle Master', description: 'Test your brain with challenging puzzles.', rating: 4.6, reviews: 142},
        {title: 'Racing Fever', description: 'Race against opponents on thrilling tracks.', rating: 4.5, reviews: 118},
        {title: 'Tower Defense', description: 'Defend your base against waves of enemies.', rating: 4.7, reviews: 156},
        {title: 'Card Battles', description: 'Collect cards and battle your opponents.', rating: 4.5, reviews: 124}
      ];
  }
  
  return gameExamples.map(game => `
    <div class="game-card bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:scale-105">
      <a href="#" class="block">
        <img src="/assets/images/games/placeholder-${game.title.toLowerCase().replace(/\s+/g, '-')}.jpg" 
             alt="${game.title}" 
             class="w-full h-40 object-cover" 
             onerror="this.src='/assets/images/games/default-game.webp'">
        <div class="p-4">
          <h3 class="text-lg font-semibold text-gray-800 mb-1">${game.title}</h3>
          <p class="text-sm text-gray-600 mb-2 line-clamp-2">${game.description}</p>
          <div class="flex items-center">
            <div class="flex">
              ${renderRatingStars(game.rating)}
            </div>
            <span class="text-xs text-gray-500 ml-2">(${game.reviews})</span>
          </div>
        </div>
      </a>
    </div>
  `).join('');
}

// Fallback static featured games
function renderStaticFeaturedGames() {
  const featuredGames = [
    {title: 'Epic Adventure', description: 'Embark on a journey like no other', rating: 4.9, reviews: 352},
    {title: 'Space Commander', description: 'Command your fleet in epic space battles', rating: 4.8, reviews: 287},
    {title: 'Racing Legends', description: 'Race against the best drivers in the world', rating: 4.7, reviews: 264},
    {title: 'Puzzle Master', description: 'Challenge your brain with the toughest puzzles', rating: 4.8, reviews: 298}
  ];
  
  return featuredGames.map(game => `
    <div class="featured-game-card relative overflow-hidden rounded-lg shadow-lg">
      <a href="#" class="block">
        <img src="/assets/images/games/featured-${game.title.toLowerCase().replace(/\s+/g, '-')}.jpg" 
             alt="${game.title}" 
             class="w-full h-60 object-cover"
             onerror="this.src='/assets/images/games/default-game.webp'">
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
          <h3 class="text-xl font-bold text-white mb-1">${game.title}</h3>
          <div class="flex items-center mb-2">
            <div class="flex text-yellow-400">
              ${renderRatingStars(game.rating)}
            </div>
            <span class="text-xs text-gray-300 ml-2">(${game.reviews})</span>
          </div>
          <span class="inline-block bg-apple-red px-2 py-1 rounded text-xs text-white">Featured</span>
        </div>
      </a>
    </div>
  `).join('');
}

// Fallback static categories
function renderStaticCategories() {
  const categories = [
    {name: 'Action Games', icon: '🎮', color: 'bg-apple-red', textColor: 'text-white', totalGames: 12, path: '/categories/action.html'},
    {name: 'Adventure Games', icon: '🗺️', color: 'bg-apple-purple', textColor: 'text-white', totalGames: 10, path: '/categories/adventure.html'},
    {name: 'Puzzle Games', icon: '🧩', color: 'bg-apple-blue', textColor: 'text-white', totalGames: 15, path: '/categories/puzzle.html'},
    {name: 'Racing Games', icon: '🏎️', color: 'bg-apple-orange', textColor: 'text-white', totalGames: 8, path: '/categories/racing.html'},
    {name: 'Shooter Games', icon: '🔫', color: 'bg-apple-red', textColor: 'text-white', totalGames: 9, path: '/categories/shooter.html'},
    {name: 'Sports Games', icon: '⚽', color: 'bg-apple-green', textColor: 'text-white', totalGames: 7, path: '/categories/sports.html'}
  ];
  
  return categories.map(category => `
    <div class="category-card">
      <a href="${category.path}" class="flex flex-col items-center p-4 ${category.color} ${category.textColor} rounded-lg transition-transform hover:scale-105">
        <span class="text-3xl mb-2">${category.icon}</span>
        <h3 class="text-sm font-medium">${category.name}</h3>
        <span class="text-xs mt-1">${category.totalGames} games</span>
      </a>
    </div>
  `).join('');
}

// Export the functions for use in other scripts
window.loadAllGames = loadAllGames;
window.loadCategoryGames = loadCategoryGames;
window.loadFeaturedGames = loadFeaturedGames;
window.loadCategories = loadCategories;

function createGameElement(game) {
    const gameCard = document.createElement('div');
    gameCard.className = 'game-card';
    
    // Add category classes for filtering
    if (game.category) {
        gameCard.classList.add(`category-${game.category.toLowerCase()}`);
    }
    
    gameCard.innerHTML = `
        <img src="${game.imageUrl || '/images/placeholder.jpg'}" alt="${game.title}" class="game-image">
        <div class="game-info">
            <h3 class="game-title">${game.title}</h3>
            <p class="game-description">${game.description}</p>
            <div class="game-actions">
                <a href="/games/${game.slug || game.title.toLowerCase().replace(/[^a-z0-9]+/g, '-')}" class="play-button">Play Now</a>
                <a href="/pages/games/${game.slug || game.title.toLowerCase().replace(/[^a-z0-9]+/g, '-')}.html" class="details-button">View Details</a>
            </div>
        </div>
    `;
    
    return gameCard;
} 