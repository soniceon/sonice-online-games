import os
import glob
import re
from collections import defaultdict

def extract_game_info(file_path):
    """从游戏页面提取游戏信息"""
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # 提取游戏名称
    title_match = re.search(r'<title>(.*?)(?:\s*-|\s*\|)', content)
    title = title_match.group(1).strip() if title_match else "Game"
    
    # 获取slug（从文件名）
    slug = os.path.splitext(os.path.basename(file_path))[0]
    
    return {
        'title': title,
        'slug': slug,
        'file_path': file_path
    }

def categorize_game(game_title, slug):
    """根据游戏标题和slug自动分类"""
    categories = []
    title_lower = game_title.lower()
    slug_lower = slug.lower()
    
    # 分类规则
    category_rules = {
        'idle': ['idle', 'tycoon', 'factory', 'business', 'company'],
        'clicker': ['clicker', 'click', 'tap'],
        'merge': ['merge', 'merging'],
        'mining': ['mine', 'mining', 'miner', 'dig'],
        'adventure': ['adventure', 'journey', 'quest'],
        'action': ['action', 'shooter', 'gun', 'shoot', 'weapon', 'impact', 'battle', 'heroic', 'monster'],
        'sports': ['basketball', 'soccer', 'football', 'sports', 'rider', 'sports', 'racing'],
        'racing': ['drift', 'race', 'racing', 'drive', 'road', 'car'],
        'puzzle': ['puzzle', 'brain', 'match', 'thinking', 'angles'],
        'strategy': ['strategy', 'tactics', 'defense', 'grow'],
        'simulation': ['simulator', 'simulation', 'tycoon'],
        'cards': ['card', 'cards'],
        'rpg': ['rpg', 'role', 'epic', 'adventure', 'sword', 'knight']
    }
    
    # 检查标题和slug中的关键词
    for category, keywords in category_rules.items():
        if any(keyword in title_lower for keyword in keywords) or any(keyword in slug_lower for keyword in keywords):
            categories.append(category)
    
    # 特殊规则：多个特定类别
    if 'idle' in title_lower or 'idle' in slug_lower:
        if 'idle' not in categories:
            categories.append('idle')
    
    if 'clicker' in title_lower or 'clicker' in slug_lower:
        if 'clicker' not in categories:
            categories.append('clicker')
    
    # 如果没有找到分类，设置为默认分类
    if not categories:
        categories.append('other')
    
    return categories

def update_categories_in_file(file_path, categories):
    """更新游戏文件中的分类标签"""
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # 更新JSON-LD中的genre字段
    genre_string = ", ".join(categories)
    content = re.sub(r'"genre":\s*"[^"]*"', f'"genre": "{genre_string}"', content)
    
    # 将分类信息保存回文件
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)

def main():
    # 获取所有游戏HTML文件
    game_pages = glob.glob('pages/games/*.html')
    print(f"找到 {len(game_pages)} 个游戏文件")
    
    # 用于统计分类情况
    category_stats = defaultdict(int)
    games_by_category = defaultdict(list)
    
    # 分析和分类每个游戏
    for page in game_pages:
        game_info = extract_game_info(page)
        categories = categorize_game(game_info['title'], game_info['slug'])
        
        print(f"游戏: {game_info['title']}")
        print(f"  分类: {', '.join(categories)}")
        
        # 更新文件中的分类
        update_categories_in_file(page, categories)
        
        # 更新统计
        for category in categories:
            category_stats[category] += 1
            games_by_category[category].append(game_info['title'])
    
    # 打印分类统计
    print("\n分类统计:")
    for category, count in sorted(category_stats.items(), key=lambda x: x[1], reverse=True):
        print(f"{category}: {count} 个游戏")
    
    # 输出完成消息
    print("\n游戏分类完成! 所有游戏页面已更新。")

if __name__ == "__main__":
    main() 