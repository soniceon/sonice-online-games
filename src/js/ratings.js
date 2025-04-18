// Game Rating and Comment System
class RatingSystem {
    constructor() {
        this.ratings = JSON.parse(localStorage.getItem('gameRatings') || '{}');
        this.comments = JSON.parse(localStorage.getItem('gameComments') || '{}');
        this.initRatingUI();
    }

    initRatingUI() {
        // Add rating modal to body
        const ratingModal = document.createElement('div');
        ratingModal.id = 'rating-modal';
        ratingModal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50';
        ratingModal.innerHTML = `
            <div class="bg-dark-lighter rounded-lg p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-white" id="rating-modal-title">Rate Game</h2>
                    <button class="text-gray-400 hover:text-white" id="close-rating-modal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form id="rating-form" class="space-y-4">
                    <input type="hidden" id="game-id">
                    <div class="flex justify-center space-x-2" id="star-rating">
                        ${Array(5).fill().map((_, i) => `
                            <button type="button" class="star-btn text-3xl text-gray-400 hover:text-yellow-400" data-rating="${i + 1}">★</button>
                        `).join('')}
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-2" for="comment">Your Comment</label>
                        <textarea id="comment" rows="4" class="w-full px-4 py-2 rounded bg-dark border border-gray-700 text-white focus:outline-none focus:border-purple-primary" required></textarea>
                    </div>
                    <button type="submit" class="w-full py-2 px-4 bg-purple-primary text-white rounded hover:bg-purple-600 transition-colors">
                        Submit Review
                    </button>
                </form>
            </div>
        `;
        document.body.appendChild(ratingModal);

        // Event Listeners
        document.getElementById('close-rating-modal').addEventListener('click', () => this.hideRatingModal());
        document.getElementById('rating-form').addEventListener('submit', (e) => this.handleRatingSubmit(e));
        
        // Star rating buttons
        const starButtons = document.querySelectorAll('.star-btn');
        starButtons.forEach(btn => {
            btn.addEventListener('click', () => this.handleStarClick(btn));
        });
    }

    showRatingModal(gameId, gameTitle) {
        if (!auth.currentUser) {
            auth.showAuthModal();
            return;
        }

        document.getElementById('game-id').value = gameId;
        document.getElementById('rating-modal-title').textContent = `Rate ${gameTitle}`;
        document.getElementById('rating-modal').classList.remove('hidden');

        // Reset form
        document.getElementById('rating-form').reset();
        this.updateStars(0);

        // Pre-fill if user has already rated
        const userRating = this.getUserRating(gameId);
        if (userRating) {
            this.updateStars(userRating.rating);
            document.getElementById('comment').value = userRating.comment;
        }
    }

    hideRatingModal() {
        document.getElementById('rating-modal').classList.add('hidden');
    }

    handleStarClick(button) {
        const rating = parseInt(button.dataset.rating);
        this.updateStars(rating);
    }

    updateStars(rating) {
        const stars = document.querySelectorAll('.star-btn');
        stars.forEach((star, index) => {
            star.classList.toggle('text-yellow-400', index < rating);
            star.classList.toggle('text-gray-400', index >= rating);
        });
    }

    handleRatingSubmit(e) {
        e.preventDefault();
        const gameId = document.getElementById('game-id').value;
        const rating = document.querySelectorAll('.star-btn.text-yellow-400').length;
        const comment = document.getElementById('comment').value;

        if (!rating) {
            this.showToast('Please select a rating', 'error');
            return;
        }

        this.addRating(gameId, rating, comment);
        this.hideRatingModal();
        this.showToast('Thank you for your review!');
        this.updateGameRating(gameId);
    }

    addRating(gameId, rating, comment) {
        if (!this.ratings[gameId]) {
            this.ratings[gameId] = [];
        }
        if (!this.comments[gameId]) {
            this.comments[gameId] = [];
        }

        const userId = auth.currentUser.id;
        const username = auth.currentUser.username;
        const timestamp = new Date().toISOString();

        // Remove existing rating by this user if any
        this.ratings[gameId] = this.ratings[gameId].filter(r => r.userId !== userId);
        this.comments[gameId] = this.comments[gameId].filter(c => c.userId !== userId);

        // Add new rating and comment
        this.ratings[gameId].push({ userId, rating, timestamp });
        this.comments[gameId].push({ userId, username, comment, timestamp });

        localStorage.setItem('gameRatings', JSON.stringify(this.ratings));
        localStorage.setItem('gameComments', JSON.stringify(this.comments));
    }

    getUserRating(gameId) {
        if (!auth.currentUser || !this.ratings[gameId]) return null;

        const rating = this.ratings[gameId].find(r => r.userId === auth.currentUser.id);
        const comment = this.comments[gameId]?.find(c => c.userId === auth.currentUser.id);

        if (!rating) return null;

        return {
            rating: rating.rating,
            comment: comment?.comment || ''
        };
    }

    getGameRating(gameId) {
        if (!this.ratings[gameId] || !this.ratings[gameId].length) return 0;

        const sum = this.ratings[gameId].reduce((acc, r) => acc + r.rating, 0);
        return Math.round((sum / this.ratings[gameId].length) * 10) / 10;
    }

    getGameComments(gameId) {
        return this.comments[gameId] || [];
    }

    updateGameRating(gameId) {
        const ratingElement = document.querySelector(`[data-game-id="${gameId}"] .game-rating`);
        if (ratingElement) {
            const rating = this.getGameRating(gameId);
            const count = this.ratings[gameId]?.length || 0;
            ratingElement.innerHTML = `
                <div class="flex items-center space-x-1">
                    <span class="text-yellow-400">★</span>
                    <span class="text-white">${rating}</span>
                    <span class="text-gray-400">(${count})</span>
                </div>
            `;
        }
    }

    showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } transition-opacity duration-300`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Initialize rating system
const ratingSystem = new RatingSystem(); 