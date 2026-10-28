/**
 * Automatic JS + SCSS build system
 */

const path = require("path");
const fs = require("fs");
const esbuild = require("esbuild");
const chokidar = require("chokidar");
const sass = require("sass");

// --------------------------------------------------
// Paths
// --------------------------------------------------

const root = path.resolve(__dirname, ".");

const scriptsRoot = path.join(root, "src/Scripts");
const stylesRoot = path.join(root, "src/Styles");

const publicScriptsRoot = path.join(root, "public/scripts");
const publicStylesRoot = path.join(root, "public/styles");

const isWatch = process.argv.includes("--watch");

// --------------------------------------------------
// Helpers
// --------------------------------------------------

function getRelativePath(file, base) {
    return path.relative(base, file).replace(/\\/g, "/");
}

function ensureDirectory(dir) {
    fs.mkdirSync(dir, {
        recursive: true
    });
}

function isJavaScriptFile(file) {
    const name = path.basename(file);

    if (!name.endsWith(".js")) {
        return false;
    }

    // Ignore minified and common backup files.
    if (
        name.endsWith(".min.js") ||
        name.endsWith(".backup.js") ||
        name.endsWith(".bak.js") ||
        name.endsWith(".old.js") ||
        name.endsWith(".orig.js") ||
        name.startsWith("~")
    ) {
        return false;
    }

    return true;
}

// --------------------------------------------------
// Find directories
// --------------------------------------------------

function findDirectories(dir) {
    if (!fs.existsSync(dir)) {
        return [];
    }

    const result = [];

    for (const item of fs.readdirSync(dir, {
        withFileTypes: true
    })) {
        if (!item.isDirectory()) {
            continue;
        }

        const fullPath = path.join(dir, item.name);

        result.push(fullPath);
        result.push(...findDirectories(fullPath));
    }

    return result;
}

// --------------------------------------------------
// Discover JS build entries
// --------------------------------------------------

function discoverJsEntries() {
    if (!fs.existsSync(scriptsRoot)) {
        return [];
    }

    const directories = [
        scriptsRoot,
        ...findDirectories(scriptsRoot)
    ];

    const entries = [];

    for (const directory of directories) {
        const files = fs.readdirSync(directory)
            .filter(isJavaScriptFile)
            .sort();

        if (!files.length) {
            continue;
        }

        // ------------------------------------------
        // Folder contains index.js
        // index.js controls all imports.
        // ------------------------------------------

        if (files.includes("index.js")) {
            const relativeDirectory = getRelativePath(
                directory,
                scriptsRoot
            );

            // Root index.js -> public/scripts.js
            // Nested index.js -> public/scripts/folder.js
            const outfile = directory === scriptsRoot
                ? path.join(publicScriptsRoot, "index.js")
                : path.join(
                    publicScriptsRoot,
                    `${relativeDirectory}.js`
                );

            entries.push({
                type: "bundle",
                directory,
                source: path.join(directory, "index.js"),
                outfile
            });

        } else {
            // ------------------------------------------
            // No index.js: build each JS file separately
            // ------------------------------------------

            for (const file of files) {
                const source = path.join(directory, file);

                const relative = getRelativePath(
                    source,
                    scriptsRoot
                );

                entries.push({
                    type: "file",
                    source,
                    outfile: path.join(
                        publicScriptsRoot,
                        relative
                    )
                });
            }
        }
    }

    return entries;
}

// --------------------------------------------------
// Build JavaScript
// --------------------------------------------------

async function buildJs(entry) {
    ensureDirectory(path.dirname(entry.outfile));

    // ------------------------------------------
    // Individual JS file
    // ------------------------------------------

    if (entry.type === "file") {
        await esbuild.build({
            entryPoints: [
                entry.source
            ],

            bundle: true,
            outfile: entry.outfile,
            format: "iife",
            sourcemap: isWatch,
            minify: !isWatch,
            target: ["es2018"],
            logLevel: "info"
        });

        return;
    }

    // ------------------------------------------
    // Folder bundle
    // index.js is the sole entry point.
    // Only its explicit imports are bundled.
    // ------------------------------------------

    await esbuild.build({
        entryPoints: [
            entry.source
        ],

        bundle: true,
        outfile: entry.outfile,
        format: "iife",
        sourcemap: isWatch,
        minify: !isWatch,
        target: ["es2018"],
        logLevel: "info"
    });
}

// --------------------------------------------------
// Build all JavaScript
// --------------------------------------------------

