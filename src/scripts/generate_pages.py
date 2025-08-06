import json
import os
from jinja2 import Environment, FileSystemLoader
import random

def load_game_data():
    with open('games_data.json', 'r', encoding='utf-8') as f:
        data = json.load(f)
        return data['games']  # 返回games数组

def generate_slug(title):
    # 将标题转换为 URL 友好的 slug
    return title.lower().replace(' ', '-').replace('_', '-')

def get_category_icon(category):
    # 为每个分类定义对应的emoji图标
    icons = {
        'action': '🎮',
        'adventure': '🗺️',
        'puzzle': '🧩',
        'strategy': '♟️',
        'sports': '⚽',
        'racing': '🏎️',
        'arcade': '👾',
        'shooting': '🎯',
        'other': '🎲'
    }
    return icons.get(category.lower(), '🎮')

def format_game_data(game):
    """格式化游戏数据，确保所有必要的字段都存在"""
    return {
        'id': game.get('id', ''),
        'title': game.get('title', ''),
        'description': game.get('description', ''),
        'image': game.get('image', '/assets/images/default-game.png'),
        'url': f'/games/{game["id"]}.html',
        'rating': game.get('rating', random.uniform(3.5, 5.0)),
        'reviews': game.get('reviews', random.randint(10, 1000)),
        'category': game.get('category', 'other'),
        'controls': game.get('controls', 'Use mouse to play')
    }

def generate_pages(games_data):
    env = Environment(loader=FileSystemLoader('templates'))
    game_template = env.get_template('game-template.html')
    category_template = env.get_template('category-template.html')
    
    # 确保输出目录存在
    os.makedirs('games', exist_ok=True)
    os.makedirs('categories', exist_ok=True)
    
    # 格式化所有游戏数据
    formatted_games = [format_game_data(game) for game in games_data]
    
    # 按类别组织游戏
    games_by_category = {}
    for game in formatted_games:
        category = game['category']
        if category not in games_by_category:
            games_by_category[category] = []
        games_by_category[category].append(game)
    
    # 生成游戏页面
    for game in formatted_games:
        # 为每个游戏随机选择4个推荐游戏（不包括当前游戏）
        other_games = [g for g in formatted_games if g['id'] != game['id']]
        recommended_games = random.sample(other_games, min(4, len(other_games)))
        
        # 生成游戏页面
        output = game_template.render(
            game=game,
            recommended_games=recommended_games
        )
        
        # 保存游戏页面
        with open(f'games/{game["id"]}.html', 'w', encoding='utf-8') as f:
            f.write(output)
    
    # 生成分类数据文件
    categories_data = {
        'categories': {
            category: {
                'games': games
            } for category, games in games_by_category.items()
        }
    }
    
    # 保存分类数据
    os.makedirs('data', exist_ok=True)
    with open('data/categories.json', 'w', encoding='utf-8') as f:
        json.dump(categories_data, f, indent=2)
    
    # 保存游戏数据
    with open('data/games.json', 'w', encoding='utf-8') as f:
        json.dump({'games': formatted_games}, f, indent=2)
    
    # 生成分类页面
    category_descriptions = {
        'action': '体验刺激的动作游戏，享受紧张刺激的游戏玩法和挑战。',
        'adventure': '踏上史诗般的冒险之旅，探索迷人的游戏世界。',
        'puzzle': '挑战你的思维，体验引人入胜的益智游戏。',
        'strategy': '测试你的战术技能，体验策略性的游戏玩法。',
        'sports': '参与各种体育模拟游戏，展示你的运动天赋。',
        'racing': '感受速度与激情，体验刺激的赛车游戏。',
        'arcade': '享受经典街机风格的游戏，简单但令人上瘾的游戏玩法。',
        'shooting': '测试你的瞄准能力和反应速度。',
        'other': '发现独特多样的游戏，超越传统类别的精彩体验。'
    }
    
    for category, games in games_by_category.items():
        category_name = category.title()
        category_id = generate_slug(category)
        category_description = category_descriptions.get(category.lower(), f'免费玩最好的{category.lower()}游戏！')
        category_icon = get_category_icon(category)
        
        output = category_template.render(
            CATEGORY_NAME=category_name,
            CATEGORY_ID=category_id,
            CATEGORY_DESCRIPTION=category_description,
            CATEGORY_ICON=category_icon,
            GAMES=games  # 添加游戏列表数据
        )
        
        with open(f'categories/{category_id}.html', 'w', encoding='utf-8') as f:
            f.write(output)

if __name__ == "__main__":
    games_data = load_game_data()
    generate_pages(games_data) 