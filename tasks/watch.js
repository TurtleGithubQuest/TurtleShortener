import { watch } from "fs";
import { Client } from "basic-ftp";
import path from "path";

// Environment variables
const SERVER_HOST = process.env.SERVER_HOST;
const SERVER_USER = process.env.SERVER_USER;
const SERVER_PASSWORD = process.env.SERVER_PASSWORD;
const SERVER_PATH = process.env.SERVER_PATH; // Default to root if not provided
const LOCAL_PATH = "./website";

// Validate environment variables
if (!SERVER_USER || !SERVER_HOST || !SERVER_PATH || !SERVER_PASSWORD) {
    console.error("Missing required environment variables. Please set SERVER_USER, SERVER_HOST, SERVER_PATH, and SERVER_PASSWORD.");
    process.exit(1);
}
const COLORS = {
    RESET: "\x1b[0m",
    GREEN: "\x1b[32m",
    YELLOW: "\x1b[33m",
    RED: "\x1b[31m",
    BLUE: "\x1b[34m",
    GREY: "\x1b[38;5;249m"
};
// Debounce function to limit the rate at which uploads are triggered
function debounce(func, wait) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
}
function getTimestamp() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    return `[${hours}:${minutes}:${seconds}]`;
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
        console.log(`${COLORS.GREY}${getTimestamp()} Uploading file: ${COLORS.GREEN}${localPath}${COLORS.RESET}`);
        await client.uploadFrom(localPath, remotePath);
    } catch (error) {
        console.error(`${COLORS.RED}${getTimestamp()} Error uploading file ${localPath}: ${COLORS.RESET}`, error);
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
console.log(`${COLORS.YELLOW}${getTimestamp()} Watching for changes in ${COLORS.BLUE}'${LOCAL_PATH}'${COLORS.YELLOW}...${COLORS.RESET}`);
watch(LOCAL_PATH, { recursive: true }, (eventType, filename) => {
    // Only trigger upload on file changes (not on rename or delete)
    if (eventType === 'change' && filename) {
        debouncedUploadFile(filename);
    }
});