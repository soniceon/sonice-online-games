import os
import glob
import re
import csv
import codecs
import random
import json
from collections import defaultdict

def read_iframe_urls(csv_path):
    """从CSV文件读取游戏iframe URLs"""
    iframe_urls = {}
    
    # 使用codecs防止BOM问题
    with codecs.open(csv_path, 'r', encoding='utf-8-sig') as f:
        reader = csv.reader(f)
        next(reader)  # 跳过表头
        for row in reader:
            if len(row) >= 2:
                game_title = row[0].strip()
                iframe_url = row[1].strip()
                # 将游戏标题转换为小写并删除空格，用作键
                key = re.sub(r'[^a-z0-9]', '', game_title.lower())
                iframe_urls[key] = iframe_url
                # 同时用标题的各种变体作为备用键
                slug_key = game_title.lower().replace(' ', '-')
                iframe_urls[slug_key] = iframe_url
    
    return iframe_urls

def extract_game_info(file_path):
    """从游戏页面提取游戏信息"""
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
    
    # 尝试提取游戏概述
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

def get_iframe_url(game_info, iframe_urls):
    """根据游戏信息获取iframe URL"""
    title = game_info['title']
    slug = game_info['slug']
    
    # 将游戏标题转换为键格式
    title_key = re.sub(r'[^a-z0-9]', '', title.lower())
    slug_key = slug.lower()
    
    # 尝试直接匹配标题
    if title_key in iframe_urls:
        return iframe_urls[title_key]
    
    # 尝试匹配slug
    if slug_key in iframe_urls:
        return iframe_urls[slug_key]
    
    # 尝试部分匹配
    for k, url in iframe_urls.items():
        if k in title_key or title_key in k or k in slug_key or slug_key in k:
            return url
    
    # 未找到匹配项，返回默认URL
    return f"https://sonice.online/games/{slug}/"

def collect_all_games_info():
    """收集所有游戏信息，用于推荐相关游戏"""
    all_games = []
    game_pages = glob.glob('pages/games/*.html')
    
    for page in game_pages:
        try:
            game_info = extract_game_info(page)
            all_games.append(game_info)
        except Exception as e:
            print(f"收集游戏信息时出错 {page}: {str(e)}")
    
    # 按照分类整理游戏
    games_by_category = defaultdict(list)
    for game in all_games:
        for category in game['categories']:
            games_by_category[category].append(game)
    
    return all_games, games_by_category

def get_recommended_games(current_game, all_games, games_by_category, max_games=4):
    """为当前游戏获取推荐游戏列表"""
    recommended_games = []
    
    # 首先尝试从相同类别中获取推荐游戏
    category_games = []
    for category in current_game['categories']:
        category_games.extend([g for g in games_by_category[category] 
                              if g['slug'] != current_game['slug'] and g not in category_games])
    
    # 如果同类别游戏不足，从所有游戏中随机选择补充
    if len(category_games) >= max_games:
        recommended_games = random.sample(category_games, max_games)
    else:
        recommended_games = category_games
        other_games = [g for g in all_games 
                     if g['slug'] != current_game['slug'] and g not in recommended_games]
        if other_games:
            remaining_slots = max_games - len(recommended_games)
            recommended_games.extend(random.sample(other_games, 
                                                  min(remaining_slots, len(other_games))))
    
    return recommended_games

