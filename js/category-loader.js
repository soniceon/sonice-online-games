// Category Loader Script for Sonice.online
// This script loads category data from JSON and handles category page dynamics

// Cache for category data to avoid multiple fetches
let categoryData = null;

/**
 * Fetches category data from the JSON file
 * @returns {Promise<Object>} The category data
 */
async function fetchCategoryData() {
  if (categoryData) {
    return categoryData;
  }
  
  try {
    const response = await fetch('/data/category-data.json');
    if (!response.ok) {
      throw new Error('Failed to fetch category data');
    }
    
    categoryData = await response.json();
    return categoryData;
  } catch (error) {
    console.error('Error fetching category data:', error);
    return { categories: [] };
  }
}

/**
 * Gets category data for a specific category
 * @param {string} categorySlug - The slug of the category to get
 * @returns {Promise<Object|null>} The category data or null if not found
 */
async function getCategoryBySlug(categorySlug) {
  const data = await fetchCategoryData();
  return data.categories.find(category => category.slug === categorySlug) || null;
}

/**
 * Sets up the current page with category data
 * @param {string} categorySlug - The slug of the current category
 */
async function setupCategoryPage(categorySlug) {
  const category = await getCategoryBySlug(categorySlug);
  
  if (!category) {
    console.error(`Category not found: ${categorySlug}`);
    return;
  }
  
  // Update page title and metadata
  document.title = `${category.name} Games - Sonice Online`;
  
  // Update meta descriptions
  const metaDesc = document.querySelector('meta[name="description"]');
  if (metaDesc) metaDesc.setAttribute('content', category.metaDescription);
  
  // Update Open Graph metadata
  const ogTitle = document.querySelector('meta[property="og:title"]');
  if (ogTitle) ogTitle.setAttribute('content', `${category.name} Games - Sonice Online`);
  
  const ogDesc = document.querySelector('meta[property="og:description"]');
  if (ogDesc) ogDesc.setAttribute('content', category.ogDescription);
  
  const ogUrl = document.querySelector('meta[property="og:url"]');
  if (ogUrl) ogUrl.setAttribute('content', `https://sonice.online/categories/${category.slug}.html`);
  
  // Update canonical link
  const canonicalLink = document.querySelector('link[rel="canonical"]');
  if (canonicalLink) canonicalLink.setAttribute('href', `https://sonice.online/categories/${category.slug}.html`);
  
  // Update page content
  const categoryTitle = document.getElementById('category-title');
  if (categoryTitle) categoryTitle.textContent = `${category.icon} ${category.name} Games`;
  
  const categoryDescription = document.getElementById('category-description');
  if (categoryDescription) categoryDescription.textContent = category.description;
  
  // Apply category color if needed
  applyColorTheme(category.color);
}

/**
 * Apply color theme based on category
 * @param {string} colorName - The name of the color to apply
 */
function applyColorTheme(colorName) {
  // Remove all color theme classes from the body
  document.body.classList.remove(
    'theme-red', 'theme-blue', 'theme-green', 'theme-purple', 
    'theme-orange', 'theme-teal', 'theme-indigo', 'theme-pink', 'theme-yellow'
  );
  
  // Add the appropriate theme class
  document.body.classList.add(`theme-${colorName}`);
}

/**
 * Loads games for a specific category from games-enhanced.json
 * @param {string} categorySlug - The slug of the category to load games for
 * @param {number} limit - Optional limit for the number of games to display
 */
