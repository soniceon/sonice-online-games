import csv
import requests
import pandas as pd
from urllib.parse import urlparse
from concurrent.futures import ThreadPoolExecutor
import time

def check_url(url, timeout=10):
    try:
        # 解析URL
        parsed = urlparse(url)
        if not parsed.scheme:
            url = 'https://' + url

        # 发送HEAD请求检查URL可访问性
        response = requests.head(url, timeout=timeout, allow_redirects=True)
        return response.status_code == 200
    except Exception as e:
        print(f"Error checking {url}: {str(e)}")
        return False

def check_games(csv_file):
    # 读取游戏数据
    games = []
    inaccessible_games = []
    
    with open(csv_file, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f, delimiter='\t')
        for row in reader:
            games.append(row)
    
    print(f"\n开始检查 {len(games)} 个游戏的可访问性...")
    
    # 使用线程池加速检查过程
    with ThreadPoolExecutor(max_workers=5) as executor:
        for game in games:
            iframe_url = game['iframe url']
            if not check_url(iframe_url):
                inaccessible_games.append({
                    'title': game['title'],
                    'iframe_url': iframe_url,
                    'image': game['image-src'],
                    'url': game['link']
                })
            time.sleep(0.5)  # 添加延迟以避免请求过快
    
    # 生成不可访问游戏的报告
    if inaccessible_games:
        print(f"\n发现 {len(inaccessible_games)} 个不可访问的游戏:")
        df = pd.DataFrame(inaccessible_games)
        report_file = 'inaccessible_games.xlsx'
        df.to_excel(report_file, index=False)
        print(f"详细报告已保存到: {report_file}")
        
        # 创建新的CSV文件，排除不可访问的游戏
        accessible_games = [game for game in games 
                          if game['title'] not in [g['title'] for g in inaccessible_games]]
        
        output_file = 'games_filtered.csv'
        with open(output_file, 'w', encoding='utf-8', newline='') as f:
            writer = csv.DictWriter(f, fieldnames=games[0].keys(), delimiter='\t')
            writer.writeheader()
            writer.writerows(accessible_games)
        print(f"已生成清理后的游戏列表: {output_file}")
        
        return len(inaccessible_games)
    else:
        print("\n所有游戏URL都可以正常访问！")
        return 0

if __name__ == '__main__':
    # 安装所需的包
    try:
        import requests
    except ImportError:
        print("正在安装所需的包...")
        import subprocess
        subprocess.check_call(['pip', 'install', 'requests'])
        import requests
    
    inaccessible_count = check_games('crazygames2.csv')
    print(f"\n检查完成！发现 {inaccessible_count} 个不可访问的游戏。") 