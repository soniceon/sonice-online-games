#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import re
import json
import codecs
from collections import defaultdict
import shutil
from bs4 import BeautifulSoup
from urllib.parse import urljoin

def slugify(text):
    """将文本转换为URL友好的slug格式"""
    # 移除非字母数字字符，并转换为小写
    text = re.sub(r'[^\w\s-]', '', text.lower())
    # 将空格替换为连字符
    text = re.sub(r'[\s]+', '-', text)
    return text

def get_category_icon(category):
    """为分类返回合适的图标"""
    icons = {
        'action': '🎮',
        'adventure': '🗺️',
        'puzzle': '🧩',
        'strategy': '♟️',
        'sports': '⚽',
        'racing': '🏎️',
        'arcade': '👾',
        'shooting': '🎯',
        'clicker': '🖱️',
        'idle': '⏳',
        'tycoon': '💰',
        'simulation': '🏭',
        'merge': '🔄',
        'mining': '⛏️',
        'other': '🎲'
    }
    return icons.get(category.lower(), '🎮')

def get_category_description(category):
    """为分类返回合适的描述"""
    descriptions = {
        'action': '体验刺激的动作游戏，享受紧张刺激的游戏玩法和挑战。',
        'adventure': '踏上史诗般的冒险之旅，探索迷人的游戏世界。',
        'puzzle': '挑战你的思维，体验引人入胜的益智游戏。',
        'strategy': '测试你的战术技能，体验策略性的游戏玩法。',
        'sports': '参与各种体育模拟游戏，展示你的运动天赋。',
        'racing': '感受速度与激情，体验刺激的赛车游戏。',
        'arcade': '享受经典街机风格的游戏，简单但令人上瘾的游戏玩法。',
        'shooting': '测试你的瞄准能力和反应速度。',
        'clicker': '简单但令人上瘾的点击游戏，不断升级和解锁新内容。',
        'idle': '放置类游戏，即使不玩也能持续进步的游戏体验。',
        'tycoon': '经营模拟游戏，建立和管理自己的商业帝国。',
        'simulation': '模拟真实世界的各种活动和系统。',
        'merge': '合并类游戏，通过合并相同物品来解锁升级和进步。',
        'mining': '挖矿类游戏，发掘资源、升级设备，享受挖掘乐趣。',
        'other': '发现独特多样的游戏，超越传统类别的精彩体验。'
    }
    return descriptions.get(category.lower(), f'免费玩最好的{category}游戏！无需下载，直接在浏览器中体验。')

def extract_game_info(file_path):
    """从游戏HTML文件中提取信息"""
    try:
        with codecs.open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
            
        # 使用BeautifulSoup解析HTML
        soup = BeautifulSoup(content, 'html.parser')
        
        # 提取标题
        title_tag = soup.select_one('title')
        title = title_tag.text.split('-')[0].strip() if title_tag else os.path.basename(file_path).replace('.html', '').replace('-', ' ').title()
        
        # 提取描述
        description_tag = soup.select_one('meta[name="description"]')
        description = description_tag['content'] if description_tag and 'content' in description_tag.attrs else f"Play {title} online for free!"
        
        # 尝试根据游戏名称猜测主要分类
        game_name_lower = title.lower()
        main_category = None
        if 'idle' in game_name_lower:
            main_category = "Idle"
        elif 'clicker' in game_name_lower:
            main_category = "Clicker"
        elif 'merge' in game_name_lower:
            main_category = "Merge"
        elif 'mining' in game_name_lower or 'miner' in game_name_lower:
            main_category = "Mining"
        elif 'tycoon' in game_name_lower:
            main_category = "Tycoon"
        elif 'racing' in game_name_lower or 'drift' in game_name_lower:
            main_category = "Racing"
        elif 'shoot' in game_name_lower:
            main_category = "Shooting"
        
        # 提取分类
        categories = []
        
        # 查找游戏分类标签
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
                            genre_categories = [g.strip() for g in genre.split(',')]
                            for g in genre_categories:
                                if g:
                                    categories.append(g)
                        elif isinstance(genre, list):
                            for g in genre:
                                if not str(g).startswith('{{') and not str(g).startswith('{%'):
                                    categories.append(str(g))
                except Exception as e:
                    print(f"Error parsing JSON-LD for {file_path}: {e}")
        
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
        image_url = og_image['content'] if og_image and 'content' in og_image.attrs else f"/assets/images/games/{slug}-360-240.webp"
        
        # 确保图片URL有备用路径
        if not image_url.startswith(('http://', 'https://')):
            image_url = f"/assets/images/games/{slug}-360-240.webp"
        
        # 构建游戏URL
        game_url = f"/pages/games/{slug}.html"
        
        return {
            'title': title,
            'description': description,
            'categories': categories,
            'slug': slug,
            'image': image_url,
            'url': game_url,
            'content': content,
            'file_path': file_path
        }
    except Exception as e:
        print(f"Error extracting info from {file_path}: {e}")
        return None