async function loadCategoryGames(categorySlug, limit = 0) {
  try {
    const gamesContainer = document.getElementById('games-container');
    const loadingElement = document.getElementById('games-loading');
    const noGamesElement = document.getElementById('no-games-message');
    
    if (!gamesContainer) {
      console.error('Games container element not found');
      return;
    }
    
    // Show loading state
    if (loadingElement) loadingElement.style.display = 'flex';
    if (noGamesElement) noGamesElement.style.display = 'none';
    
    // Fetch games data
    const response = await fetch('/data/games-enhanced.json');
    
    if (!response.ok) {
      throw new Error('Failed to fetch games data');
    }
    
    const data = await response.json();
    
    // Filter games by category
    let categoryGames = data.games.filter(game => {
      // Check if the game has this category in its categories array
      if (game.categories && Array.isArray(game.categories)) {
        return game.categories.includes(categorySlug);
      }
      
      // Fallback to check if the category appears in the title or tags
      const title = (game.title || '').toLowerCase();
      const tags = Array.isArray(game.tags) ? game.tags.join(' ').toLowerCase() : '';
      const categoryName = categorySlug.toLowerCase();
      
      return title.includes(categoryName) || tags.includes(categoryName);
    });
    
    // Apply limit if specified and greater than zero
    if (limit > 0 && categoryGames.length > limit) {
      categoryGames = categoryGames.slice(0, limit);
    }
    
    // Hide loading state
    if (loadingElement) loadingElement.style.display = 'none';
    
    // Show no games message if no games found
    if (categoryGames.length === 0) {
      if (noGamesElement) noGamesElement.style.display = 'block';
      return;
    }
    
    // Clear the container
    gamesContainer.innerHTML = '';
    
    // Populate games
    categoryGames.forEach(game => {
      const gameElement = createGameElement(game);
      gamesContainer.appendChild(gameElement);
    });
    
  } catch (error) {
    console.error('Error loading category games:', error);
    
    // Hide loading state and show error message
    const loadingElement = document.getElementById('games-loading');
    if (loadingElement) loadingElement.style.display = 'none';
    
    const noGamesElement = document.getElementById('no-games-message');
    if (noGamesElement) {
      noGamesElement.textContent = 'Failed to load games. Please try again later.';
      noGamesElement.style.display = 'block';
    }
  }
}

/**
 * Creates a game element for the grid
 * @param {Object} game - The game data
 * @returns {HTMLElement} The game element
 */
function createGameElement(game) {
  const gameCard = document.createElement('div');
  gameCard.className = 'bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:shadow-lg hover:-translate-y-1';
  
  // Generate stars HTML for rating
  const rating = game.rating || Math.floor(Math.random() * 5) + 1;
  let starsHtml = '';
  
  for (let i = 1; i <= 5; i++) {
    if (i <= rating) {
      starsHtml += '<svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
    } else {
      starsHtml += '<svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
    }
  }
  
  const gamePath = game.path || `/games/${game.id || game.slug || 'game'}.html`;
  
  gameCard.innerHTML = `
    <a href="${gamePath}" class="block">
      <img src="${game.thumbnail || game.image || '/assets/images/games/default-game.webp'}" 
           alt="${game.title}" 
           class="w-full h-40 object-cover"
           onerror="this.onerror=null; this.src='/assets/images/games/default-game.webp';">
      <div class="p-4">
        <h3 class="text-lg font-bold text-gray-800 mb-2">${game.title}</h3>
        <p class="text-gray-600 text-sm mb-3 line-clamp-2">${game.description || 'An exciting online game. Play now for free!'}</p>
        <div class="flex items-center">
          <div class="flex">
            ${starsHtml}
          </div>
          <span class="text-gray-500 text-sm ml-2">(${game.reviews || Math.floor(Math.random() * 1000) + 10})</span>
        </div>
      </div>
    </a>
  `;
  
  return gameCard;
}

/**
 * Initialize the category navigation menu
 */
async function initCategoryNav() {
  const categoryNav = document.getElementById('category-nav');
  if (!categoryNav) return;
  
  const data = await fetchCategoryData();
  
  // Populate category navigation
  data.categories.forEach(category => {
    const categoryItem = document.createElement('a');
    categoryItem.href = `/categories/${category.slug}.html`;
    categoryItem.className = 'flex items-center py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors';
    categoryItem.innerHTML = `
      <span class="mr-2">${category.icon}</span>
      <span>${category.name}</span>
    `;
    
    // Add active class if we're on this category's page
    if (window.location.pathname.includes(`/categories/${category.slug}.html`)) {
      categoryItem.classList.add('bg-gray-100', 'font-medium');
    }
    
    categoryNav.appendChild(categoryItem);
  });
}

// Export functions for use in other scripts
window.CategoryLoader = {
  fetchCategoryData,
  getCategoryBySlug,
  setupCategoryPage,
  loadCategoryGames,
  initCategoryNav
}; 