<?php
declare(strict_types=1);

namespace TurtleShortener\Misc;

class LogUtil {
    private static ?LogUtil $instance = null;
    protected mixed $handle = null;

    private function __construct(
        private ?string $date = null,
        private readonly string $logDir = (__DIR__ . '/../../logs')
    ) {
        $this->date = date('Y-m-d');
    }

    public static function getInstance(): LogUtil {
        if (self::$instance === null) {
            self::$instance = new LogUtil();
        }
        return self::$instance;
    }

    private function getHandle(): mixed {
        if ($this->handle === null) {
            if (!is_dir($this->logDir) && !mkdir($this->logDir, 0777, true) && !is_dir($this->logDir)) {
                throw new \RuntimeException(\sprintf('Directory "%s" was not created', $this->logDir));
            }
            $this->handle = fopen("{$this->logDir}/{$this->date}-log.txt", 'ab');
            if ($this->handle === false) {
                throw new \RuntimeException('Failed to open log file for writing');
            }
        }
        return $this->handle;
    }

    public function debug(string $message): void {
        $this->log($message, 'debug');
    }
    public function error(string $message): void {
        $this->log($message, 'error');
    }

    private function log(string $message, string $level): void {
        $timestamp = date('H:i:s');
        $logMessage = "[$timestamp] |$level| - $message\n";
        fwrite($this->getHandle(), $logMessage);
    }

    public function __destruct() {
        if ($this->handle !== null) {
            fclose($this->handle);
        }
    }

}