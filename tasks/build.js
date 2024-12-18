import { resolve } from 'path';
import { exists } from 'fs/promises';
import {colorLog, runCommand} from './utils.js';

export async function buildComposer() {
    colorLog("YELLOW", 'Building Composer...');
    const composerDir = resolve(import.meta.dir, '../website/composer');
    
    try {
        await runCommand('composer', ['--version'], composerDir);
        await runCommand('composer', ['install', '--no-dev', '--optimize-autoloader'], composerDir);
        colorLog("YELLOW", 'Composer build completed.');
    } catch (error) {
        colorLog("RED", 'Composer is not available or encountered an error: \n'+ error);
        colorLog("YELLOW", 'Please make sure Composer is installed and accessible from the command line.');
        colorLog("YELLOW", 'You can download Composer from https://getcomposer.org/');
        throw new Error('Composer build failed. See above for details.');
    }
}

export async function buildJavaScript(isQuiet=false) {
    if (!isQuiet)
        colorLog("YELLOW", 'Building JavaScript...');
    if (!(await exists('package.json'))) {
        throw new Error('package.json not found in the src directory. Make sure it exists and contains necessary build scripts.');
    }

    await runCommand('bun', ['install'], null, isQuiet);
    await runCommand('bun', ['run', 'build.js'], null, isQuiet);
    if (!isQuiet)
        colorLog("YELLOW", 'JavaScript build completed.');
}

export async function build() {
    try {
        await buildComposer();
    } catch (error) {
        colorLog("RED", `Composer build failed: \n${error}`);
        process.exit(1);
    }

    try {
        await buildJavaScript();
    } catch (error) {
        colorLog("RED", `JavaScript build failed: \n${error}`);
        process.exit(1);
    }
    colorLog("GREEN", 'Build process completed successfully.');
}
if (import.meta.main) {
    colorLog("BRIGHT_MAGENTA", "Building...");
    await build();
}
