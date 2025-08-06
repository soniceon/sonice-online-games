// DOM Elements
const userMenuButton = document.querySelector('[data-dropdown-toggle="userMenu"]');
const userMenu = document.getElementById('userMenu');
const searchInput = document.querySelector('input[type="search"]');
const searchSuggestions = document.getElementById('searchSuggestions');

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
        if (userMenu && !userMenuButton?.contains(e.target) && !userMenu.contains(e.target)) {
            userMenu.classList.add('hidden');
        }
    });

    // Search input handler
    if (searchInput) {
        searchInput.addEventListener('input', debounce(handleSearch, 300));
        searchInput.addEventListener('focus', () => {
            if (searchSuggestions && searchInput.value.length > 0) {
                searchSuggestions.classList.remove('hidden');
            }
        });
    }
});

// Search handler
async function handleSearch(e) {
    const query = e.target.value.trim();
    
    if (query.length < 2) {
        if (searchSuggestions) {
            searchSuggestions.classList.add('hidden');
        }
        return;
    }
    
    try {
        const response = await fetch(`/api/search.php?q=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        if (data.success && searchSuggestions) {
            // Update suggestions UI
            searchSuggestions.innerHTML = data.results
                .map(game => `
                    <a href="/game/${game.slug}" class="block px-4 py-2 hover:bg-gray-700">
                        <div class="flex items-center">
                            <img src="${game.thumbnail}" alt="${game.title}" class="w-10 h-10 rounded">
                            <div class="ml-3">
                                <div class="text-sm font-medium">${game.title}</div>
                                <div class="text-xs text-gray-400">${game.category}</div>
                            </div>
                        </div>
                    </a>
                `)
                .join('');
            
            searchSuggestions.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Search error:', error);
    }
}

// Utility function: Debounce
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Favorite game handler
async function toggleFavorite(gameId) {
    try {
        const response = await fetch('/api/favorites.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ game_id: gameId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            const button = document.querySelector(`[data-favorite-id="${gameId}"]`);
            if (button) {
                button.classList.toggle('text-red-500');
                button.classList.toggle('text-gray-400');
            }
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Favorite toggle error:', error);
        showToast('An error occurred while updating favorites', 'error');
    }
}

// Toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white ${
        type === 'error' ? 'bg-red-500' : 
        type === 'success' ? 'bg-green-500' : 
        'bg-blue-500'
    } transition-opacity duration-300`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
} 
const userMenuButton = document.querySelector('[data-dropdown-toggle="userMenu"]');
const userMenu = document.getElementById('userMenu');
const searchInput = document.querySelector('input[type="search"]');
const searchSuggestions = document.getElementById('searchSuggestions');

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
        if (userMenu && !userMenuButton?.contains(e.target) && !userMenu.contains(e.target)) {
            userMenu.classList.add('hidden');
        }
    });

    // Search input handler
    if (searchInput) {
        searchInput.addEventListener('input', debounce(handleSearch, 300));
        searchInput.addEventListener('focus', () => {
            if (searchSuggestions && searchInput.value.length > 0) {
                searchSuggestions.classList.remove('hidden');
            }
        });
    }
});

// Search handler
async function handleSearch(e) {
    const query = e.target.value.trim();
    
    if (query.length < 2) {
        if (searchSuggestions) {
            searchSuggestions.classList.add('hidden');
        }
        return;
    }
    
    try {
        const response = await fetch(`/api/search.php?q=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        if (data.success && searchSuggestions) {
            // Update suggestions UI
            searchSuggestions.innerHTML = data.results
                .map(game => `
                    <a href="/game/${game.slug}" class="block px-4 py-2 hover:bg-gray-700">
                        <div class="flex items-center">
                            <img src="${game.thumbnail}" alt="${game.title}" class="w-10 h-10 rounded">
                            <div class="ml-3">
                                <div class="text-sm font-medium">${game.title}</div>
                                <div class="text-xs text-gray-400">${game.category}</div>
                            </div>
                        </div>
                    </a>
                `)
                .join('');
            
            searchSuggestions.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Search error:', error);
    }
}

// Utility function: Debounce
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Favorite game handler
async function toggleFavorite(gameId) {
    try {
        const response = await fetch('/api/favorites.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ game_id: gameId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            const button = document.querySelector(`[data-favorite-id="${gameId}"]`);
            if (button) {
                button.classList.toggle('text-red-500');
                button.classList.toggle('text-gray-400');
            }
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Favorite toggle error:', error);
        showToast('An error occurred while updating favorites', 'error');
    }
}

// Toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white ${
        type === 'error' ? 'bg-red-500' : 
        type === 'success' ? 'bg-green-500' : 
        'bg-blue-500'
    } transition-opacity duration-300`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
} 