import {spawnSync} from "bun";

export const COLORS = {
    RESET: "\x1b[0m",
    GREEN: "\x1b[32m",
    YELLOW: "\x1b[33m",
    RED: "\x1b[31m",
    BLUE: "\x1b[34m",
    GREY: "\x1b[38;5;240m",
    CYAN: "\x1b[36m",
    MAGENTA: "\x1b[35m",
    WHITE: "\x1b[37m",
    BLACK: "\x1b[30m",
    BRIGHT_GREEN: "\x1b[38;5;82m",
    BRIGHT_YELLOW: "\x1b[38;5;226m",
    BRIGHT_RED: "\x1b[38;5;196m",
    BRIGHT_BLUE: "\x1b[38;5;75m",
    BRIGHT_CYAN: "\x1b[38;5;51m",
    BRIGHT_MAGENTA: "\x1b[38;5;201m",
    BRIGHT_WHITE: "\x1b[38;5;15m"
};

export function getTimestamp() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    return `[${hours}:${minutes}:${seconds}]`;
}

export function colorLog(color, message) {
    console.log(`${COLORS.BRIGHT_YELLOW}${getTimestamp()} ${COLORS[color]}${message}${COLORS.RESET}`);
}

export function runCommand(command, args, cwd, isQuiet=false) {
    if (!isQuiet)
        colorLog("BLUE", `Running command: ${command} ${args.join(' ')}`);
    const proc = spawnSync([command, ...args], {
        cwd
    });
    if (!proc.success)
        throw new Error(proc.error);

    if (proc.stdout && !isQuiet) {
        const lines = proc.stdout.toString().split(/\r?\n/);
        lines.forEach(line => {
            if (line.trim()) {
                colorLog("GREY", line);
            }
        });
    }
}