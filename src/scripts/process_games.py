import csv
import json
import pandas as pd
from collections import defaultdict

def categorize_games(csv_file):
    # 游戏分类规则
    categories = {
        'idle': ['idle', 'tycoon', 'clicker', 'merge'],
        'mining': ['mine', 'miner', 'mining'],
        'adventure': ['adventure', 'quest', 'journey'],
        'simulation': ['simulator', 'factory', 'business'],
        'puzzle': ['puzzle', 'match', 'brain'],
    }
    
    games_by_category = defaultdict(list)
    all_games = []
    
    with open(csv_file, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f, delimiter='\t')
        for row in reader:
            game = {
                'title': row['title'],
                'image': row['image-src'],
                'url': row['link'],
                'iframe_url': row['iframe url']
            }
            
            # 根据游戏标题判断类别
            title_lower = row['title'].lower()
            categorized = False
            
            for category, keywords in categories.items():
                if any(keyword in title_lower for keyword in keywords):
                    game['category'] = category
                    games_by_category[category].append(game)
                    categorized = True
                    break
            
            if not categorized:
                game['category'] = 'other'
                games_by_category['other'].append(game)
            
            all_games.append(game)
    
    # 将分类结果保存为JSON
    result = {
        'categories': dict(games_by_category),
        'total_games': sum(len(games) for games in games_by_category.values())
    }
    
    with open('categorized_games.json', 'w', encoding='utf-8') as f:
        json.dump(result, f, indent=2, ensure_ascii=False)
    
    # 创建Excel文件
    df = pd.DataFrame(all_games)
    
    # 创建一个Excel写入器
    with pd.ExcelWriter('games_catalog.xlsx', engine='openpyxl') as writer:
        # 写入所有游戏表
        df.to_excel(writer, sheet_name='所有游戏', index=False)
        
        # 为每个类别创建单独的表
        for category in games_by_category:
            category_df = pd.DataFrame(games_by_category[category])
            category_df.to_excel(writer, sheet_name=category, index=False)
        
        # 创建统计表
        stats = pd.DataFrame({
            '类别': list(games_by_category.keys()),
            '游戏数量': [len(games) for games in games_by_category.values()]
        })
        stats.loc[len(stats)] = ['总计', result['total_games']]
        stats.to_excel(writer, sheet_name='统计', index=False)
    
    return result

if __name__ == '__main__':
    result = categorize_games('crazygames2.csv')
    
    # 打印分类统计
    print("\n游戏分类统计:")
    for category, games in result['categories'].items():
        print(f"{category}: {len(games)} 个游戏")
    print(f"\n总计: {result['total_games']} 个游戏")
    print("\nExcel文件已生成: games_catalog.xlsx") 