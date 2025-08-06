// Favorite game functionality
async function toggleFavorite(gameId) {
    try {
        const response = await fetch(`/api/games/${gameId}/favorite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        if (response.ok) {
            const button = document.querySelector(`[data-game-id="${gameId}"]`);
            const icon = button.querySelector('i');
            icon.classList.toggle('text-red-500');
            icon.classList.toggle('text-white');
            showToast(icon.classList.contains('text-red-500') ? 'Added to favorites' : 'Removed from favorites');
        } else {
            const data = await response.json();
            showToast(data.message || 'Failed to update favorites', 'error');
        }
    } catch (error) {
        showToast('An error occurred. Please try again.', 'error');
    }
}

// Toast notifications
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type === 'error' ? 'bg-red-600' : 'bg-green-600'}`;
    toast.textContent = message;
    document.body.appendChild(toast);

    // Trigger reflow
    toast.offsetHeight;

    // Show toast
    toast.classList.add('show');

    // Remove toast after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Search functionality
const searchInput = document.querySelector('input[type="search"]');
if (searchInput) {
    let searchTimeout;
    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(async () => {
            const query = e.target.value.trim();
            if (query.length >= 2) {
                try {
                    const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
                    if (response.ok) {
                        const data = await response.json();
                        // Handle search results
                        if (data.results.length > 0) {
                            // Update UI with search results
                        } else {
                            // Show no results message
                        }
                    }
                } catch (error) {
                    console.error('Search failed:', error);
                }
            }
        }, 300);
    });
}

// Mobile menu toggle
const mobileMenuButton = document.querySelector('[data-mobile-menu]');
const sidebar = document.querySelector('#sidebar');

if (mobileMenuButton && sidebar) {
    mobileMenuButton.addEventListener('click', () => {
        sidebar.classList.toggle('translate-x-0');
        sidebar.classList.toggle('-translate-x-full');
    });
}

// Game play count
function incrementPlayCount(gameId) {
    fetch(`/api/games/${gameId}/play`, {
        method: 'POST',
    }).catch(error => {
        console.error('Failed to increment play count:', error);
    });
}

// Category filter
function filterByCategory(categoryId) {
    window.location.href = `/category/${categoryId}`;
}

// Infinite scroll for game lists
let isLoading = false;
let currentPage = 1;

window.addEventListener('scroll', () => {
    if (isLoading) return;

    const gameGrid = document.querySelector('.game-grid');
    if (!gameGrid) return;

    const threshold = 100;
    if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - threshold) {
        loadMoreGames();
    }
});

async function loadMoreGames() {
    isLoading = true;
    const gameGrid = document.querySelector('.game-grid');
    const loadingSpinner = document.createElement('div');
    loadingSpinner.className = 'spinner mx-auto mt-4';
    gameGrid.appendChild(loadingSpinner);

    try {
        const response = await fetch(`/api/games?page=${currentPage + 1}`);
        if (response.ok) {
            const data = await response.json();
            if (data.games.length > 0) {
                currentPage++;
                // Append new games to the grid
                data.games.forEach(game => {
                    // Create and append game card
                });
            }
        }
    } catch (error) {
        console.error('Failed to load more games:', error);
    } finally {
        gameGrid.removeChild(loadingSpinner);
        isLoading = false;
    }
}

// Initialize tooltips
const tooltips = document.querySelectorAll('[data-tooltip]');
tooltips.forEach(element => {
    element.addEventListener('mouseenter', e => {
        const tooltip = document.createElement('div');
        tooltip.className = 'absolute z-50 px-2 py-1 text-sm text-white bg-gray-900 rounded-md';
        tooltip.textContent = e.target.dataset.tooltip;
        document.body.appendChild(tooltip);

        const rect = e.target.getBoundingClientRect();
        tooltip.style.top = `${rect.bottom + 5}px`;
        tooltip.style.left = `${rect.left + (rect.width - tooltip.offsetWidth) / 2}px`;

        e.target.addEventListener('mouseleave', () => {
            document.body.removeChild(tooltip);
        }, { once: true });
    });
}); 