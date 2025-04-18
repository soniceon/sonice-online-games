#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import re
import glob
from collections import defaultdict
from bs4 import BeautifulSoup
import codecs
import shutil
import json

def extract_game_info(file_path):
    """从游戏HTML文件中提取信息"""
    try:
        with codecs.open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # 尝试从文件名推断主要分类
        filename = os.path.basename(file_path).replace('.html', '')
        main_category = None
        
        # 通过游戏名称猜测可能的分类
        lower_filename = filename.lower()
        category_hints = {
            'clicker': 'Clicker',
            'idle': 'Idle',
            'merge': 'Merge',
            'race': 'Racing',
            'racing': 'Racing',
            'shooter': 'Shooter',
            'action': 'Action',
            'puzzle': 'Puzzle',
            'card': 'Card',
            'adventure': 'Adventure',
            'simulation': 'Simulation',
            'strategy': 'Strategy',
            'arcade': 'Arcade',
            'sport': 'Sports'
        }
        
        for hint, category in category_hints.items():
            if hint in lower_filename:
                main_category = category
                break
        
        soup = BeautifulSoup(content, 'html.parser')
        
        # 提取标题
        title_tag = soup.select_one('title')
        title = title_tag.text.strip() if title_tag else ""
        title = re.sub(r' - Play Free Online Game', '', title)
        
        # 提取描述
        meta_desc = soup.select_one('meta[name="description"]')
        description = meta_desc['content'] if meta_desc and 'content' in meta_desc.attrs else ""
        
        # 提取分类
        categories = []
        category_elements = soup.select('.category-tag')
        for cat in category_elements:
            cat_text = cat.text.strip()
            # 确保不是模板变量
            if cat_text and not cat_text.startswith('{{') and not cat_text.startswith('{%'):
                categories.append(cat_text)
        
        # 如果没有找到分类，检查结构化数据
        if not categories:
            script_data = soup.select_one('script[type="application/ld+json"]')
            if script_data:
                try:
                    data = json.loads(script_data.string)
                    if "genre" in data:
                        genre = data["genre"]
                        # 确保不是模板变量
                        if isinstance(genre, str) and not genre.startswith('{{') and not genre.startswith('{%'):
                            genre_text = re.sub(r'<[^>]+>', '', genre)
                            if genre_text:
                                categories.append(genre_text)
                        elif isinstance(genre, list):
                            for g in genre:
                                if not str(g).startswith('{{') and not str(g).startswith('{%'):
                                    genre_text = re.sub(r'<[^>]+>', '', str(g))
                                    if genre_text:
                                        categories.append(genre_text)
                except:
                    pass
        
        # 如果仍然没有分类且我们猜测了一个分类，使用它
        if not categories and main_category:
            categories = [main_category]
        # 如果仍然没有分类，添加默认分类
        elif not categories:
            categories = ["Other"]
        
        # 提取slug
        slug = os.path.basename(file_path).replace('.html', '')
        
        # 提取图片URL
        og_image = soup.select_one('meta[property="og:image"]')
        image_url = og_image['content'] if og_image and 'content' in og_image.attrs else "/assets/images/default-game.jpg"
        
        return {
            'title': title,
            'description': description,
            'categories': categories,
            'slug': slug,
            'image': image_url,
            'url': f"/games/{slug}.html",
            'file_path': file_path,
            'content': content
        }
    except Exception as e:
        print(f"Error extracting info from {file_path}: {e}")
        return None

def update_game_categories(game_info):
    """更新游戏文件中的分类标签"""
    try:
        if not game_info or 'content' not in game_info:
            return False
            
        content = game_info['content']
        categories = game_info['categories']
        
        if not categories:
            return False
            
        # 替换{{category}}标签为实际分类
        main_category = categories[0]
        category_tag_html = f'<span class="category-tag">{main_category}</span>'
        
        # 替换分类标签
        content = content.replace('<span class="category-tag">{{category}}</span>', category_tag_html)
        
        # 更新JSON-LD中的genre字段
        json_ld_pattern = r'("genre"\s*:\s*)"<span class="category-tag">{{category}}</span>"'
        content = re.sub(json_ld_pattern, f'\\1"{main_category}"', content)
        
        # 写回文件
        with codecs.open(game_info['file_path'], 'w', encoding='utf-8') as f:
            f.write(content)
            
        return True
    except Exception as e:
        print(f"Error updating categories for {game_info['title']}: {e}")
        return False

def get_category_icon(category):
    """获取分类对应的图标"""
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
        "其他": "🎲",
        "Action": "🏃",
        "Adventure": "🧭",
        "Arcade": "🕹️",
        "Casual": "🎮",
        "Card": "🃏",
        "Classic": "🎯",
        "Creative": "💡",
        "Defense": "🛡️",
        "Driving": "🚗",
        "Educational": "📚",
        "Fighting": "👊",
        "Horror": "👻",
        "Multiplayer": "👥",
        "Platform": "🏃‍♂️",
        "Puzzle": "🧩",
        "Racing": "🏎️",
        "RPG": "🧙",
        "Shooter": "🔫",
        "Simulation": "🎛️",
        "Sports": "⚽",
        "Strategy": "♟️",
        "Word": "📝",
        "Clicker": "👆",
        "Idle": "⏳",
        "Merge": "🔄",
        "Other": "🎲"
    }
    return icon_map.get(category, "🎮")

