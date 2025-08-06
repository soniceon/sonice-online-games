import os
import re
import json
import glob
from collections import defaultdict
import codecs

def extract_game_info(file_path):
    """Extract game information from HTML file."""
    try:
        with codecs.open(file_path, 'r', encoding='utf-8-sig') as file:
            content = file.read()
            
            # Extract title
            title_match = re.search(r'<title>(.*?)</title>', content)
            title = title_match.group(1).replace(' - Sonice Games', '') if title_match else "未知游戏"
            
            # Extract slug from filename
            slug = os.path.basename(file_path).replace('.html', '')
            
            # Extract image URL
            image_match = re.search(r'<meta property="og:image" content="(.*?)"', content)
            image_url = image_match.group(1) if image_match else "/assets/images/default-game.jpg"
            
            # Extract description
            desc_match = re.search(r'<meta name="description" content="(.*?)"', content)
            description = desc_match.group(1) if desc_match else ""
            
            # Extract categories
            categories = []
            category_section = re.search(r'<div class="game-categories">(.*?)</div>', content, re.DOTALL)
            if category_section:
                category_tags = re.findall(r'<span class="category-badge">(.*?)</span>', category_section.group(1))
                categories = [cat.strip() for cat in category_tags if cat.strip()]
            
            # If no categories found, try alternative method
            if not categories:
                categories_match = re.search(r'<meta property="article:section" content="(.*?)"', content)
                if categories_match:
                    categories = [cat.strip() for cat in categories_match.group(1).split(',')]
            
            # Ensure we have at least one category
            if not categories:
                categories = ["其他"]
            
            return {
                "title": title,
                "slug": slug,
                "image": image_url,
                "description": description,
                "categories": categories,
                "url": f"/games/{slug}.html"
            }
    except Exception as e:
        print(f"Error extracting info from {file_path}: {e}")
        return None

def get_category_icon(category):
    """Return emoji icon for the category."""
    icon_map = {
        "动作": "🏃",
        "冒险": "🧭",
        "街机": "🕹️",
        "休闲": "🎮",
        "卡牌": "🃏",
        "经典": "🎯",
        "创意": "💡",
        "防御": "🛡️",
        "驾驶": "🚗",
        "教育": "📚",
        "格斗": "👊",
        "恐怖": "👻",
        "多人": "👥",
        "平台": "🏃‍♂️",
        "益智": "🧩",
        "赛车": "🏎️",
        "角色扮演": "🧙",
        "射击": "🔫",
        "模拟": "🎛️",
        "运动": "⚽",
        "策略": "♟️",
        "文字": "📝",
        "3D": "🎲",
        "HTML5": "🌐",
        "IO": "⚔️",
        "高分": "🏆",
        "单人": "👤",
        "二人": "👫",
        "物理": "⚖️",
        "像素": "🎮",
        "跑酷": "🏃‍♀️",
        "技巧": "🎯",
        "解谜": "🔍",
        "点击": "👆",
        "敏捷": "⚡",
        "儿童": "👶",
        "女生": "👧",
        "男生": "👦",
    }
    return icon_map.get(category, "🎮")

def get_category_description(category):
    """Return description for the category."""
    description_map = {
        "动作": "快节奏的动作游戏，考验你的反应速度和操作技巧。",
        "冒险": "探索未知世界，完成各种任务的冒险游戏。",
        "街机": "经典街机风格的游戏，重现传统游戏厅的欢乐时光。",
        "休闲": "简单易上手的休闲游戏，适合所有年龄段的玩家。",
        "卡牌": "各类卡牌收集与对战游戏，考验策略和运气。",
        "经典": "经久不衰的经典游戏，超越时间的经典玩法。",
        "创意": "充满创意和独特玩法的游戏，带来全新游戏体验。",
        "防御": "建造防御工事，阻止敌人入侵的策略游戏。",
        "驾驶": "驾驶各种交通工具的模拟游戏，体验驾驶乐趣。",
        "教育": "寓教于乐的教育类游戏，边玩边学习。",
        "格斗": "各类格斗对战游戏，展示你的格斗技巧。",
        "恐怖": "充满恐怖元素的游戏，挑战你的胆量。",
        "多人": "可与多名玩家一起游戏的多人联机游戏。",
        "平台": "经典平台跳跃游戏，挑战各种障碍。",
        "益智": "锻炼思维能力的益智解谜游戏，挑战你的智力。",
        "赛车": "紧张刺激的赛车游戏，体验速度与激情。",
        "角色扮演": "扮演各种角色，体验不同人生的角色扮演游戏。",
        "射击": "测试你的瞄准能力的射击游戏，锁定目标。",
        "模拟": "模拟现实生活的各种场景，体验不同职业和生活。",
        "运动": "各类体育运动游戏，体验运动的乐趣。",
        "策略": "需要深思熟虑的策略游戏，考验你的策略思维。",
        "文字": "以文字为主的游戏，包括文字冒险和解谜。",
        "3D": "采用3D图形的游戏，提供更加逼真的游戏体验。",
        "HTML5": "使用HTML5技术开发的游戏，无需插件即可在浏览器中运行。",
        "IO": "多人在线竞技类IO游戏，与全球玩家一起战斗。",
        "高分": "挑战高分的游戏，看看你能否登上排行榜。",
        "单人": "专为单人游戏设计的游戏，独自探索游戏世界。",
        "二人": "适合两个人一起玩的游戏，与朋友一起享受游戏乐趣。",
        "物理": "基于物理引擎的游戏，具有真实物理效果。",
        "像素": "采用像素艺术风格的复古游戏，怀旧经典。",
        "跑酷": "快速奔跑跳跃的跑酷游戏，考验反应和技巧。",
        "技巧": "需要特定技巧的游戏，磨练你的游戏能力。",
        "解谜": "各种解谜游戏，挑战你的逻辑思维能力。",
        "点击": "以点击为主要操作的游戏，简单易上手。",
        "敏捷": "考验反应速度的敏捷游戏，快速应对各种挑战。",
        "儿童": "专为儿童设计的游戏，内容健康有趣。",
        "女生": "针对女生喜好设计的游戏，内容丰富多彩。",
        "男生": "适合男生口味的游戏，充满挑战和刺激。",
    }
    return description_map.get(category, f"来玩最好的{category}游戏，免费在线体验各种精彩{category}游戏。")