async function buildAllJs() {
    const entries = discoverJsEntries();

    console.log(
        `📦 Found ${entries.length} JavaScript build(s)`
    );

    for (const entry of entries) {
        console.log(
            `→ ${getRelativePath(entry.outfile, root)}`
        );

        await buildJs(entry);
    }
}

// --------------------------------------------------
// SCSS
// --------------------------------------------------

function buildCss() {
    const entry = path.join(
        stylesRoot,
        "app.scss"
    );

    const outfile = path.join(
        publicStylesRoot,
        "app.css"
    );

    if (!fs.existsSync(entry)) {
        return;
    }

    const result = sass.compile(entry, {
        style: isWatch
            ? "expanded"
            : "compressed",

        sourceMap: isWatch
    });

    ensureDirectory(path.dirname(outfile));

    fs.writeFileSync(
        outfile,
        result.css
    );

    if (isWatch && result.sourceMap) {
        fs.writeFileSync(
            `${outfile}.map`,
            JSON.stringify(result.sourceMap)
        );
    }

    console.log(
        `✓ ${getRelativePath(outfile, root)}`
    );
}

// --------------------------------------------------
// Debounce helper
// --------------------------------------------------

function debounce(fn, delay = 200) {
    let timer = null;

    return (...args) => {
        clearTimeout(timer);

        timer = setTimeout(() => {
            fn(...args);
        }, delay);
    };
}

// --------------------------------------------------
// Watch
// --------------------------------------------------

async function watch() {
    // ------------------------------------------
    // Initial build
    // ------------------------------------------

    console.log("\n🔨 Initial build...\n");

    await buildAllJs();
    buildCss();

    // ------------------------------------------
    // JS watcher
    // ------------------------------------------

    let jsBuildQueue = Promise.resolve();

    const rebuildJs = debounce((event, file) => {
        if (!isJavaScriptFile(file)) {
            return;
        }

        console.log(
            `\n↻ JS ${event}: ${getRelativePath(file, root)}`
        );

        // Queue builds to prevent overlapping esbuild runs.
        jsBuildQueue = jsBuildQueue
            .then(() => buildAllJs())
            .catch(error => {
                console.error(
                    "❌ JS build failed:",
                    error.message
                );
            });

    }, 200);

    const jsWatcher = chokidar.watch(
        scriptsRoot,
        {
            persistent: true,
            ignoreInitial: true,

            ignored: file => {
                if (typeof file !== "string") {
                    return false;
                }

                const name = path.basename(file);

                return (
                    name.endsWith(".min.js") ||
                    name.endsWith(".backup.js") ||
                    name.endsWith(".bak.js") ||
                    name.endsWith(".old.js") ||
                    name.endsWith(".orig.js") ||
                    name.startsWith("~")
                );
            },

            awaitWriteFinish: {
                stabilityThreshold: 150,
                pollInterval: 50
            }
        }
    );

    jsWatcher.on(
        "change",
        file => rebuildJs("changed", file)
    );

    jsWatcher.on(
        "add",
        file => rebuildJs("added", file)
    );

    jsWatcher.on(
        "unlink",
        file => rebuildJs("removed", file)
    );

    // ------------------------------------------
    // SCSS watcher
    // ------------------------------------------

    const rebuildCss = debounce(file => {
        console.log(
            `\n↻ SCSS changed: ${getRelativePath(file, root)}`
        );

        try {
            buildCss();
        } catch (error) {
            console.error(
                "❌ SCSS build failed:",
                error.message
            );
        }

    }, 200);

    const cssWatcher = chokidar.watch(
        stylesRoot,
        {
            persistent: true,
            ignoreInitial: true,

            awaitWriteFinish: {
                stabilityThreshold: 150,
                pollInterval: 50
            }
        }
    );

    cssWatcher.on(
        "change",
        file => rebuildCss(file)
    );

    cssWatcher.on(
        "add",
        file => rebuildCss(file)
    );

    cssWatcher.on(
        "unlink",
        file => rebuildCss(file)
    );

    console.log("\n👀 Watching EZPZE...\n");
}

// --------------------------------------------------
// Production build
// --------------------------------------------------

async function productionBuild() {
    console.log("\n🔨 Building EZPZE...\n");

    await buildAllJs();
    buildCss();

    console.log(
        "\n✓ EZPZE build completed successfully.\n"
    );
}

// --------------------------------------------------
// Run
// --------------------------------------------------

async function build() {
    if (isWatch) {
        await watch();
    } else {
        await productionBuild();
    }
}

build().catch(error => {
    console.error(error);
    process.exit(1);
});