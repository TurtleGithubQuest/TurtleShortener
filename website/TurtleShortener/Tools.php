<?php
declare(strict_types=1);

namespace TurtleShortener;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use TurtleShortener\Admin\CommandExecutor;

require_once __DIR__ . '/bootstrap.php';

class Tools {
    private CommandExecutor $executor;

    public function __construct() {
        $this->executor = new CommandExecutor();
        session_start();
    }

    public function handleRequest(): void {
        try {
            $tool = $this->getToolParameter();
            if (!isset($tool)) {
                exit;
            }

            $this->processToolRequest($tool);
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    private function getToolParameter(): ?string {
        return strtolower($_GET['t'] ?? '');
    }

    /**
     * @throws Exception
     */
    private function processToolRequest(string $tool): void {
        if ($tool === 'clear') {
            $this->handleClearRequest();
        }

        $params = [...$_GET, ...$_POST];
        echo $this->executor->execute($tool, $params);
    }

    #[NoReturn] private function handleClearRequest(): void {
        header('Location: ../');
        unset($_SESSION['shortened_array']);
        exit;
    }

    private function handleError(Exception $e): void {
        http_response_code(400);
        echo $e->getMessage();
    }
}

$tools = new Tools();
$tools->handleRequest();