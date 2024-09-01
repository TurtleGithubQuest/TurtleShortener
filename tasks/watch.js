import { watch } from "fs";
import { Client } from "basic-ftp";
import path from "path";
import {colorLog} from "./utils.js";
import {buildJavaScript} from "./build.js";

// Environment variables
const SERVER_HOST = process.env.SERVER_HOST;
const SERVER_USER = process.env.SERVER_USER;
const SERVER_PASSWORD = process.env.SERVER_PASSWORD;
const SERVER_PATH = process.env.SERVER_PATH; // Default to root if not provided
const LOCAL_PATH = "./website";
const JS_PATH = "./src";

// Validate environment variables
if (!SERVER_USER || !SERVER_HOST || !SERVER_PATH || !SERVER_PASSWORD) {
    colorLog("RED", "Missing required environment variables. Please set SERVER_USER, SERVER_HOST, SERVER_PATH, and SERVER_PASSWORD.");
    process.exit(1);
}

// Debounce function to limit the rate at which uploads are triggered
function debounce(func, wait) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
}

// Function to upload a file to the server
async function uploadFile(localPath, remotePath) {
    const client = new Client();
    let attempts = 0;
    const maxAttempts = 5;
    let success = false;

    while (attempts < maxAttempts && !success) {
        try {
            await client.access({
                host: SERVER_HOST,
                user: SERVER_USER,
                password: SERVER_PASSWORD,
                secure: true, // Enable FTPS
            });
            colorLog("GREEN", `Uploading file: ${localPath} (Attempt ${attempts + 1})`);
            await client.uploadFrom(localPath, remotePath);
            success = true;
        } catch (error) {
            attempts++;
            colorLog("RED", `Error uploading file ${localPath} (Attempt ${attempts}): ${error}`);
            if (attempts >= maxAttempts) {
                colorLog("RED", `Failed to upload file ${localPath} after ${maxAttempts} attempts.`);
            }
        } finally {
            client.close();
        }
    }
}

// Debounced upload function
const debouncedUploadFile = debounce(async (filename) => {
    const localFilePath = path.join(LOCAL_PATH, filename);
    const remoteFilePath = path.join(SERVER_PATH, filename);

    await uploadFile(localFilePath, remoteFilePath);
}, 15); // Adjust debounce delay as needed

// Watch for changes in the directory
colorLog("BRIGHT_MAGENTA", `Watching for changes in folder '${LOCAL_PATH.replace("./", "")}'...`);
watch(LOCAL_PATH, { recursive: true }, (eventType, filename) => {
    // Only trigger upload on file changes (not on rename or delete)
    if (eventType !== 'change' && filename) {
        colorLog("BRIGHT_WHITE", `${eventType}d ${filename}.`);
    }
    // Ignore temporary files
    if (!filename.endsWith('~')) {
        debouncedUploadFile(filename);
    }
});
colorLog("BRIGHT_MAGENTA", `Watching for changes in folder '${JS_PATH.replace("./", "")}'...`);
watch(JS_PATH, { recursive: true }, async (eventType, filename) => {
    if (eventType === 'change')
        debounce(buildJavaScript(true), 15);
});
