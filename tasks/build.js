import { spawnSync } from 'bun';
import { resolve } from 'path';
import { exists } from 'fs/promises';
import { colorLog } from './utils.js';

function runCommand(command, args, cwd) {
    colorLog("BLUE", `Running command: ${command} ${args.join(' ')}`);
    const proc = spawnSync([command, ...args], {
        cwd
    });
    if (!proc.success)
        throw new Error(proc.error);

    if (proc.stdout) {
        const lines = proc.stdout.toString().split(/\r?\n/);
        lines.forEach(line => {
            if (line.trim()) {
                colorLog("GREY", line);
            }
        });
    }
}

async function buildComposer() {
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

async function buildJavaScript() {
    colorLog("YELLOW", 'Building JavaScript...');
    if (!(await exists('package.json'))) {
        throw new Error('package.json not found in the src directory. Make sure it exists and contains necessary build scripts.');
    }

    await runCommand('bun', ['install']);
    await runCommand('bun', ['run', 'build.js']);
    
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
colorLog("BRIGHT_MAGENTA", "Building..")
await build();
