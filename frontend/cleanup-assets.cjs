// Cleanup old built assets before build
const fs = require('fs');
const path = require('path');

const assetsDir = path.join(__dirname, '../public/assets');

if (fs.existsSync(assetsDir)) {
    const files = fs.readdirSync(assetsDir);
    
    files.forEach(file => {
        // Only delete JS and CSS files (not images or other assets)
        if (file.endsWith('.js') || file.endsWith('.css')) {
            fs.unlinkSync(path.join(assetsDir, file));
            console.log(`Deleted: ${file}`);
        }
    });
    
    console.log('✓ Old assets cleaned');
} else {
    console.log('Assets directory not found, skipping cleanup');
}
