import os
import glob
import re

def get_game_info(file_path):
    """从现有的游戏页面中提取游戏信息"""
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
        
    # 提取标题
    title_match = re.search(r'<title>(.*?) -', content)
    title = title_match.group(1) if title_match else "Game"
    
    # 提取描述
    desc_match = re.search(r'<meta name="description" content="([^"]*)"', content)
    description = desc_match.group(1) if desc_match else f"Play {title} at sonice.online - Free browser game, no download required!"
    
    # 提取分类
    category_match = re.search(r'"genre":\s*"([^"]*)"', content)
    category = category_match.group(1) if category_match else "Action"
    
    # 获取slug（从文件名）
    slug = os.path.splitext(os.path.basename(file_path))[0]
    
    return {
        'title': title,
        'description': description,
        'category': category,
        'slug': slug
    }

def update_game_page(file_path):
    """更新单个游戏页面，保留模板结构，只更新特定内容"""
    try:
        # 读取源模板文件
        template_path = 'src/templates/game-template.html'
        with open(template_path, 'r', encoding='utf-8') as f:
            template_content = f.read()
        
        # 获取游戏信息
        game_info = get_game_info(file_path)
        
        # 替换模板中的占位符
        new_content = template_content
        
        # 替换游戏标题
        new_content = re.sub(r'{{game\.title}}', game_info['title'], new_content)
        
        # 替换游戏描述
        new_content = re.sub(r'{{game\.description}}', game_info['description'], new_content)
        
        # 替换游戏分类（如果有多个分类的情况）
        if '{% for category in game.categories %}' in new_content:
            category_pattern = r'{% for category in game\.categories %}(.*?){% endfor %}'
            category_content = re.search(category_pattern, new_content, re.DOTALL)
            if category_content:
                category_template = category_content.group(1)
                category_html = category_template.replace('{{category}}', game_info['category'])
                new_content = re.sub(category_pattern, category_html, new_content, flags=re.DOTALL)
        
        # 替换游戏 iframe URL
        new_content = re.sub(r'{{game\.iframeUrl}}', f"https://sonice.online/games/{game_info['slug']}/", new_content)
        
        # 替换游戏控制说明（如果有）
        new_content = re.sub(r'{{game\.controls}}', f"Use mouse and keyboard to play {game_info['title']}", new_content)
        
        # 替换其他可能的游戏相关占位符
        new_content = re.sub(r'{{game\.slug}}', game_info['slug'], new_content)
        new_content = re.sub(r'{{game\.image}}', f"https://sonice.online/src/assets/images/games/{game_info['slug']}.jpg", new_content)
        
        # 处理评分相关的占位符
        new_content = re.sub(r'{{game\.rating\|default\(.*?\)}}', "4.5", new_content)
        new_content = re.sub(r'{{game\.ratingCount\|default\(.*?\)}}', "128", new_content)
        
        # 写入文件
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(new_content)
            
        print(f"Updated: {file_path}")
        return True
    except Exception as e:
        print(f"Error updating {file_path}: {str(e)}")
        return False

def main():
    """主函数"""
    # 获取所有游戏页面
    game_pages = glob.glob('pages/games/*.html')
    
    success_count = 0
    error_count = 0
    
    for page in game_pages:
        if update_game_page(page):
            success_count += 1
        else:
            error_count += 1
    
    print(f"\nUpdate completed!")
    print(f"Successfully updated: {success_count} pages")
    print(f"Failed to update: {error_count} pages")

if __name__ == '__main__':
    main() 