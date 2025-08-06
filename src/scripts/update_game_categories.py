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
    
    # 提取游戏描述
    desc_match = re.search(r'<meta name="description" content="([^"]*)"', content)
    description = desc_match.group(1) if desc_match else f"Play {title} online for free."
    
    # 获取slug（从文件名）
    slug = os.path.splitext(os.path.basename(file_path))[0]
    
    # 尝试提取iframe URL以帮助分类
    iframe_match = re.search(r'<iframe src="([^"]*)"', content)
    iframe_url = iframe_match.group(1) if iframe_match else ""
    
    return {
        'title': title,
        'description': description,
        'slug': slug,
        'iframe_url': iframe_url,
        'file_path': file_path
    }

def categorize_game(game_info):
    """根据游戏标题和slug自动分类"""
    categories = []
    title_lower = game_info['title'].lower()
    slug_lower = game_info['slug'].lower()
    iframe_lower = game_info['iframe_url'].lower()
    
    # 分类规则
    category_rules = {
        'idle': ['idle', 'tycoon', 'factory', 'business', 'company', 'empire'],
        'clicker': ['clicker', 'click', 'tap'],
        'merge': ['merge', 'merging'],
        'mining': ['mine', 'mining', 'miner', 'dig', 'pickaxe', 'drill'],
        'adventure': ['adventure', 'journey', 'quest', 'firebo', 'watergirl'],
        'action': ['action', 'battle', 'heroic', 'monster', 'survival', 'impact', 'smash'],
        'shooter': ['shooter', 'gun', 'shoot', 'weapon', 'sniper', 'cs', 'special forces'],
        'sports': ['basketball', 'soccer', 'football', 'sports', 'dancer'],
        'racing': ['drift', 'race', 'racing', 'drive', 'road', 'car', 'hot road'],
        'puzzle': ['puzzle', 'brain', 'match', 'thinking', 'angles', 'fuser'],
        'strategy': ['strategy', 'tactics', 'defense', 'grow'],
        'simulation': ['simulator', 'simulation', 'tycoon', 'factory', 'business'],
        'cards': ['card', 'cards'],
        'rpg': ['rpg', 'role', 'epic', 'hero', 'sword', 'knight']
    }
    
    # 检查标题、slug和iframe URL中的关键词
    for category, keywords in category_rules.items():
        if any(keyword in title_lower for keyword in keywords) or \
           any(keyword in slug_lower for keyword in keywords) or \
           any(keyword in iframe_lower for keyword in keywords):
            categories.append(category)
    
    # 特殊规则
    if 'idle' in title_lower or 'idle' in slug_lower:
        if 'idle' not in categories:
            categories.append('idle')
    
    if 'clicker' in title_lower or 'clicker' in slug_lower:
        if 'clicker' not in categories:
            categories.append('clicker')
            
    if 'merge' in title_lower or 'merge' in slug_lower:
        if 'merge' not in categories:
            categories.append('merge')
    
    # 如果没有找到分类，设置为默认分类
    if not categories:
        categories.append('other')
    
    return categories

def update_categories_in_file(file_path, categories):
    """更新游戏文件中的分类标签"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # 更新JSON-LD中的genre字段
        genre_string = ", ".join(categories)
        content = re.sub(r'"genre":\s*"[^"]*"', f'"genre": "{genre_string}"', content)
        
        # 替换分类标签区域
        # 首先找到分类循环区域
        categories_pattern = r'{{% for category in game\.categories %}}(.*?){{% endfor %}}'
        categories_template_match = re.search(categories_pattern, content, re.DOTALL)
        
        if categories_template_match:
            categories_template = categories_template_match.group(1)
            # 去除loop相关条件判断
            categories_template = re.sub(r'{{% if not loop\.last %}}.*?{{% endif %}}', '', categories_template)
            
            # 构建分类HTML
            categories_html = ""
            for category in categories:
                # 替换模板中的{{category}}
                category_html = categories_template.replace('{{category}}', category)
                categories_html += category_html
            
            # 替换整个分类循环
            content = re.sub(categories_pattern, categories_html, content, flags=re.DOTALL)
        
        # 将更新后的内容保存回文件
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        
        return True
    except Exception as e:
        print(f"更新 {file_path} 时出错: {str(e)}")
        return False

def main():
    # 获取所有游戏HTML文件
    game_pages = glob.glob('pages/games/*.html')
    print(f"找到 {len(game_pages)} 个游戏文件")
    
    # 用于统计分类情况
    category_stats = defaultdict(int)
    games_by_category = defaultdict(list)
    
    success_count = 0
    error_count = 0
    
    # 分析和分类每个游戏
    for page in game_pages:
        try:
            # 提取游戏信息
            game_info = extract_game_info(page)
            
            # 自动分类
            categories = categorize_game(game_info)
            
            print(f"游戏: {game_info['title']}")
            print(f"  分类: {', '.join(categories)}")
            
            # 更新文件中的分类
            if update_categories_in_file(page, categories):
                success_count += 1
                
                # 更新统计
                for category in categories:
                    category_stats[category] += 1
                    games_by_category[category].append(game_info['title'])
            else:
                error_count += 1
        except Exception as e:
            print(f"处理 {page} 时出错: {str(e)}")
            error_count += 1
    
    # 打印分类统计
    print("\n分类统计:")
    for category, count in sorted(category_stats.items(), key=lambda x: x[1], reverse=True):
        print(f"{category}: {count} 个游戏")
    
    # 输出完成消息
    print("\n游戏分类完成!")
    print(f"成功更新: {success_count} 页面")
    print(f"更新失败: {error_count} 页面")

if __name__ == "__main__":
    main() 