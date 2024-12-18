import { Client } from "basic-ftp";
import { Readable } from "stream";
import { build } from "./build.js";
import path from "path";
import { colorLog } from "./utils.js";

const SERVER_HOST = process.env.SERVER_HOST;
const SERVER_USER = process.env.SERVER_USER;
const SERVER_PASSWORD = process.env.SERVER_PASSWORD;
const SERVER_PATH = process.env.SERVER_PATH; // Default to root if not provided
const LOCAL_PATH = "./website";

if (!SERVER_USER || !SERVER_HOST || !SERVER_PATH || !SERVER_PASSWORD) {
    colorLog("RED", "Missing required environment variables. Please set SERVER_USER, SERVER_HOST, SERVER_PATH, and SERVER_PASSWORD.");
    process.exit(1);
}

async function deploy() {
    const client = new Client();

    try {
        colorLog("YELLOW", "Connecting to server...");

        await client.access({
            host: SERVER_HOST,
            user: SERVER_USER,
            password: SERVER_PASSWORD,
            secure: true, // Enable FTPS
        });

        colorLog("GREEN", "Connection established.");

        // Step 1: Upload the entire website directory
        colorLog("YELLOW", `Uploading directory: '${LOCAL_PATH}' to '${SERVER_HOST}/${SERVER_PATH}'.`);
        await client.uploadFromDir(LOCAL_PATH, SERVER_PATH);

        // Step 2: Generate and upload settings.php directly to the server
        colorLog("YELLOW", "Generating settings.php...");
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

        const settingsFilePath = path.join("TurtleShortener", "settings.php");
        const readableStream = Readable.from([settingsContent]);
        // Upload settings.php
        colorLog("YELLOW", "Uploading settings.php...");
        await client.uploadFrom(readableStream, path.join(SERVER_PATH, settingsFilePath));

        colorLog("GREEN", "Deployment completed successfully!");
    } catch (error) {
        colorLog("RED", `Deployment failed: ${error}`);
    } finally {
        client.close();
    }
}
if (import.meta.main) {
    await build();
    colorLog("BRIGHT_MAGENTA", "Deploying..")
    await deploy();
}