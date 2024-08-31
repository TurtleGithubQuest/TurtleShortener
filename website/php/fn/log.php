<?php
namespace Website\Php\Lang;
namespace Website\Php\Lang;
namespace Website\Php\Fn;
namespace Website\Php\Fn;
namespace Website\Php\Fn;

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

    public function log($message): void{
        $timestamp = date('H:i:s');
        fwrite($this->handle, "[$timestamp] - $message\n");
    }

    function __destruct() {
        fclose($this->handle);
    }
}
