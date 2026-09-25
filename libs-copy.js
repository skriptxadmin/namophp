const fs = require('fs');
const path = require('path');
const libs = require('./libs.conf');

for (const lib of libs) {

    for (const file of lib.files) {

        const source = path.resolve(file.src);
        const destination = path.resolve(
            `public/libs/${lib.name}/${file.dest}`
        );

        if (!fs.existsSync(source)) {
            console.warn(`⚠ File not found: ${file.src}`);
            continue;
        }

        fs.mkdirSync(path.dirname(destination), {
            recursive: true
        });

        fs.copyFileSync(source, destination);

        console.log(`✓ ${file.src} → ${destination}`);
    }
}