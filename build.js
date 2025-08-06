const fs = require('fs');
const path = require('path');

// 确保public目录存在
if (!fs.existsSync('public')) {
    fs.mkdirSync('public');
}

// 复制必要的文件到public目录
const filesToCopy = [
    'sitemap.xml',
    'robots.txt',
    'index.php',
    '_headers'
];

filesToCopy.forEach(file => {
    const sourcePath = path.join(__dirname, file);
    const destPath = path.join(__dirname, 'public', file);
    
    if (fs.existsSync(sourcePath)) {
        fs.copyFileSync(sourcePath, destPath);
        console.log(`Copied ${file} to public/`);
    }
});

console.log('Build completed successfully!'); 