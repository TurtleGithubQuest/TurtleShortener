<?php
declare(strict_types=1);

namespace TurtleShortener\Admin;

use Exception;
use InvalidArgumentException;
use JsonException;
use ReflectionException;
use ReflectionMethod;
use TurtleShortener\Database\Migrate as MigrateDb;
use TurtleShortener\Database\UpKeep;
use TurtleShortener\Misc\Search;

/**
 * Handles execution of commands
 */
class CommandExecutor {
    private array $settings;
    private array $commands = [];

    public function __construct() {
        $this->settings = $GLOBALS['settings'];

        if (empty($this->settings)) {
            throw new \LogicException('Settings not loaded');
        }

        $this->registerCommand('upkeep', new UpKeep());
        $this->registerCommand('build', new Build());
        $this->registerCommand('migratedb', new MigrateDb());
        $this->registerCommand('search', new Search());
        $this->registerCommand('statsum', new StatSummary());
    }

    /**
     * Register a command with the executor
     * @param string $name Command name
     * @param mixed $command Command instance
     */
    public function registerCommand(string $name, mixed $command): void {
        $this->commands[$name] = $command;
    }

    /**
     * Executes the specified command
     * @param string $commandName The command to execute
     * @param array $params
     * @return string Result message
     * @throws JsonException
     * @throws ReflectionException
     */
    public function execute(string $commandName, array $params): string {
        try {
            if (!isset($this->commands[$commandName])) {
                throw new InvalidArgumentException("Unknown command: $commandName");
            }

            $reflection = new ReflectionMethod($this->commands[$commandName], 'execute');
            $parameters = $reflection->getParameters();
            $args = [];

            foreach ($parameters as $parameter) {
                $paramName = $parameter->getName();
                if (isset($params[$paramName])) {
                    $args[] = $params[$paramName];
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $args[] = $parameter->getDefaultValue();
                } else {
                    throw new InvalidArgumentException("Missing required parameter: $paramName");
                }
            }

            return $this->commands[$commandName]->execute(...$args);
        } catch (Exception $e) {
            $GLOBALS['log']->error("Command execution failed: {$e->getMessage()}");
            throw $e;
        }
    }

}