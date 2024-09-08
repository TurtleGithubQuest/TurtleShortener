<?php
namespace TurtleShortener\Misc;

namespace TurtleShortener\Misc;


class LogUtil {
    private static ?LogUtil $instance = null;
    private $handle;
    private $logDir;
    private $date;

    private function __construct() {
        $this->date = date('Y-m-d');
        $this->logDir = __DIR__ . "/../../logs";
    }

    public static function getInstance(): LogUtil {
        if (self::$instance === null) {
            self::$instance = new LogUtil();
        }
        return self::$instance;
    }

    private function initializeHandle(): void {
        if ($this->handle === null) {
            if (!is_dir($this->logDir) && !mkdir($this->logDir, 0777, true) && !is_dir($this->logDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->logDir));
            }
            $this->handle = fopen("{$this->logDir}/{$this->date}-log.txt", 'ab');
            if ($this->handle === false) {
                throw new \RuntimeException('Failed to open log file for writing');
            }
        }
    }

    public function debug(string $message): void {
        $this->log($message, 'debug');
    }

    private function log(string $message, string $level): void {
        $this->initializeHandle();
        $timestamp = date('H:i:s');
        $logMessage = "[$timestamp] |$level| - $message\n";
        fwrite($this->handle, $logMessage);
    }

    public function __destruct() {
        if ($this->handle !== null) {
            fclose($this->handle);
        }
    }
}