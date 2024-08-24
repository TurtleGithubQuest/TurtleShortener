import { $ } from "bun";

const SERVER_USER = "your_username";
const SERVER_HOST = "your_server_hostname";
const SERVER_PATH = "/path/to/server/directory";
const LOCAL_PATH = "./website";

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
