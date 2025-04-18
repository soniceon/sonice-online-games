import requests
from bs4 import BeautifulSoup
import json
import logging
import os
from urllib.parse import urljoin
import time
import re

# Set up logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

class GameScraper:
    def __init__(self):
        self.base_url = "https://www.onlinegames.io"
        self.headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language': 'en-US,en;q=0.5',
            'Referer': 'https://www.onlinegames.io/',
            'Cookie': 'cf_clearance=your_cf_clearance_cookie_here'  # 需要从浏览器获取
        }
        self.games_data = []
        self.processed_titles = set()

    def get_soup(self, url):
        try:
            response = requests.get(url, headers=self.headers)
            response.raise_for_status()
            return BeautifulSoup(response.text, 'html.parser')
        except requests.RequestException as e:
            logger.error(f"Error fetching {url}: {e}")
            return None

    def extract_game_data(self, game_url):
        soup = self.get_soup(game_url)
        if not soup:
            return None

        try:
            # Extract game title
            title = soup.find('h1').text.strip()
            
            # Skip if we've already processed this game
            if title in self.processed_titles:
                return None
            self.processed_titles.add(title)

            # Extract game description
            description = soup.find('meta', {'name': 'description'})['content']

            # Extract game image
            image = soup.find('meta', {'property': 'og:image'})['content']
            if not image.startswith(('http://', 'https://')):
                image = urljoin(self.base_url, image)

            # Extract game categories
            categories = [tag.text.strip() for tag in soup.find_all('a', {'class': 'tag'})]
            if not categories:
                categories = ['Action']

            # Extract iframe URL - look for game container
            game_container = soup.find('div', {'class': 'game-container'})
            if not game_container:
                game_container = soup.find('div', {'id': 'game-container'})
            
            iframe = None
            if game_container:
                iframe = game_container.find('iframe')
            else:
                # Try to find iframe directly
                iframe = soup.find('iframe', {'id': 'game-iframe'})
            
            iframe_url = None
            if iframe:
                iframe_url = iframe.get('src')
                if iframe_url and not iframe_url.startswith(('http://', 'https://')):
                    iframe_url = urljoin(self.base_url, iframe_url)
            
            if not iframe_url:
                # Try to find game script
                game_script = soup.find('script', string=re.compile('gameUrl'))
                if game_script:
                    match = re.search(r'gameUrl\s*=\s*["\'](.*?)["\']', game_script.string)
                    if match:
                        iframe_url = match.group(1)
            
            if not iframe_url:
                logger.warning(f"No iframe URL found for {title}")
                return None

            # Create game data structure
            game_data = {
                'title': title,
                'description': description,
                'image': image,
                'categories': categories,
                'iframeUrl': iframe_url,
                'controls': self.get_default_controls(categories)
            }

            logger.info(f"Successfully extracted data for {title}")
            return game_data
        except Exception as e:
            logger.error(f"Error extracting data from {game_url}: {e}")
            return None

    def get_default_controls(self, categories):
        if any(cat in ['Racing', 'Driving', 'Car'] for cat in categories):
            return "Use WASD or Arrow keys to drive, Spacebar for handbrake, R to reset car position."
        elif any(cat in ['Shooting', 'FPS', 'Action'] for cat in categories):
            return "Use WASD or Arrow keys to move, Mouse to aim and shoot, R to reload."
        elif any(cat in ['Sports', 'Basketball'] for cat in categories):
            return "Use Mouse to aim and shoot, Space to jump, WASD or Arrow keys to move."
        else:
            return "Use WASD or Arrow keys to move, Mouse to interact, Space to jump or select."

    def scrape_category(self, category_url):
        soup = self.get_soup(category_url)
        if not soup:
            return

        # Find all game links in the category page
        game_links = soup.find_all('a', {'class': 'game-link'})
        if not game_links:
            # Try alternative class names
            game_links = soup.find_all('a', {'class': 'game'})
        
        for link in game_links:
            game_url = urljoin(self.base_url, link['href'])
            logger.info(f"Scraping game: {game_url}")
            
            game_data = self.extract_game_data(game_url)
            if game_data:
                self.games_data.append(game_data)
                # Save data after each game to prevent data loss
                self.save_data()
            
            # Add delay to avoid overwhelming the server
            time.sleep(2)

    def scrape_game(self, game_url):
        game_data = self.extract_game_data(game_url)
        if game_data:
            self.games_data.append(game_data)
            self.save_data()
            logger.info(f"Successfully scraped {game_data['title']}")
        else:
            logger.error(f"Failed to scrape {game_url}")

    def save_data(self):
        with open('games_data.json', 'w', encoding='utf-8') as f:
            json.dump(self.games_data, f, ensure_ascii=False, indent=2)

    def scrape_all_categories(self):
        categories = [
            '/t/action/',
            '/t/racing/',
            '/t/sports/',
            '/t/shooter/',
            '/t/cards/'
        ]

        for category in categories:
            category_url = urljoin(self.base_url, category)
            logger.info(f"Scraping category: {category_url}")
            self.scrape_category(category_url)

def main():
    # Create output directory if it doesn't exist
    output_dir = 'scraped_data'
    os.makedirs(output_dir, exist_ok=True)

    # Initialize scraper
    scraper = GameScraper()
    
    # Scrape all categories
    scraper.scrape_all_categories()
    
    # Save results
    output_file = os.path.join(output_dir, 'games_data.json')
    scraper.save_data()
    logger.info(f"Scraping completed. Found {len(scraper.games_data)} games.")

if __name__ == "__main__":
    main() 