def update_game_page(file_path, template_path, iframe_urls, all_games, games_by_category):
    """使用模板和CSV数据更新游戏页面"""
    try:
        # 读取模板文件
        with open(template_path, 'r', encoding='utf-8') as f:
            template = f.read()
        
        # 获取游戏信息
        game_info = extract_game_info(file_path)
        
        # 获取iframe URL
        game_info['iframe_url'] = get_iframe_url(game_info, iframe_urls)
        
        # 获取推荐游戏
        recommended_games = get_recommended_games(game_info, all_games, games_by_category)
        
        print(f"处理: {file_path}")
        print(f"  标题: {game_info['title']}")
        print(f"  iframe URL: {game_info['iframe_url']}")
        
        # 创建新内容，替换所有模板变量
        new_content = template
        
        # 基本属性替换
        new_content = new_content.replace('{{game.title}}', game_info['title'])
        new_content = new_content.replace('{{game.description}}', game_info['description'])
        new_content = new_content.replace('{{game.slug}}', game_info['slug'])
        new_content = new_content.replace('{{game.controls}}', game_info['controls'])
        new_content = new_content.replace('{{game.iframeUrl}}', game_info['iframe_url'])
        new_content = new_content.replace('{{game.image}}', f"/src/assets/images/games/{game_info['slug']}.jpg")
        
        # 处理类别循环标签 - 保留分类区域
        category_pattern = r'{% for category in game\.categories %}(.*?){% endfor %}'
        category_match = re.search(category_pattern, new_content, re.DOTALL)
        
        if category_match:
            template_part = category_match.group(1)
            # 移除所有Jinja2控制语句，但保留内容
            template_part = re.sub(r'{% if not loop\.last %}.*?{% endif %}', '', template_part)
            
            # 为每个分类生成HTML
            categories_html = ""
            for category in game_info['categories']:
                categories_html += template_part.replace('{{category}}', category)
            
            # 替换整个循环块
            new_content = re.sub(category_pattern, categories_html, new_content, flags=re.DOTALL)
        
        # 处理推荐游戏循环标签
        rec_game_pattern = r'{% for rec_game in game\.recommendedGames %}(.*?){% endfor %}'
        rec_game_match = re.search(rec_game_pattern, new_content, re.DOTALL)
        
        if rec_game_match:
            rec_template_part = rec_game_match.group(1)
            rec_games_html = ""
            
            for rec_game in recommended_games:
                game_html = rec_template_part
                game_html = game_html.replace('{{rec_game.title}}', rec_game['title'])
                game_html = game_html.replace('{{rec_game.description}}', rec_game['description'])
                game_html = game_html.replace('{{rec_game.slug}}', rec_game['slug'])
                game_html = game_html.replace('{{rec_game.image}}', f"/src/assets/images/games/{rec_game['slug']}.jpg")
                # 修复游戏链接，确保正确跳转
                game_html = re.sub(
                    r'href="\/games\/\{\{rec_game\.slug\}\}\.html"', 
                    f'href="/pages/games/{rec_game["slug"]}.html"', 
                    game_html
                )
            
                rec_games_html += game_html
            
            # 替换整个循环块
            new_content = re.sub(rec_game_pattern, rec_games_html, new_content, flags=re.DOTALL)
        
        # 设置默认评分值
        new_content = new_content.replace('{{game.rating|default(4.5)}}', '4.5')
        new_content = new_content.replace('{{game.ratingCount|default(128)}}', '128')
        
        # 使用游戏概述替换description字段，保留原有的游戏介绍
        if 'overview' in game_info and game_info['overview'] != game_info['description']:
            # 找到游戏概述区域并替换内容
            overview_pattern = r'<div class="game-overview">\s*<h2>Game Overview</h2>\s*<p>.*?</p>'
            if re.search(overview_pattern, new_content, re.DOTALL):
                new_content = re.sub(
                    overview_pattern,
                    f'<div class="game-overview">\n        <h2>Game Overview</h2>\n        <p>{game_info["overview"]}</p>',
                    new_content,
                    flags=re.DOTALL
                )
        
        # 保存更新后的内容
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(new_content)
        
        return True
    except Exception as e:
        print(f"更新 {file_path} 时出错: {str(e)}")
        return False

def main():
    # 模板和CSV文件路径
    template_path = 'src/templates/game-template.html'
    csv_path = '游戏iframe.CSV'
    
    # 确认文件存在
    if not os.path.exists(template_path):
        print(f"错误: 模板文件 {template_path} 不存在!")
        return
    
    if not os.path.exists(csv_path):
        print(f"错误: CSV文件 {csv_path} 不存在!")
        return
    
    # 读取iframe URLs
    iframe_urls = read_iframe_urls(csv_path)
    print(f"从CSV文件加载了 {len(iframe_urls)} 个游戏iframe URL")
    
    # 收集所有游戏信息，用于推荐游戏
    all_games, games_by_category = collect_all_games_info()
    print(f"收集了 {len(all_games)} 个游戏信息，包含 {len(games_by_category)} 个分类")
    
    # 获取所有游戏HTML文件
    game_pages = glob.glob('pages/games/*.html')
    
    success_count = 0
    error_count = 0
    
    # 更新每个游戏页面
    for page in game_pages:
        if update_game_page(page, template_path, iframe_urls, all_games, games_by_category):
            success_count += 1
        else:
            error_count += 1
    
    print("\n更新完成!")
    print(f"成功更新: {success_count} 页面")
    print(f"更新失败: {error_count} 页面")

if __name__ == "__main__":
    main() 