def get_category_description(category):
    """获取分类对应的描述"""
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
        "其他": "各种有趣的游戏，涵盖多种类型和玩法。",
        "Action": "Fast-paced action games that test your reaction speed and operation skills.",
        "Adventure": "Explore unknown worlds and complete various quests in adventure games.",
        "Arcade": "Classic arcade-style games that recreate the fun of traditional game halls.",
        "Casual": "Simple and easy-to-learn casual games suitable for players of all ages.",
        "Card": "Various card collection and battle games that test strategy and luck.",
        "Classic": "Timeless classic games with gameplay that transcends time.",
        "Clicker": "Simple yet addictive games where clicking is the main mechanic.",
        "Idle": "Games that progress even when you're not actively playing.",
        "Merge": "Games where combining similar items creates better ones.",
        "Puzzle": "Brain-teasing puzzle games that challenge your intellect.",
        "Racing": "Exciting racing games that let you experience speed and thrills.",
        "Shooter": "Test your aiming skills in these targeting games.",
        "Simulation": "Games that simulate various real-life scenarios.",
        "Sports": "Various sports games that let you experience the joy of athletics.",
        "Strategy": "Games that require careful planning and strategic thinking.",
        "Other": "Various interesting games covering multiple types and gameplay styles."
    }
    
    # 如果没有找到对应描述，生成一个通用描述
    if category not in description_map:
        return f"Play the best {category} games online at sonice.online. Free browser games, no download required!"
    
    return description_map[category]

def slugify(text):
    """将文本转换为URL友好的slug格式"""
    text = text.lower()
    # 移除非字母数字字符
    slug = re.sub(r'[^\w\s-]', '', text)
    # 将空格替换为连字符
    slug = re.sub(r'[\s]+', '-', slug)
    # 移除多余的连字符
    slug = re.sub(r'-+', '-', slug)
    return slug

def main():
    base_dir = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
    games_dir = os.path.join(base_dir, 'pages', 'games')
    output_dir = os.path.join(base_dir, 'category')
    template_path = os.path.join(base_dir, 'src', 'templates', 'new-category-template.html')
    
    # 确保输出目录存在
    if not os.path.exists(output_dir):
        os.makedirs(output_dir)
    
    # 读取模板
    try:
        with open(template_path, 'r', encoding='utf-8') as f:
            template = f.read()
    except Exception as e:
        print(f"Error reading template: {e}")
        return
    
    # 查找所有游戏HTML文件
    game_files = []
    if os.path.exists(games_dir):
        for filename in os.listdir(games_dir):
            if filename.endswith('.html'):
                game_files.append(os.path.join(games_dir, filename))
    
    print(f"Found {len(game_files)} game files")
    
    # 更新游戏文件中的分类标签
    updated_count = 0
    games_info = []
    
    for file_path in game_files:
        game_info = extract_game_info(file_path)
        if game_info:
            games_info.append(game_info)
            if update_game_categories(game_info):
                updated_count += 1
    
    print(f"Updated {updated_count} game files with category information")
    
    # 按分类收集游戏
    categories = defaultdict(list)
    
    for game_info in games_info:
        for category in game_info['categories']:
            # 创建不包含原始内容的精简版game_info
            slim_game_info = {k: v for k, v in game_info.items() if k != 'content'}
            categories[category].append(slim_game_info)
    
    # 生成分类页面
    success_count = 0
    error_count = 0
    
    for category, games in categories.items():
        try:
            category_slug = slugify(category)
            icon = get_category_icon(category)
            description = get_category_description(category)
            
            # 按标题排序游戏
            games.sort(key=lambda x: x['title'])
            
            # 创建页面内容
            page_content = template
            # 替换变量
            page_content = page_content.replace('{{category.name}}', category)
            page_content = page_content.replace('{{category.slug}}', category_slug)
            page_content = page_content.replace('{{category.description}}', description)
            
            # 游戏卡片HTML
            games_html = ""
            for game in games:
                games_html += f'''
                <div class="game-card">
                    <a href="{game['url']}">
                        <img src="{game['image']}" alt="{game['title']}">
                        <div class="game-card-content">
                            <h3>{game['title']}</h3>
                            <p>{game['description'][:100]}...</p>
                            <div class="play-button">Play Now</div>
                        </div>
                    </a>
                </div>
                '''
            
            # 替换游戏循环部分
            page_content = re.sub(
                r'{% for game in category.games %}.*?{% endfor %}',
                games_html,
                page_content,
                flags=re.DOTALL
            )
            
            # 写入分类页面
            output_path = os.path.join(output_dir, f"{category_slug}.html")
            with open(output_path, 'w', encoding='utf-8') as f:
                f.write(page_content)
            
            success_count += 1
            print(f"Created category page: {output_path} with {len(games)} games")
        
        except Exception as e:
            print(f"Error creating category page for {category}: {e}")
            error_count += 1
    
    # 生成分类索引数据
    try:
        categories_data = {}
        for category, games in categories.items():
            category_slug = slugify(category)
            categories_data[category] = {
                "name": category,
                "slug": category_slug,
                "icon": get_category_icon(category),
                "count": len(games),
                "url": f"/category/{category_slug}.html"
            }
        
        # 写入分类索引数据
        data_dir = os.path.join(base_dir, 'data')
        if not os.path.exists(data_dir):
            os.makedirs(data_dir)
            
        with open(os.path.join(data_dir, 'categories.json'), 'w', encoding='utf-8') as f:
            json.dump({"categories": categories_data}, f, ensure_ascii=False, indent=2)
            
        print(f"Created categories index data with {len(categories_data)} categories")
    except Exception as e:
        print(f"Error creating categories index: {e}")
    
    print(f"\n分类页面生成完成:")
    print(f"成功: {success_count}")
    print(f"失败: {error_count}")

if __name__ == "__main__":
    main() 