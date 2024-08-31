import { spawn } from 'bun';
import { join, resolve } from 'path';
import { exists } from 'fs/promises';

async function runCommand(command, args, cwd) {
    console.log(`Running command: ${command} ${args.join(' ')}`);
    const proc = spawn([command, ...args], { cwd });
    const output = await new Response(proc.stdout).text();
    console.log(output);
    if (!proc.success) {
        throw new Error(`Command failed: ${command} ${args.join(' ')}`);
    }
}

async function buildComposer() {
    console.log('Building Composer...');
    const composerDir = resolve(import.meta.dir, '../website/composer');
    await runCommand('composer', ['install', '--no-dev', '--optimize-autoloader'], composerDir);
    console.log('Composer build completed.');
}

async function buildJavaScript() {
    console.log('Building JavaScript...');
    const srcDir = resolve(import.meta.dir, '../src');
    
    // Check if package.json exists
    if (!(await exists(join(srcDir, 'package.json')))) {
        throw new Error('package.json not found in the src directory. Make sure it exists and contains necessary build scripts.');
    }

    // Install dependencies
    await runCommand('bun', ['install'], srcDir);

    // Run build script (assuming it's defined in package.json)
    await runCommand('bun', ['run', 'build'], srcDir);
    
    console.log('JavaScript build completed.');
}

async function build() {
    try {
        await buildComposer();
        await buildJavaScript();
        console.log('Build process completed successfully.');
    } catch (error) {
        console.error('Build process failed:', error);
        process.exit(1);
    }
}

await build();