def create_all_category_pages():
    """创建所有分类页面"""
    try:
        # 获取基础路径
        base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
        base_dir = os.path.dirname(base_dir)  # 再向上一级
        
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
            # 尝试备用模板路径
            alt_template_path = os.path.join(base_dir, 'src', 'templates', 'category-template.html')
            try:
                with open(alt_template_path, 'r', encoding='utf-8') as f:
                    template = f.read()
            except Exception as e2:
                print(f"Error reading alternate template: {e2}")
                return
        
        # 查找所有游戏HTML文件
        game_files = []
        if os.path.exists(games_dir):
            for filename in os.listdir(games_dir):
                if filename.endswith('.html'):
                    game_files.append(os.path.join(games_dir, filename))
        
        print(f"Found {len(game_files)} game files")
        
        # 收集游戏信息
        games_info = []
        
        for file_path in game_files:
            game_info = extract_game_info(file_path)
            if game_info:
                games_info.append(game_info)
        
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
                page_content = page_content.replace('{{category.icon}}', icon)
                
                # 游戏卡片HTML
                games_html = ""
                for game in games:
                    # 确保图片路径正确
                    image_path = game['image']
                    if not image_path.startswith(('http://', 'https://')):
                        image_path = f"/assets/images/games/{game['slug']}-360-240.webp"
                    
                    # 修剪描述
                    short_desc = game['description'][:100] + "..." if len(game['description']) > 100 else game['description']
                    
                    games_html += f'''
                    <div class="game-card">
                        <a href="{game['url']}">
                            <img src="{image_path}" 
                                 alt="{game['title']}" 
                                 onerror="this.onerror=null; this.src='/src/assets/images/games/{game['slug']}-360-240.webp'; 
                                         if(this.naturalHeight==0){{this.src='/assets/images/default-game.webp';}}">
                            <div class="game-card-content">
                                <h3>{game['title']}</h3>
                                <p>{short_desc}</p>
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
            
            # 创建分类索引页面
            create_categories_index_page(base_dir, categories_data)
            
        except Exception as e:
            print(f"Error creating categories index: {e}")
        
        print(f"\n分类页面生成完成:")
        print(f"成功: {success_count}")
        print(f"失败: {error_count}")
        
        return True
    except Exception as e:
        print(f"Error in create_all_category_pages: {e}")
        return False

def create_categories_index_page(base_dir, categories_data):
    """创建分类索引页面"""
    try:
        template_path = os.path.join(base_dir, 'src', 'templates', 'categories-index-template.html')
        output_path = os.path.join(base_dir, 'categories.html')
        
        # 读取模板
        try:
            with open(template_path, 'r', encoding='utf-8') as f:
                template = f.read()
        except Exception as e:
            print(f"Error reading categories index template: {e}")
            return False
            
        # 按游戏数量排序分类
        sorted_categories = sorted(
            categories_data.values(), 
            key=lambda x: x['count'], 
            reverse=True
        )
        
        # 构建分类卡片HTML
        categories_html = ""
        for category in sorted_categories:
            categories_html += f'''
            <div class="category-card">
                <a href="{category['url']}">
                    <div class="category-icon">{category['icon']}</div>
                    <div class="category-info">
                        <h3>{category['name']}</h3>
                        <span class="category-count">{category['count']} games</span>
                    </div>
                </a>
            </div>
            '''
            
        # 替换模板中的分类循环
        template = re.sub(
            r'{% for category in categories %}.*?{% endfor %}',
            categories_html,
            template,
            flags=re.DOTALL
        )
        
        # 写入分类索引页面
        with open(output_path, 'w', encoding='utf-8') as f:
            f.write(template)
            
        print(f"Created categories index page: {output_path}")
        return True
        
    except Exception as e:
        print(f"Error creating categories index page: {e}")
        return False

def main():
    create_all_category_pages()

if __name__ == "__main__":
    main() 