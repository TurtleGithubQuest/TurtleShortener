import { Client } from "basic-ftp";
import { Readable } from "stream";
import path from "path";

const SERVER_HOST = process.env.SERVER_HOST;
const SERVER_USER = process.env.SERVER_USER;
const SERVER_PASSWORD = process.env.SERVER_PASSWORD;
const SERVER_PATH = process.env.SERVER_PATH; // Default to root if not provided
const LOCAL_PATH = "./website";

if (!SERVER_USER || !SERVER_HOST || !SERVER_PATH || !SERVER_PASSWORD) {
    console.error("Missing required environment variables. Please set SERVER_USER, SERVER_HOST, SERVER_PATH, and SERVER_PASSWORD.");
    process.exit(1);
}

async function deploy() {
    const client = new Client();

    try {
        console.log(`${getTimestamp()} Connecting to server...`);

        await client.access({
            host: SERVER_HOST,
            user: SERVER_USER,
            password: SERVER_PASSWORD,
            secure: true, // Enable FTPS
        });

        console.log(`${getTimestamp()} Connection established.`);

        // Step 1: Upload the entire website directory
        console.log(`${getTimestamp()} Uploading directory: ${LOCAL_PATH}...`);
        await client.uploadFromDir(LOCAL_PATH, SERVER_PATH);

        // Step 2: Generate and upload settings.php directly to the server
        console.log(`${getTimestamp()} Generating settings.php...`);
        const settingsContent = `<?php
          return [
              'db_host' => '${process.env.DB_HOST || 'localhost'}',
              'db_name' => '${process.env.DB_NAME || 'database'}',
              'db_user' => '${process.env.DB_USER || 'user'}',
              'db_pass' => '${process.env.DB_PASS || 'password'}',
              'admin_tokens' => array(${(process.env.ADMIN_TOKENS || '').split(',').map(token => `'${token.trim()}'`).join(", ")}),
              'server_tokens' => array(${(process.env.SERVER_TOKENS || '').split(',').map(token => `'${token.trim()}'`).join(", ")}),
              'img_dir' => '${process.env.IMG_DIR || 'i/'}',
              'img_tokens' => array(${(process.env.IMG_TOKENS || '').split(',').map(token => `'${token.trim()}'`).join(", ")}),
              'img_name_length' => ${process.env.IMG_NAME_LENGTH || 6},
              'img_extensions' => array(${(process.env.IMG_EXTENSIONS || 'jpg,jpeg,png,gif,jfif').replaceAll(" ", "").split(',').map(ext => `'${ext.trim()}'`).join(", ")}),
          ];`;

        const settingsFilePath = path.join("php", "settings.php");
        const readableStream = Readable.from([settingsContent]);
        // Upload settings.php
        console.log(`${getTimestamp()} Uploading settings.php...`);
        await client.uploadFrom(readableStream, path.join(SERVER_PATH, settingsFilePath));

        console.log(`${getTimestamp()} Deployment completed successfully!`);
    } catch (error) {
        console.error(`${getTimestamp()} Deployment failed:`, error);
    } finally {
        client.close();
    }
}
function getTimestamp() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    return `[${hours}:${minutes}:${seconds}]`;
}
await deploy();
