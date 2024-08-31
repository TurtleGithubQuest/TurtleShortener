import { spawn } from 'bun';
import { join, resolve } from 'path';
import { exists } from 'fs/promises';
import { colorLog } from './utils.js';

async function runCommand(command, args, cwd) {
    colorLog("BLUE", `Running command: ${command} ${args.join(' ')}`);
    const proc = spawn([command, ...args], { cwd });
    const output = await new Response(proc.stdout).text();
    console.log(output);
    if (!proc.success) {
        throw new Error(`Command failed: ${command} ${args.join(' ')}`);
    }
}

async function buildComposer() {
    colorLog("YELLOW", 'Building Composer...');
    const composerDir = resolve(import.meta.dir, '../website/composer');
    
    try {
        // Check if composer is available
        await runCommand('composer', ['--version'], composerDir);
        
        // If composer is available, proceed with the installation
        await runCommand('composer', ['install', '--no-dev', '--optimize-autoloader'], composerDir);
        colorLog("GREEN", 'Composer build completed.');
    } catch (error) {
        colorLog("RED", 'Composer is not available or encountered an error.');
        colorLog("YELLOW", 'Please make sure Composer is installed and accessible from the command line.');
        colorLog("YELLOW", 'You can download Composer from https://getcomposer.org/');
        throw new Error('Composer build failed. See above for details.');
    }
}

async function buildJavaScript() {
    colorLog("YELLOW", 'Building JavaScript...');
    const srcDir = resolve(import.meta.dir, '../src');
    
    // Check if package.json exists
    if (!(await exists(join(srcDir, 'package.json')))) {
        throw new Error('package.json not found in the src directory. Make sure it exists and contains necessary build scripts.');
    }

    // Install dependencies
    await runCommand('bun', ['install'], srcDir);

    // Run build script (assuming it's defined in package.json)
    await runCommand('bun', ['run', 'build'], srcDir);
    
    colorLog("GREEN", 'JavaScript build completed.');
}

async function build() {
    let composerBuildFailed = false;
    try {
        await buildComposer();
    } catch (error) {
        composerBuildFailed = true;
        colorLog("RED", `Composer build failed: ${error}`);
    }

    try {
        await buildJavaScript();
        if (composerBuildFailed) {
            colorLog("YELLOW", 'Build process completed with warnings (Composer build failed).');
        } else {
            colorLog("GREEN", 'Build process completed successfully.');
        }
    } catch (error) {
        colorLog("RED", `JavaScript build failed: ${error}`);
        process.exit(1);
    }
}

await build();
