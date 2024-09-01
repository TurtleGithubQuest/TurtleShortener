<?php
namespace TurtleShortener\Misc;

class LogUtil {
    private static LogUtil $instance;
    private $handle;

    private function __construct() {
        $date = date('Y-m-d');
        $logDir = __DIR__."/../../logs";
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $this->handle = fopen("$logDir/$date-log.txt", 'a');
    }

    public static function getInstance(): LogUtil {
        if (!isset(self::$instance)) {
            self::$instance = new LogUtil();
        }
        return self::$instance;
    }

    public function debug(string $message): void
    {
        $this->log($message, 'debug');
    }

    private function log(string $message, string $level): void
    {
        $timestamp = date('H:i:s');
        fwrite($this->handle, "[$timestamp] |$level| - $message\n");
    }

    function __destruct() {
        fclose($this->handle);
    }
}
