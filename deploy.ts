import { $ } from "bun";

const SERVER_USER = process.env.SERVER_USER;
const SERVER_HOST = process.env.SERVER_HOST;
const SERVER_PATH = process.env.SERVER_PATH;
const LOCAL_PATH = "./website";

if (!SERVER_USER || !SERVER_HOST || !SERVER_PATH) {
  console.error("Missing required environment variables. Please set SERVER_USER, SERVER_HOST, and SERVER_PATH.");
  process.exit(1);
}

async function deploy() {
  try {
    console.log("Starting deployment...");
    
    const result = await $`rsync -avz --delete ${LOCAL_PATH}/ ${SERVER_USER}@${SERVER_HOST}:${SERVER_PATH}`;
    
    console.log(result.stdout);
    console.log("Deployment completed successfully!");
  } catch (error) {
    console.error("Deployment failed:", error);
  }
}

deploy();
