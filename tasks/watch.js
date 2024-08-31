import { watch } from "fs";
import { Client } from "basic-ftp";
import path from "path";
import { COLORS, getTimestamp, colorLog } from "./utils.js";

// Environment variables
const SERVER_HOST = process.env.SERVER_HOST;
const SERVER_USER = process.env.SERVER_USER;
const SERVER_PASSWORD = process.env.SERVER_PASSWORD;
const SERVER_PATH = process.env.SERVER_PATH; // Default to root if not provided
const LOCAL_PATH = "./website";

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
    try {
        await client.access({
            host: SERVER_HOST,
            user: SERVER_USER,
            password: SERVER_PASSWORD,
            secure: true, // Enable FTPS
        });
        colorLog("GREEN", `Uploading file: ${localPath}`);
        await client.uploadFrom(localPath, remotePath);
    } catch (error) {
        colorLog("RED", `Error uploading file ${localPath}: ${error}`);
    } finally {
        client.close();
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
    if (eventType === 'change' && filename) {
        debouncedUploadFile(filename);
    }
});
