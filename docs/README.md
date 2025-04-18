# Game Scraper and Page Generator

This project scrapes game data from onlinegames.io and generates HTML pages for each game.

## Setup

1. Install the required dependencies:
```bash
pip install -r requirements.txt
```

2. Create a `templates` directory and place the `game_template.html` file inside it.

## Usage

1. First, run the scraper to collect game data:
```bash
python scraper.py
```
This will create a `games_data.json` file containing all the scraped game data.

2. Then, generate the HTML pages:
```bash
python generate_pages.py
```
This will create individual HTML pages for each game in the `games` directory.

## Files

- `scraper.py`: Scrapes game data from onlinegames.io
- `generate_pages.py`: Generates HTML pages from the scraped data
- `templates/game_template.html`: Template for individual game pages
- `requirements.txt`: Python dependencies
- `games_data.json`: Output file containing scraped game data
- `games/`: Directory containing generated HTML pages

## Features

- Scrapes game titles, descriptions, thumbnails, tags, and iframe URLs
- Generates SEO-friendly HTML pages
- Uses Tailwind CSS for styling
- Responsive design
- Rating system integration (to be implemented)

## Notes

- The scraper includes delays to avoid overwhelming the server
- Make sure to respect the website's robots.txt and terms of service
- The generated pages use the same styling as your main website