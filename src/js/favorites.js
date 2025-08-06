// Favorites Management System
class FavoriteManager {
    constructor() {
        this.favorites = this.loadFavorites();
        this.buttons = [];
        this.isLoggedIn = !!JSON.parse(localStorage.getItem('currentUser'));
        
        // Listen for auth state changes
        document.addEventListener('authStateChanged', () => {
            const wasLoggedIn = this.isLoggedIn;
            this.isLoggedIn = !!JSON.parse(localStorage.getItem('currentUser'));
            
            // If login state changed, reload favorites
            if (wasLoggedIn !== this.isLoggedIn) {
                this.favorites = this.loadFavorites();
                this.updateAllButtonStates();
            }
        });
    }

    loadFavorites() {
        const currentUser = JSON.parse(localStorage.getItem('currentUser'));
        if (currentUser) {
            // User-specific favorites
            const userId = currentUser.id;
            return JSON.parse(localStorage.getItem(`favorites_${userId}`) || '[]');
        } else {
            // Anonymous favorites
            return JSON.parse(localStorage.getItem('favorites') || '[]');
        }
    }

    saveFavorites() {
        const currentUser = JSON.parse(localStorage.getItem('currentUser'));
        if (currentUser) {
            // User-specific favorites
            const userId = currentUser.id;
            localStorage.setItem(`favorites_${userId}`, JSON.stringify(this.favorites));
        } else {
            // Anonymous favorites
            localStorage.setItem('favorites', JSON.stringify(this.favorites));
        }
    }

