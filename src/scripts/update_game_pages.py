import os
import re
from pathlib import Path

def extract_game_info(html_content):
    """从现有游戏页面提取游戏信息"""
    title_match = re.search(r'<title>(.*?) - Play Free Online Game</title>', html_content)
    description_match = re.search(r'<meta name="description" content="(.*?)">', html_content)
    genre_match = re.search(r'"genre": "(.*?)"', html_content)
    image_match = re.search(r'"image": "(.*?)"', html_content)
    
    # 从genre中提取分类
    genres = []
    if genre_match:
        genres = [g.strip() for g in genre_match.group(1).split(',')]
    
    # 如果没有找到分类，从标题和描述中推断
    if not genres:
        if 'shooter' in html_content.lower() or 'cs' in html_content.lower():
            genres = ['Action', 'Shooter', 'Tactical']
        elif 'idle' in html_content.lower() or 'clicker' in html_content.lower():
            genres = ['Idle', 'Clicker']
        elif 'racing' in html_content.lower() or 'drift' in html_content.lower():
            genres = ['Racing', 'Sports']
        else:
            genres = ['Action']
    
    return {
        'title': title_match.group(1) if title_match else '',
        'description': description_match.group(1) if description_match else '',
        'genres': genres,
        'image': image_match.group(1) if image_match else '/src/assets/images/games/default.jpg'
    }

def create_new_game_page(game_info, template_path):
    """使用模板创建新的游戏页面"""
    with open(template_path, 'r', encoding='utf-8') as f:
        template = f.read()
    
    # 替换基本信息
    new_content = template.replace('{{game.title}}', game_info['title'])
    new_content = new_content.replace('{{game.description}}', game_info['description'])
    new_content = new_content.replace('{{game.image}}', game_info['image'])
    
    # 处理分类循环
    categories_html = ''
    for category in game_info['genres']:
        categories_html += f'<span class="category-tag">{category}</span>'
    
    # 替换分类循环
    new_content = re.sub(
        r'{%\s*for category in game\.categories\s*%}.*?{%\s*endfor\s*%}',
        categories_html,
        new_content,
        flags=re.DOTALL
    )
    
    # 替换结构化数据中的分类
    genres_str = ', '.join(game_info['genres'])
    new_content = re.sub(
        r'("genre":\s*)"[^"]*"',
        f'\\1"{genres_str}"',
        new_content
    )
    
    # 替换其他变量
    slug = game_info['title'].lower().replace(' ', '-')
    new_content = new_content.replace('{{game.slug}}', slug)
    new_content = new_content.replace('{{game.iframeUrl}}', f'https://sonice.online/games/{slug}/')
    
    # 根据游戏类型设置不同的控制说明
    if 'Shooter' in game_info['genres']:
        controls = 'Use WASD to move, mouse to aim and shoot, R to reload, and Space to jump.'
    elif 'Racing' in game_info['genres']:
        controls = 'Use arrow keys or WASD to control your vehicle. Space for handbrake.'
    else:
        controls = 'Use your mouse to play this game.'
    new_content = new_content.replace('{{game.controls}}', controls)
    
    new_content = new_content.replace('{{game.rating|default(4.5)}}', '4.5')
    new_content = new_content.replace('{{game.ratingCount|default(128)}}', '128')
    
    # 处理推荐游戏
    recommended_games_html = '''
        <div class="game-card">
            <img src="/src/assets/images/games/default.jpg" alt="Recommended Game">
            <div class="game-card-content">
                <h3>Similar Game</h3>
                <p>Try another exciting game in the same category!</p>
                <a href="#" class="play-button">Play Now</a>
            </div>
        </div>
    '''
    new_content = re.sub(
        r'{%\s*for rec_game in game\.recommendedGames\s*%}.*?{%\s*endfor\s*%}',
        recommended_games_html * 4,  # 显示4个推荐游戏
        new_content,
        flags=re.DOTALL
    )
    
    return new_content

def main():
    # 设置路径
    games_dir = Path('pages/games')
    template_path = Path('src/templates/game-template.html')
    
    # 确保模板文件存在
    if not template_path.exists():
        print(f"Error: Template file not found at {template_path}")
        return
    
    # 处理每个游戏页面
    for game_file in games_dir.glob('*.html'):
        print(f"Processing {game_file.name}...")
        
        # 读取现有游戏页面
        with open(game_file, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # 提取游戏信息
        game_info = extract_game_info(content)
        
        # 创建新的游戏页面
        new_content = create_new_game_page(game_info, template_path)
        
        # 保存新的游戏页面
        with open(game_file, 'w', encoding='utf-8') as f:
            f.write(new_content)
        
        print(f"Updated {game_file.name}")

if __name__ == '__main__':
    main() 