function getRandomGames(currentGameId, category, count = 4) {
    // Get all games in the same category
    let games = gamesData[category].filter(game => game.id !== currentGameId);
    
    // Shuffle games array randomly
    for (let i = games.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [games[i], games[j]] = [games[j], games[i]];
    }
    
    // Return specified number of games
    return games.slice(0, count);
}

function renderGameCard(game) {
    return `
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <a href="${game.id}.html">
                <img src="../images/${game.image}" alt="${game.title}" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="font-bold text-lg mb-2 text-gray-800">${game.title}</h3>
                    <p class="text-gray-600 text-sm">${game.categories.join(', ')}</p>
                </div>
            </a>
        </div>
    `;
}