def slugify(text):
    """Convert text to URL-friendly slug."""
    # Simple implementation - replace spaces with hyphens and lowercase
    slug = re.sub(r'[^\w\s-]', '', text.lower())
    slug = re.sub(r'[\s-]+', '-', slug)
    return slug

def main():
    # Ensure output directory exists
    output_dir = "category"
    os.makedirs(output_dir, exist_ok=True)
    
    # Load template
    try:
        with codecs.open("src/templates/category-template.html", "r", encoding="utf-8-sig") as file:
            template = file.read()
    except FileNotFoundError:
        print("Template file not found.")
        return
    
    # Find all game HTML files
    game_files = []
    for pattern in ["games/*.html", "pages/games/*.html"]:
        game_files.extend(glob.glob(pattern))
    
    print(f"Found {len(game_files)} game files")
    
    # Collect games by category
    categories = defaultdict(list)
    games_by_slug = {}
    
    for file_path in game_files:
        game_info = extract_game_info(file_path)
        if game_info:
            games_by_slug[game_info["slug"]] = game_info
            for category in game_info["categories"]:
                categories[category].append(game_info)
    
    # Generate category pages
    success_count = 0
    error_count = 0
    category_data = {"categories": {}}
    
    for category, games in categories.items():
        try:
            category_id = slugify(category)
            category_icon = get_category_icon(category)
            category_description = get_category_description(category)
            
            # Sort games by title
            games.sort(key=lambda x: x["title"])
            
            # Create page content
            page_content = template
            page_content = page_content.replace("{{CATEGORY_NAME}}", category)
            page_content = page_content.replace("{{CATEGORY_ID}}", category_id)
            page_content = page_content.replace("{{CATEGORY_ICON}}", category_icon)
            page_content = page_content.replace("{{CATEGORY_DESCRIPTION}}", category_description)
            
            # Handle games list using Jinja2-style template
            games_html = ""
            for game in games:
                game_categories_html = ""
                for game_category in game["categories"]:
                    game_categories_html += f'<span class="category-badge">{game_category}</span>'
                
                games_html += f'''
                <div class="game-card">
                    <a href="{game['url']}" class="game-link">
                        <div class="game-image">
                            <img src="{game['image']}" alt="{game['title']}">
                            <div class="play-button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                </svg>
                            </div>
                        </div>
                        <div class="game-info">
                            <h3 class="game-title">{game['title']}</h3>
                            <div class="game-categories">
                                {game_categories_html}
                            </div>
                        </div>
                    </a>
                </div>
                '''
            
            # Replace the Jinja2 loop with our generated HTML
            page_content = re.sub(
                r'{% for game in GAMES %}.*?{% endfor %}',
                games_html,
                page_content,
                flags=re.DOTALL
            )
            
            # Write the category page
            output_path = os.path.join(output_dir, f"{category_id}.html")
            with codecs.open(output_path, "w", encoding="utf-8") as file:
                file.write(page_content)
            
            # Add to category data
            category_data["categories"][category] = {
                "id": category_id,
                "name": category,
                "icon": category_icon,
                "description": category_description,
                "games": [game["slug"] for game in games],
                "url": f"/category/{category_id}.html"
            }
            
            success_count += 1
            print(f"Created category page: {output_path} with {len(games)} games")
        
        except Exception as e:
            print(f"Error creating category page for {category}: {e}")
            error_count += 1
    
    # Save category data
    try:
        with open("data/categories.json", "w", encoding="utf-8") as file:
            json.dump(category_data, file, ensure_ascii=False, indent=2)
        print(f"Category data saved to data/categories.json")
    except Exception as e:
        print(f"Error saving category data: {e}")
    
    print(f"\nCategory page generation completed:")
    print(f"Success: {success_count}")
    print(f"Errors: {error_count}")

if __name__ == "__main__":
    main() 