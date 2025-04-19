const fs = require('fs');
const path = require('path');

// 示例游戏数据
const exampleGame = {
  title: "Falling Fruits",
  iframeUrl: "https://games.crazygames.com/en_US/falling-fruits/index.html",
  categories: ["Arcade", "Casual", "Puzzle"],
  description: "Falling Fruits is an addictive casual game where you need to catch falling fruits to score points. Watch out for bombs and other obstacles that might end your game early! Can you beat your high score?",
  controls: "Use your mouse or touch screen to control the basket. Move left and right to catch the falling fruits. Avoid bombs and special items that might have negative effects."
};

// 生成单个游戏页面
async function generateSingleGamePage() {
  // 确保输出目录存在
  const outputDir = path.join(__dirname, '../pages/games');
  if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true });
  }

  // 读取模板文件
  try {
    const templatePath = path.join(__dirname, '../src/templates/simple-game-template.html');
    let templateContent = fs.readFileSync(templatePath, 'utf8');
    
    // 为游戏生成slug（URL友好的文件名）
    const slug = exampleGame.title.toLowerCase().replace(/[^\w\s]/g, '').replace(/\s+/g, '-');
    
    // 生成分类标签HTML
    const categoriesHtml = exampleGame.categories.map(category => 
      `<span class="category-tag">${category}</span>`
    ).join('');
    
    // 用游戏数据替换模板中的占位符
    const htmlContent = templateContent
      .replace(/{{game.title}}/g, exampleGame.title)
      .replace(/{{game.description}}/g, exampleGame.description)
      .replace(/{{game.iframeUrl}}/g, exampleGame.iframeUrl)
      .replace(/{{game.controls}}/g, exampleGame.controls)
      .replace(/{{game.categories_html}}/g, categoriesHtml);
    
    // 写入生成的HTML文件
    const outputPath = path.join(outputDir, `${slug}.html`);
    fs.writeFileSync(outputPath, htmlContent);
    
    console.log(`Successfully generated sample game page: ${outputPath}`);
  } catch (error) {
    console.error('Error generating sample game page:', error);
  }
}

// 执行生成过程
generateSingleGamePage(); 