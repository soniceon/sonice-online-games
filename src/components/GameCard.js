import { generateRatingStars } from '../utils/helpers.js';

class GameCard {
    constructor(game) {
        this.game = game;
    }

    render() {
        return `
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <a href="games/${this.game.id}.html" class="block" aria-label="${this.game.title}">
                    <div class="relative pb-[56.25%]">
                        <img src="${this.game.image}" 
                             alt="${this.game.title} game screenshot" 
                             class="absolute inset-0 w-full h-full object-cover"
                             loading="lazy"
                             width="300"
                             height="169">
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2 text-gray-800">${this.game.title}</h3>
                        <p class="text-gray-600 text-sm mb-2">${this.game.categories.join(', ')}</p>
                        <div class="flex items-center">
                            <div class="flex text-apple-yellow" aria-label="Rating: ${this.game.rating} out of 5">
                                ${generateRatingStars(this.game.rating)}
                            </div>
                            <span class="text-gray-600 text-sm ml-2">(${this.game.reviews})</span>
                        </div>
                        ${this.game.description ? `
                            <p class="text-gray-600 text-sm mt-2 line-clamp-2">${this.game.description}</p>
                        ` : ''}
                    </div>
                </a>
            </div>
        `;
    }

    static createGameGrid(games, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = games.map(game => new GameCard(game).render()).join('');
    }
}

export default GameCard; 