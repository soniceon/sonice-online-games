import os
import glob
import re
import csv
import codecs

def read_iframe_urls(csv_path):
    """从CSV文件读取游戏iframe URLs和标题"""
    iframe_urls = {}
    game_titles = {}
    
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
    
    return iframe_urls, game_titles

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
    
    # 提取当前的iframe URL
    iframe_match = re.search(r'<iframe src="([^"]*)"', content)
    current_iframe_url = iframe_match.group(1) if iframe_match else ""
    
    return {
        'title': title,
        'description': description,
        'categories': categories,
        'slug': slug,
        'controls': controls,
        'current_iframe_url': current_iframe_url
    }

def get_game_title(slug, game_titles):
    """根据slug获取游戏标题"""
    slug_key = slug.lower()
    
    if slug_key in game_titles:
        return game_titles[slug_key]
    
    # 尝试部分匹配
    for k, title in game_titles.items():
        if k in slug_key or slug_key in k:
            return title
    
    # 如果找不到匹配，将slug转换为标题格式
    return ' '.join(word.capitalize() for word in slug.replace('-', ' ').split())

def get_game_categories(slug):
    """根据游戏slug确定游戏分类"""
    slug = slug.lower()
    
    if "idle" in slug:
        return ["Idle", "Clicker"]
    elif "clicker" in slug:
        return ["Clicker", "Arcade"]
    elif "merge" in slug:
        return ["Merge", "Puzzle"]
    elif "miner" in slug or "mining" in slug:
        return ["Mining", "Simulation"]
    elif "tycoon" in slug:
        return ["Tycoon", "Simulation"]
    elif "shooter" in slug or "shoot" in slug:
        return ["Shooter", "Action"]
    elif "puzzle" in slug:
        return ["Puzzle", "Brain"] 
    elif "racer" in slug or "racing" in slug or "drift" in slug:
        return ["Racing", "Sports"]
    elif "soccer" in slug or "football" in slug or "basketball" in slug:
        return ["Sports", "Ball"]
    else:
        return ["Game", "Arcade"]

def update_game_page(file_path, iframe_urls, game_titles):
    """只更新游戏页面中的标题、分类和描述，保持iframe URL不变"""
    try:
        # 获取游戏信息
        game_info = extract_game_info(file_path)
        
        # 读取文件内容
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # 获取slug（从文件名）
        slug = game_info['slug']
        
        # 从CSV获取正确的游戏标题
        correct_title = get_game_title(slug, game_titles)
        
        print(f"处理: {file_path}")
        print(f"  标题: {correct_title}")
        
        # 1. 更新标题 - 将 {{game.title}} 替换为实际标题
        content = re.sub(r'\{\{game\.title\}\}', correct_title, content)
        
        # 2. 更新描述 - 将 {{game.description}} 替换为实际描述
        description = f"Play {correct_title} online for free! Enjoy this addictive game directly in your browser, no download required."
        content = re.sub(r'\{\{game\.description\}\}', description, content)
        
        # 3. 替换分类循环模板
        categories = get_game_categories(slug)
        
        # 处理分类标签部分 (第一处)
        template_pattern = r'{% for category in game\.categories %}(.*?){% if not loop\.last %}.*?{% endif %}(.*?){% endfor %}'
        category_match = re.search(template_pattern, content, re.DOTALL)
        
        if category_match:
            category_html = ""
            category_template = category_match.group(1) + category_match.group(2)
            
            for category in categories:
                category_html += category_template.replace('{{category}}', category)
            
            content = re.sub(template_pattern, category_html, content, flags=re.DOTALL)
        
        # 处理第二处分类标签（在overview部分）
        overview_pattern = r'<div class="game-categories">\s*{% for category in game\.categories %}(.*?){% endfor %}\s*</div>'
        overview_match = re.search(overview_pattern, content, re.DOTALL)
        
        if overview_match:
            category_tag_template = overview_match.group(1)
            category_tags = ""
            
            for category in categories:
                category_tags += category_tag_template.replace('{{category}}', category)
            
            overview_html = f'<div class="game-categories">\n{category_tags}\n</div>'
            content = re.sub(overview_pattern, overview_html, content, flags=re.DOTALL)
        
        # 更新结构化数据中的genre
        genre_pattern = r'"genre":\s*"{% for category in game\.categories %}{{category}}{% if not loop\.last %}, {% endif %}{% endfor %}"'
        genre_text = f'"genre": "{", ".join(categories)}"'
        content = re.sub(genre_pattern, genre_text, content)
        
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
    
    # 确认文件存在
    if not os.path.exists(csv_path):
        print(f"错误: CSV文件 {csv_path} 不存在!")
        return
    
    # 读取iframe URLs和游戏标题
    iframe_urls, game_titles = read_iframe_urls(csv_path)
    print(f"从CSV文件加载了 {len(iframe_urls)} 个游戏iframe URL")
    
    # 获取所有游戏HTML文件
    game_pages = glob.glob('pages/games/*.html')
    
    success_count = 0
    error_count = 0
    
    # 更新每个游戏页面
    for page in game_pages:
        if update_game_page(page, iframe_urls, game_titles):
            success_count += 1
        else:
            error_count += 1
    
    print("\n更新完成!")
    print(f"成功更新: {success_count} 页面")
    print(f"更新失败: {error_count} 页面")

if __name__ == "__main__":
    main() 