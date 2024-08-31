export const COLORS = {
    RESET: "\x1b[0m",
    GREEN: "\x1b[32m",
    YELLOW: "\x1b[33m",
    RED: "\x1b[31m",
    BLUE: "\x1b[34m",
    GREY: "\x1b[38;5;249m"
};

export function getTimestamp() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    return `[${hours}:${minutes}:${seconds}]`;
}

export function colorLog(color, message) {
    console.log(`${COLORS.GREY}${getTimestamp()} ${COLORS[color]}${message}${COLORS.RESET}`);
}
