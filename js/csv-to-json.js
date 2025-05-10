const fs = require('fs');
const path = require('path');
const csv = require('csvtojson');

// Configuration
const CSV_PATH = path.join(__dirname, '../data/games-sample.csv');
const JSON_OUTPUT_PATH = path.join(__dirname, '../data/games.json');
const CATEGORIES_OUTPUT_PATH = path.join(__dirname, '../data/categories.json');

// Convert CSV to JSON and extract categories
async function convertCsvToJson() {
    try {
        console.log(`Reading CSV file: ${CSV_PATH}`);
        
        // Parse CSV file to JSON array
        const games = await csv().fromFile(CSV_PATH);
        
        console.log(`Found ${games.length} games in CSV`);
        
        // Create categories list
        const categories = [...new Set(games.map(game => game.category))];
        
        console.log(`Found ${categories.length} unique categories`);
        
        // Prepare JSON structures
        const gamesJson = {
            count: games.length,
            games: games
        };
        
        const categoriesJson = {
            count: categories.length,
            categories: categories.map(category => ({
                name: category,
                slug: category.toLowerCase().replace(/\s+/g, '-'),
                count: games.filter(game => game.category === category).length
            }))
        };
        
        // Write JSON files
        console.log(`Writing games JSON to: ${JSON_OUTPUT_PATH}`);
        fs.writeFileSync(JSON_OUTPUT_PATH, JSON.stringify(gamesJson, null, 2));
        
        console.log(`Writing categories JSON to: ${CATEGORIES_OUTPUT_PATH}`);
        fs.writeFileSync(CATEGORIES_OUTPUT_PATH, JSON.stringify(categoriesJson, null, 2));
        
        console.log('CSV to JSON conversion complete!');
    } catch (error) {
        console.error('Error converting CSV to JSON:', error);
        process.exit(1);
    }
}

// Main execution
async function main() {
    try {
        await convertCsvToJson();
    } catch (error) {
        console.error('An error occurred:', error);
        process.exit(1);
    }
}

// Run the script
main(); 