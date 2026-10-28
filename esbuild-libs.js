const fs = require('fs');
const path = require('path');
const libs = require('./esbuild-libs-conf');

const outputDir = path.resolve('public/libs');

fs.mkdirSync(outputDir, { recursive: true });

function concat(files, output) {

    if (!files || files.length === 0) {
        return;
    }

    const contents = files.map(file => {

        const fullPath = path.resolve(file);

        if (!fs.existsSync(fullPath)) {
            throw new Error(`File not found: ${file}`);
        }

        return fs.readFileSync(fullPath, 'utf8');

    });

    fs.writeFileSync(
        path.join(outputDir, output),
        contents.join('\n;\n')
    );

    console.log(`✓ ${output}`);
}


for (const lib of libs) {

    concat(
        lib.css,
        `${lib.name}.css`
    );

    concat(
        lib.js,
        `${lib.name}.js`
    );
}

console.log('\nLibraries built successfully.');