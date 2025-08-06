import os
import glob
import re
import csv
import codecs
import random
import json
from collections import defaultdict

def read_iframe_urls(csv_path):
    """从CSV文件读取游戏iframe URLs和标题"""
    iframe_urls = {}
    game_titles = {}
    
    try:
        # 使用codecs防止BOM问题
        with codecs.open(csv_path, 'r', encoding='utf-8-sig') as f:
            reader = csv.reader(f)
            next(reader)  # 跳过表头
            for row in reader:
                if len(row) >= 2:
                    game_title = row[0].strip()
                    iframe_url = row[1].strip()
                    
                    # 保存标题和iframe URL
                    key = game_title.lower().replace(' ', '-')
                    iframe_urls[key] = iframe_url
                    game_titles[key] = game_title
                    
                    # 同时用标题的各种变体作为备用键
                    no_space_key = re.sub(r'[^a-z0-9]', '', game_title.lower())
                    iframe_urls[no_space_key] = iframe_url
                    game_titles[no_space_key] = game_title
        
        print(f"从CSV文件加载了 {len(iframe_urls)} 个游戏iframe URL")
        return iframe_urls, game_titles
    except Exception as e:
        print(f"读取CSV文件时出错: {str(e)}")
        return {}, {}

def extract_game_info(file_path):
    """从游戏页面提取游戏信息"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # 提取游戏名称
        title_match = re.search(r'<title>(.*?)(?:\s*-|\s*\|)', content)
        title = title_match.group(1).strip() if title_match else "Game"
        
        # 提取游戏描述
        desc_match = re.search(r'<meta name="description" content="([^"]*)"', content)
        description = desc_match.group(1) if desc_match else f"Play {title} online for free."
        
        # 提取游戏分类
        category_match = re.search(r'"genre":\s*"([^"]*)"', content)
        category = category_match.group(1).split(',')[0].strip() if category_match else "Action"
        categories = [cat.strip() for cat in category.split(',')] if ',' in category else [category]
        
        # 获取slug（从文件名）
        slug = os.path.splitext(os.path.basename(file_path))[0]
        
        # 尝试提取控制说明
        controls_match = re.search(r'<div class="game-controls">\s*<h2>Game Controls</h2>\s*<p>(.*?)</p>', content, re.DOTALL)
        controls = controls_match.group(1).strip() if controls_match else "Use mouse to play."
        
        # 提取游戏概述
        overview_match = re.search(r'<div class="game-overview">\s*<h2>Game Overview</h2>\s*<p>(.*?)</p>', content, re.DOTALL)
        overview = overview_match.group(1).strip() if overview_match else description
        
        return {
            'title': title,
            'description': description,
            'categories': categories,
            'slug': slug,
            'controls': controls,
            'overview': overview
        }
    except Exception as e:
        print(f"提取游戏信息时出错 {file_path}: {str(e)}")
        return {
            'title': os.path.splitext(os.path.basename(file_path))[0].replace('-', ' ').title(),
            'description': "Play this game online for free!",
            'categories': ["Game"],
            'slug': os.path.splitext(os.path.basename(file_path))[0],
            'controls': "Use mouse to play.",
            'overview': "Play this game online for free!"
        }

def get_iframe_url(game_info, iframe_urls):
    """获取游戏的iframe URL"""
    slug = game_info['slug'].lower()
    title = game_info['title'].lower()
    
    # 尝试多种匹配方法
    if slug in iframe_urls:
        return iframe_urls[slug]
    
    no_space_slug = re.sub(r'[^a-z0-9]', '', slug)
    if no_space_slug in iframe_urls:
        return iframe_urls[no_space_slug]
    
    no_space_title = re.sub(r'[^a-z0-9]', '', title)
    if no_space_title in iframe_urls:
        return iframe_urls[no_space_title]
    
    # 尝试部分匹配
    for key in iframe_urls:
        if key in slug or slug in key:
            return iframe_urls[key]
    
    # 返回当前的iframe URL（如果有的话）
    return game_info.get('current_iframe_url', '')

def collect_all_games_info():
    """收集所有游戏信息并按分类整理"""
    games_by_category = defaultdict(list)
    all_games = []
    
    game_pages = glob.glob('pages/games/*.html')
    
    for page in game_pages:
        game_info = extract_game_info(page)
        all_games.append(game_info)
        
        # 将游戏添加到每个分类中
        for category in game_info['categories']:
            games_by_category[category].append(game_info)
    
    return all_games, games_by_category

def get_recommended_games(current_slug, games_by_category, all_games, count=4):
    """基于当前游戏的分类，获取推荐游戏"""
    # 找到当前游戏
    current_game = None
    for game in all_games:
        if game['slug'] == current_slug:
            current_game = game
            break
    
    if not current_game:
        # 随机返回几个游戏
        return random.sample(all_games, min(count, len(all_games)))
    
    # 收集同类游戏
    similar_games = []
    for category in current_game['categories']:
        for game in games_by_category[category]:
            if game['slug'] != current_slug and game not in similar_games:
                similar_games.append(game)
    
    # 如果类似游戏不够，随机添加一些
    if len(similar_games) < count:
        remaining_games = [g for g in all_games if g['slug'] != current_slug and g not in similar_games]
        similar_games.extend(random.sample(remaining_games, min(count - len(similar_games), len(remaining_games))))
    
    # 限制数量
    return similar_games[:count]

def update_game_page(file_path, template_path, iframe_urls, game_titles):
    """更新游戏页面中的标题、分类、描述和推荐游戏，处理游戏卡片图片问题"""
    try:
        # 获取游戏信息
        game_info = extract_game_info(file_path)
        slug = game_info['slug']
        
        # 读取模板
        with open(template_path, 'r', encoding='utf-8') as f:
            template = f.read()
        
        # 获取iframe URL
        iframe_url = get_iframe_url(game_info, iframe_urls)
        
        # 从CSV获取正确的游戏标题
        correct_title = game_titles.get(slug.lower(), game_info['title'])
        
        print(f"处理: {file_path}")
        print(f"  标题: {correct_title}")
        
        # 收集所有游戏信息
        all_games, games_by_category = collect_all_games_info()
        
        # 获取推荐游戏
        recommended_games = get_recommended_games(slug, games_by_category, all_games)
        
        # 准备推荐游戏HTML
        recommended_html = ""
        for rec_game in recommended_games:
            rec_slug = rec_game['slug']
            rec_title = rec_game['title']
            rec_description = rec_game['description'][:100] + "..." if len(rec_game['description']) > 100 else rec_game['description']
            
            # 检查并生成图片路径，包含备用路径
            img_paths = [
                f"/assets/images/games/{rec_slug}-360-240.webp",
                f"/src/assets/images/games/{rec_slug}-360-240.webp",
                f"../../assets/images/games/{rec_slug}-360-240.webp"
            ]
            
            # 构建游戏卡片HTML，包含图片错误处理
            recommended_html += f'''
            <div class="game-card">
                <img src="{img_paths[0]}" alt="{rec_title}" 
                     onerror="this.onerror=null; this.src='{img_paths[1]}'; 
                     if(this.naturalHeight==0){{this.src='/assets/images/default-game.webp';}}">
                <div class="game-card-content">
                    <h3>{rec_title}</h3>
                    <p>{rec_description}</p>
                    <a href="/pages/games/{rec_slug}.html" class="play-button">Play Now</a>
                </div>
            </div>'''
        
        # 替换模板变量
        content = template
        
        # 替换基本变量
        content = content.replace('{{game.title}}', correct_title)
        content = content.replace('{{game.description}}', game_info['description'])
        content = content.replace('{{game.slug}}', slug)
        content = content.replace('{{game.controls}}', game_info['controls'])
        content = content.replace('{{game.iframeUrl}}', iframe_url)
        content = content.replace('{{game.image}}', f"/assets/images/games/{slug}-360-240.webp")
        
        # 替换评分变量
        content = content.replace('{{game.rating|default(4.5)}}', '4.5')
        content = content.replace('{{game.ratingCount|default(128)}}', '128')
        
        # 处理游戏概述
        content = content.replace('<p>{{game.description}}</p>', f'<p>{game_info["overview"]}</p>', 1)
        
        # 处理分类循环
        category_matches = re.findall(r'{% for category in game\.categories %}(.*?){% endfor %}', content, re.DOTALL)
        for match in category_matches:
            category_template = re.sub(r'{%.*?%}', '', match).replace('{{category}}', '{}')
            categories_html = ''
            for category in game_info['categories']:
                categories_html += category_template.format(category)
            content = content.replace(f'{{% for category in game.categories %}}{match}{{% endfor %}}', categories_html)
        
        # 替换逗号分隔的分类列表（如在结构化数据中）
        genre_pattern = r'"genre": "{% for category in game\.categories %}{{category}}{% if not loop\.last %}, {% endif %}{% endfor %}"'
        genre_text = f'"genre": "{", ".join(game_info["categories"])}"'
        content = re.sub(genre_pattern, genre_text, content)
        
        # 替换推荐游戏部分
        rec_pattern = r'<div class="games-grid">\s*{% for rec_game in game\.recommendedGames %}.*?{% endfor %}\s*</div>'
        content = re.sub(rec_pattern, f'<div class="games-grid">{recommended_html}</div>', content, flags=re.DOTALL)
        
        # 修复任何剩余的模板变量
        content = re.sub(r'\{\{.*?\}\}', '', content)
        content = re.sub(r'\{%.*?%\}', '', content)
        
        # 保存更新后的内容
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        
        return True
    except Exception as e:
        print(f"更新 {file_path} 时出错: {str(e)}")
        return False

def main():
    # CSV文件路径
    csv_path = '游戏iframe.CSV'
    # 模板文件路径
    template_path = 'src/templates/game-template.html'
    
    # 确认文件存在
    if not os.path.exists(csv_path):
        print(f"错误: CSV文件 {csv_path} 不存在!")
        return
    
    if not os.path.exists(template_path):
        print(f"错误: 模板文件 {template_path} 不存在!")
        return
    
    # 读取iframe URLs和游戏标题
    iframe_urls, game_titles = read_iframe_urls(csv_path)
    
    # 获取所有游戏HTML文件
    game_pages = glob.glob('pages/games/*.html')
    
    success_count = 0
    error_count = 0
    
    # 更新每个游戏页面
    for page in game_pages:
        if update_game_page(page, template_path, iframe_urls, game_titles):
            success_count += 1
        else:
            error_count += 1
    
    print("\n更新完成!")
    print(f"成功更新: {success_count} 页面")
    print(f"更新失败: {error_count} 页面")

if __name__ == "__main__":
    main() 