    initButtons() {
        // Find all favorite buttons on the page
        const favoriteButtons = document.querySelectorAll('.favorite-button');
        
        favoriteButtons.forEach(button => {
            const gameId = button.dataset.gameId;
            if (!gameId) return;
            
            // Store button reference for later updates
            this.buttons.push(button);
            
            // Set initial state
            this.updateButtonState(button, this.isGameFavorite(gameId));
            
            // Add click event
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleFavorite(gameId, button);
            });
        });
    }

    toggleFavorite(gameId, button) {
        // If not logged in and this requires authentication, show login modal
        if (!this.isLoggedIn && this.requiresAuthentication()) {
            this.showLoginPrompt();
            return;
        }
        
        const isFavorite = this.isGameFavorite(gameId);
        
        if (isFavorite) {
            // Remove from favorites
            this.favorites = this.favorites.filter(id => id !== gameId);
            this.showToast(`Game removed from favorites`, 'info');
        } else {
            // Add to favorites with animation
            this.favorites.push(gameId);
            this.showToast(`Game added to favorites`, 'success');
            this.playFavoriteAnimation(button);
        }
        
        // Update button state
        this.updateButtonState(button, !isFavorite);
        
        // Save to localStorage
        this.saveFavorites();
        
        // Update all buttons with the same gameId
        this.updateButtonsForGame(gameId, !isFavorite);
    }

    updateButtonState(button, isFavorite) {
        if (isFavorite) {
            button.classList.add('favorite-active');
            button.innerHTML = `<i class="fas fa-heart"></i>`;
            button.setAttribute('title', 'Remove from favorites');
        } else {
            button.classList.remove('favorite-active');
            button.innerHTML = `<i class="far fa-heart"></i>`;
            button.setAttribute('title', 'Add to favorites');
        }
    }
    
    updateButtonsForGame(gameId, isFavorite) {
        // Update all buttons with the same gameId
        this.buttons.forEach(button => {
            if (button.dataset.gameId === gameId) {
                this.updateButtonState(button, isFavorite);
            }
        });
    }
    
    updateAllButtonStates() {
        // Update all buttons based on current favorites
        this.buttons.forEach(button => {
            const gameId = button.dataset.gameId;
            if (gameId) {
                this.updateButtonState(button, this.isGameFavorite(gameId));
            }
        });
    }

    isGameFavorite(gameId) {
        return this.favorites.includes(gameId);
    }
    
    requiresAuthentication() {
        // Configuration: whether favorites require authentication
        // This can be set to true if you want to force login for favorites
        return false;
    }
    
    showLoginPrompt() {
        // Show login prompt using the auth system
        if (typeof auth !== 'undefined') {
            auth.showAuthModal();
            auth.showToast('Please sign in to add games to favorites', 'info');
        } else {
            this.showToast('Please sign in to add games to favorites', 'info');
        }
    }
    
    playFavoriteAnimation(button) {
        // Create and play heart animation
        const heart = document.createElement('div');
        heart.className = 'favorite-animation';
        heart.innerHTML = '<i class="fas fa-heart"></i>';
        
        // Position the heart at the button position
        const rect = button.getBoundingClientRect();
        heart.style.left = `${rect.left + rect.width/2}px`;
        heart.style.top = `${rect.top + rect.height/2}px`;
        
        // Add to body
        document.body.appendChild(heart);
        
        // Remove after animation completes
        setTimeout(() => {
            heart.remove();
        }, 1000);
    }

    showToast(message, type = 'success') {
        // Use existing toast if available
        const toastElement = document.getElementById('toast-notification');
        
        if (toastElement) {
            // Use existing toast element
            toastElement.textContent = message;
            toastElement.className = `toast ${type}`;
            toastElement.classList.add('show');
            
            setTimeout(() => {
                toastElement.classList.remove('show');
            }, 3000);
        } else {
            // Create new toast element
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                type === 'info' ? 'bg-blue-500' : 'bg-green-500'
            } transition-opacity duration-300`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    }
    
    getFavoriteCount() {
        return this.favorites.length;
    }
    
    renderFavoritesList(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        if (this.favorites.length === 0) {
            container.innerHTML = `
                <div class="text-center py-10">
                    <i class="far fa-heart text-4xl mb-2 text-gray-400"></i>
                    <p class="text-gray-400">You haven't added any favorites yet</p>
                    <a href="/games.html" class="mt-4 inline-block px-4 py-2 bg-purple-primary text-white rounded hover:bg-purple-600 transition-colors">
                        Browse Games
                    </a>
                </div>
            `;
            return;
        }
        
        // Clear container
        container.innerHTML = '';
        
        // Get games data (simulated - in a real app, you'd fetch this from an API)
        this.getFavoritesData().then(games => {
            games.forEach(game => {
                const gameCard = document.createElement('div');
                gameCard.className = 'bg-dark-lighter rounded-lg overflow-hidden transition-transform hover:scale-105';
                gameCard.innerHTML = `
                    <a href="${game.url}" class="block">
                        <img src="${game.image}" alt="${game.title}" class="w-full h-40 object-cover">
                        <div class="p-4">
                            <div class="flex justify-between items-start">
                                <h3 class="text-white font-medium text-lg">${game.title}</h3>
                                <button class="favorite-button favorite-active" data-game-id="${game.id}" title="Remove from favorites">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                            <p class="text-gray-400 text-sm mt-1">${game.category}</p>
                        </div>
                    </a>
                `;
                
                container.appendChild(gameCard);
                
                // Initialize the favorite button
                const favoriteButton = gameCard.querySelector('.favorite-button');
                favoriteButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.toggleFavorite(game.id, favoriteButton);
                    
                    // If on favorites page, remove card with animation
                    if (window.location.pathname.includes('favorites')) {
                        gameCard.style.opacity = '0';
                        gameCard.style.transform = 'scale(0.8)';
                        setTimeout(() => {
                            gameCard.remove();
                            // Check if empty after removal
                            if (container.children.length === 0) {
                                this.renderFavoritesList(containerId);
                            }
                        }, 300);
                    }
                });
            });
        });
    }
    
    async getFavoritesData() {
        // In a real app, you would fetch this data from your API
        // For demonstration, we're simulating it with local data
        
        // Simulate API call delay
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Mock data
        const allGames = {
            'game1': {
                id: 'game1',
                title: 'Epic Adventure',
                image: '/assets/images/games/game1.jpg',
                category: 'Adventure',
                url: '/game-details.html?id=game1'
            },
            'game2': {
                id: 'game2',
                title: 'Space Shooter',
                image: '/assets/images/games/game2.jpg',
                category: 'Action',
                url: '/game-details.html?id=game2'
            },
            'game3': {
                id: 'game3',
                title: 'Puzzle Master',
                image: '/assets/images/games/game3.jpg',
                category: 'Puzzle',
                url: '/game-details.html?id=game3'
            },
            'game4': {
                id: 'game4',
                title: 'Racing Pro',
                image: '/assets/images/games/game4.jpg',
                category: 'Racing',
                url: '/game-details.html?id=game4'
            },
            'game5': {
                id: 'game5',
                title: 'Fantasy RPG',
                image: '/assets/images/games/game5.jpg',
                category: 'RPG',
                url: '/game-details.html?id=game5'
            }
        };
        
        // Filter to only show favorites
        return this.favorites
            .map(id => allGames[id])
            .filter(game => game !== undefined);
    }
    
    // Add method to migrate anonymous favorites to user favorites upon login
    migrateAnonymousFavorites() {
        const currentUser = JSON.parse(localStorage.getItem('currentUser'));
        if (!currentUser) return;
        
        const userId = currentUser.id;
        const anonymousFavorites = JSON.parse(localStorage.getItem('favorites') || '[]');
        
        if (anonymousFavorites.length === 0) return;
        
        // Get user favorites
        const userFavorites = JSON.parse(localStorage.getItem(`favorites_${userId}`) || '[]');
        
        // Merge favorites (remove duplicates)
        const mergedFavorites = [...new Set([...userFavorites, ...anonymousFavorites])];
        
        // Save merged favorites
        localStorage.setItem(`favorites_${userId}`, JSON.stringify(mergedFavorites));
        
        // Update current favorites
        this.favorites = mergedFavorites;
        
        // Clear anonymous favorites
        localStorage.removeItem('favorites');
        
        // Update buttons
        this.updateAllButtonStates();
        
        if (anonymousFavorites.length > 0) {
            this.showToast(`${anonymousFavorites.length} favorites synced to your account`, 'info');
        }
    }
}

// Create global instance
const favoriteManager = new FavoriteManager();

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    favoriteManager.initButtons();
    
    // Check if we need to render favorites list
    const favoritesContainer = document.getElementById('favorites-container');
    if (favoritesContainer) {
        favoriteManager.renderFavoritesList('favorites-container');
    }
    
    // If user is logged in, migrate anonymous favorites
    if (favoriteManager.isLoggedIn) {
        favoriteManager.migrateAnonymousFavorites();
    }
    
    // Add favorites count to nav if element exists
    const favoritesCount = document.getElementById('favorites-count');
    if (favoritesCount) {
        const count = favoriteManager.getFavoriteCount();
        favoritesCount.textContent = count;
        favoritesCount.style.display = count > 0 ? 'flex' : 'none';
    }
});

// Add styles for favorite animation
document.addEventListener('DOMContentLoaded', () => {
    const style = document.createElement('style');
    style.textContent = `
        .favorite-button {
            color: #6c757d;
            background: none;
            border: none;
            cursor: pointer;
            transition: transform 0.3s, color 0.3s;
            font-size: 1.2rem;
            padding: 0.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .favorite-button:hover {
            transform: scale(1.2);
        }
        
        .favorite-button.favorite-active {
            color: #e91e63;
        }
        
        .favorite-animation {
            position: fixed;
            z-index: 1000;
            font-size: 1rem;
            color: #e91e63;
            pointer-events: none;
            animation: favorite-float 1s ease-out forwards;
            transform: translate(-50%, -50%);
        }
        
        @keyframes favorite-float {
            0% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -120%) scale(2);
            }
        }
        
        #favorites-count {
            background-color: #e91e63;
            color: white;
            font-size: 0.7rem;
            width: 1.2rem;
            height: 1.2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: -0.4rem;
            right: -0.4rem;
        }
    `;
    document.head.appendChild(style);
}); 