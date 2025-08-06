#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import json
import re
import random
import codecs
from collections import defaultdict

def read_games_data():
    """读取游戏数据"""
    base_dir = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
    data_path = os.path.join(base_dir, 'data', 'games.json')
    
    if not os.path.exists(data_path):
        print(f"Error: Games data file not found at {data_path}")
        return None
    
    try:
        with open(data_path, 'r', encoding='utf-8') as f:
            data = json.load(f)
        return data.get('games', [])
    except Exception as e:
        print(f"Error reading games data: {e}")
        return None

def read_categories_data():
    """读取分类数据"""
    base_dir = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
    data_path = os.path.join(base_dir, 'data', 'categories.json')
    
    if not os.path.exists(data_path):
        print(f"Error: Categories data file not found at {data_path}")
        return None
    
    try:
        with open(data_path, 'r', encoding='utf-8') as f:
            data = json.load(f)
        return data.get('categories', {})
    except Exception as e:
        print(f"Error reading categories data: {e}")
        return None

def organize_games_by_category(games):
    """将游戏按分类组织"""
    games_by_category = defaultdict(list)
    
    # 主要分类
    main_categories = [
        "featured", "action", "racing", "clicker", 
        "idle", "mining", "merge", "other"
    ]
    
    # 初始化所有主要分类
    for category in main_categories:
        games_by_category[category] = []
    
    # 将游戏分配到各个分类
    for game in games:
        # 为精选游戏随机选择一些
        if random.random() > 0.85:
            games_by_category["featured"].append(game)
        
        # 根据游戏类别分配
        for category in game.get("categories", []):
            category_lower = category.lower()
            if category_lower in main_categories:
                games_by_category[category_lower].append(game)
    
    # 如果精选游戏不足12个，添加一些热门游戏
    if len(games_by_category["featured"]) < 8:
        for category in ["idle", "clicker", "racing", "action"]:
            available_games = [g for g in games_by_category[category] 
                              if g not in games_by_category["featured"]]
            random.shuffle(available_games)
            needed = 8 - len(games_by_category["featured"])
            games_by_category["featured"].extend(available_games[:needed])
            if len(games_by_category["featured"]) >= 8:
                break
    
    # 确保每个分类不超过12个游戏
    for category in games_by_category:
        random.shuffle(games_by_category[category])
        games_by_category[category] = games_by_category[category][:12]
    
    return games_by_category

def create_game_card_html(game):
    """生成游戏卡片HTML"""
    game_id = game.get("id", "")
    title = game.get("title", "Unknown Game")
    category = game.get("categories", ["Other"])[0]
    
    # 图片路径处理，设置多个备选路径确保图片能显示
    image_html = f'''
        <img 
            src="/assets/images/games/{game_id}.webp" 
            alt="{title}" 
            loading="lazy"
            onerror="this.onerror=null; this.src='/assets/images/games/{game_id}.jpg'; 
                    if(this.naturalHeight==0){{this.src='/src/assets/images/games/{game_id}.webp'}}; 
                    if(this.naturalHeight==0){{this.src='/src/assets/images/games/{game_id}.jpg'}}; 
                    if(this.naturalHeight==0){{this.src='/assets/images/default-game.webp'}}"
        >
    '''
    
    return f'''
    <div class="game-card">
        <a href="/games/{game_id}.html" class="block">
            <div class="relative">
                {image_html}
                <div class="play-overlay absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="p-4">
                <h3 class="text-white font-medium line-clamp-1">{title}</h3>
                <div class="flex flex-wrap gap-2 mt-1">
                    <span class="px-2 py-1 text-xs bg-purple-primary bg-opacity-25 text-purple-primary rounded-full">{category}</span>
                </div>
            </div>
        </a>
    </div>
    '''

def update_index_page():
    """更新首页内容"""
    base_dir = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
    index_path = os.path.join(base_dir, 'index.html')
    
    # 读取游戏数据
    games = read_games_data()
    if not games:
        print("Failed to load games data")
        return False
    
    # 读取分类数据
    categories = read_categories_data()
    if not categories:
        print("Failed to load categories data")
        return False
    
    # 组织游戏按分类
    games_by_category = organize_games_by_category(games)
    
    # 读取首页模板
    try:
        with codecs.open(index_path, 'r', encoding='utf-8-sig') as f:
            content = f.read()
    except Exception as e:
        print(f"Error reading index file: {e}")
        return False
    
    # 更新每个分类区域的游戏卡片
    for category in games_by_category:
        category_games = games_by_category[category]
        if not category_games:
            continue
        
        # 为该分类生成所有游戏卡片的HTML
        games_html = ''.join([create_game_card_html(game) for game in category_games])
        
        # 查找该分类的容器并更新内容
        category_pattern = f'<section class="game-section mb-6" data-category="{category}">.*?<div class="games-grid-compact">(.*?)</div>.*?</section>'
        section_pattern = re.compile(category_pattern, re.DOTALL)
        
        # 替换分类下的游戏卡片
        if section_pattern.search(content):
            content = section_pattern.sub(
                f'<section class="game-section mb-6" data-category="{category}">'
                f'<div class="flex items-center justify-between mb-3">'
                f'<h2 class="game-section-title text-xl font-bold text-white">{category.title()} Games</h2>'
                f'<a href="/category/{category}.html" class="text-blue-primary hover:text-blue-secondary text-sm">View more</a>'
                f'</div>'
                f'<div class="games-grid-compact">{games_html}</div></section>',
                content
            )
    
    # 更新首页
    try:
        with codecs.open(index_path, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Successfully updated index page with games")
        return True
    except Exception as e:
        print(f"Error updating index file: {e}")
        return False

def main():
    success = update_index_page()
    if success:
        print("首页游戏更新完成")
    else:
        print("首页游戏更新失败")

if __name__ == "__main__":
    main() 