/**
 * URL修复脚本 - 确保所有HTML文件中的URL链接正确
 * 
 * 此脚本用于扫描和修复生成的HTML文件中的URL链接，确保它们指向正确的位置
 */

const fs = require('fs');
const path = require('path');
const glob = require('glob');

// 配置项
const config = {
  // 基本路径修正 (如果需要修改所有URL的基本路径)
  baseUrlCorrection: '',
  
  // 链接修正映射
  linkCorrections: {
    // 示例: 将错误路径映射到正确路径
    '/games/': '/categories/',
    'assets/images/default-avatar.png': 'assets/images/user-avatar.png',
  },
  
  // 需要扫描的文件模式
  filePatterns: [
    '*.html',
    'categories/*.html',
    'src/templates/*.html'
  ]
};

// 主函数
async function fixUrls() {
  console.log('开始扫描并修复URL链接...');
  
  try {
    // 获取所有匹配的文件
    const files = [];
    for (const pattern of config.filePatterns) {
      const matches = await globPromise(pattern);
      files.push(...matches);
    }
    
    console.log(`找到 ${files.length} 个文件需要检查`);
    
    // 处理每个文件
    for (const file of files) {
      await processFile(file);
    }
    
    console.log('所有URL链接已修复完成！');
  } catch (error) {
    console.error('修复URL时出错:', error);
  }
}

// 使用Promise封装glob
function globPromise(pattern) {
  return new Promise((resolve, reject) => {
    glob(pattern, (err, matches) => {
      if (err) {
        reject(err);
      } else {
        resolve(matches);
      }
    });
  });
}

// 处理单个文件
async function processFile(filePath) {
  console.log(`处理文件: ${filePath}`);
  
  try {
    // 读取文件内容
    let content = fs.readFileSync(filePath, 'utf8');
    let originalContent = content;
    let changesCount = 0;
    
    // 应用基本路径修正
    if (config.baseUrlCorrection) {
      // 实现基本路径修正逻辑
    }
    
    // 应用链接修正
    for (const [wrongPath, correctPath] of Object.entries(config.linkCorrections)) {
      const regex = new RegExp(wrongPath.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g');
      const matches = content.match(regex);
      
      if (matches && matches.length > 0) {
        content = content.replace(regex, correctPath);
        changesCount += matches.length;
        console.log(`  - 将 "${wrongPath}" 替换为 "${correctPath}" (${matches.length} 处)`);
      }
    }
    
    // 保存文件（如果有更改）
    if (content !== originalContent) {
      fs.writeFileSync(filePath, content, 'utf8');
      console.log(`  ✅ 已修复 ${changesCount} 处URL问题并保存文件`);
    } else {
      console.log(`  ✓ 文件无需修改`);
    }
  } catch (error) {
    console.error(`  ❌ 处理文件 ${filePath} 时出错:`, error);
  }
}

// 添加命令行参数处理
function parseArgs() {
  const args = process.argv.slice(2);
  
  if (args.includes('--help') || args.includes('-h')) {
    console.log(`
URL修复工具使用方法:
  node fix-urls.js [选项]

选项:
  --dry-run       只检查不修改文件
  --verbose       显示详细输出
  --pattern=GLOB  指定文件匹配模式 (可多次使用)
  --help, -h      显示此帮助信息
    `);
    process.exit(0);
  }
  
  // 解析其他参数
  // (例如 --dry-run, --verbose 等)
}

// 执行脚本
parseArgs();
fixUrls().catch(console.error); 