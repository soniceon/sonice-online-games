const fs = require('fs');
const path = require('path');

// Configuration
const GAMES_DATA_PATH = path.join(__dirname, '../data/games.json');
const OUTPUT_DIR = path.join(__dirname, '../pages/games');
const BACKUP_DIR = path.join(__dirname, '../pages/games/backups');

// Find the latest game template file
function findLatestTemplate() {
    const rootDir = path.join(__dirname, '..');
    const templateFiles = [
        'game-template.html',
        'game-template-v2.html',
        'game-template-v3.html',
        'game-template-v3.1.html'
    ];
    
    // Check if files exist, preferring the latest version
    for (let i = templateFiles.length - 1; i >= 0; i--) {
        const templatePath = path.join(rootDir, templateFiles[i]);
        if (fs.existsSync(templatePath)) {
            console.log(`Using template: ${templateFiles[i]}`);
            return templatePath;
        }
    }
    
    // If no specific template is found, check in pages/games directory
    const fallbackTemplate = path.join(rootDir, 'pages/games/game-template.html');
    if (fs.existsSync(fallbackTemplate)) {
        console.log(`Using fallback template from pages/games directory`);
        return fallbackTemplate;
    }
    
    throw new Error('Could not find any game template file');
}

// Ensure directory exists
function ensureDirectoryExists(directory) {
    if (!fs.existsSync(directory)) {
        console.log(`Creating directory: ${directory}`);
        fs.mkdirSync(directory, { recursive: true });
    }
}

// Create a slug from a title if it doesn't exist
function createSlug(title) {
    return title.toLowerCase()
        .replace(/[^\w\s-]/g, '') // Remove special characters
        .replace(/\s+/g, '-')     // Replace spaces with hyphens
        .trim();                   // Trim leading/trailing spaces
}

// Backup existing file if it exists
function backupExistingFile(filePath) {
    if (fs.existsSync(filePath)) {
        ensureDirectoryExists(BACKUP_DIR);
        
        const fileName = path.basename(filePath);
        const timestamp = new Date().toISOString().replace(/[:T.]/g, '-').slice(0, -5);
        const backupPath = path.join(BACKUP_DIR, `${fileName}.${timestamp}.bak`);
        
        try {
            fs.copyFileSync(filePath, backupPath);
            console.log(`Backed up: ${filePath} → ${backupPath}`);
            return true;
        } catch (error) {
            console.warn(`Warning: Failed to backup ${filePath}: ${error.message}`);
            return false;
        }
    }
    return false;
}

// Generate individual game pages from template
function generateGamePages() {
    try {
        ensureDirectoryExists(OUTPUT_DIR);
        
        // Find and load template
        const TEMPLATE_PATH = findLatestTemplate();
        console.log(`Reading template file: ${TEMPLATE_PATH}`);
        let template;
        
        try {
            template = fs.readFileSync(TEMPLATE_PATH, 'utf8');
        } catch (error) {
            throw new Error(`Failed to read template file: ${error.message}`);
        }
        
        // Load games data
        console.log(`Reading games data: ${GAMES_DATA_PATH}`);
        let gamesData;
        
        try {
            const rawData = fs.readFileSync(GAMES_DATA_PATH, 'utf8');
            gamesData = JSON.parse(rawData);
        } catch (error) {
            throw new Error(`Failed to read or parse games data: ${error.message}`);
        }
        
        if (!gamesData.games || !Array.isArray(gamesData.games)) {
            throw new Error('Invalid games data: games property is missing or not an array');
        }
        
        console.log(`Generating pages for ${gamesData.games.length} games...`);
        
        // Track statistics
        const stats = {
            successful: 0,
            failed: 0,
            backups: 0,
            slugsCreated: 0
        };
        
        // Create a page for each game
        gamesData.games.forEach((game, index) => {
            try {
                // Skip invalid games
                if (!game || typeof game !== 'object') {
                    console.error(`Skipping invalid game at index ${index}`);
                    stats.failed++;
                    return;
                }
                
                // Make sure game has required fields
                if (!game.title) {
                    console.error(`Skipping game with no title at index ${index}`);
                    stats.failed++;
                    return;
                }
                
                // Ensure game has a slug
                if (!game.slug) {
                    game.slug = createSlug(game.title);
                    console.log(`Created slug for "${game.title}": ${game.slug}`);
                    stats.slugsCreated++;
                }
                
                const outputPath = path.join(OUTPUT_DIR, `${game.slug}.html`);
                
                // Backup existing file
                if (backupExistingFile(outputPath)) {
                    stats.backups++;
                }
                
                // Replace placeholders in template
                let gameHtml = template;
                
                // Replace all known game properties
                Object.keys(game).forEach(key => {
                    const regex = new RegExp(`{{game.${key}}}`, 'g');
                    const value = game[key] !== null && game[key] !== undefined ? game[key] : '';
                    gameHtml = gameHtml.replace(regex, value);
                });
                
                // Add category-specific class to body
                gameHtml = gameHtml.replace('{{categoryClass}}', 
                    `category-${game.category ? game.category.toLowerCase().replace(/\s+/g, '-') : 'other'}`);
                
                // Write the file
                fs.writeFileSync(outputPath, gameHtml);
                console.log(`Generated: ${game.slug}.html`);
                stats.successful++;
            } catch (error) {
                console.error(`Error generating page for "${game?.title || 'Unknown game'}": ${error.message}`);
                stats.failed++;
            }
        });
        
        console.log('\nGeneration Summary:');
        console.log(`- ${stats.successful} pages successfully generated`);
        console.log(`- ${stats.failed} pages failed to generate`);
        console.log(`- ${stats.backups} existing pages backed up`);
        console.log(`- ${stats.slugsCreated} slugs automatically created`);
        
        if (stats.successful > 0) {
            console.log('\nSuccessfully generated game pages!');
        } else {
            throw new Error('No game pages were successfully generated');
        }
    } catch (error) {
        console.error('Error generating game pages:', error);
        process.exit(1);
    }
}

// Main execution
function main() {
    try {
        console.log('============================================');
        console.log('   GAME PAGE GENERATOR - SONICE ONLINE     ');
        console.log('============================================\n');
        
        const startTime = new Date();
        generateGamePages();
        const endTime = new Date();
        const duration = (endTime - startTime) / 1000; // in seconds
        
        console.log(`\nTotal execution time: ${duration.toFixed(2)} seconds`);
    } catch (error) {
        console.error('An error occurred:', error);
        process.exit(1);
    }
}

// Run the script
main();

// Additional option: Generate index.html for the games directory that redirects to the main games page
const indexRedirect = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="0;url=/pages/games.html">
    <title>Games - Sonice Online Games</title>
</head>
<body>
    <p>Redirecting to <a href="/pages/games.html">games page</a>...</p>
</body>
</html>
`;

try {
    fs.writeFileSync(path.join(OUTPUT_DIR, 'index.html'), indexRedirect);
    console.log('- Generated games directory index redirect page');
} catch (error) {
    console.error('Error creating redirect page:', error);
}

console.log('\nDone!'); 