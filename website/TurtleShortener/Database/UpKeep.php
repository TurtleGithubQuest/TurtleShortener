<?php
declare(strict_types=1);
namespace TurtleShortener\Database;

use PDO;
use RuntimeException;
use TurtleShortener\Misc\AccessLevel;
use function sprintf;

/**
 * Handles cleanup of expired URLs from the database
 */
class UpKeep {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = DbUtil::getPdo();
    }

    /**
     * Deletes expired URLs from the database
     * @return int Number of deleted URLs
     */
    private function deleteExpiredUrls(): int {
        $query = 'DELETE FROM urls WHERE expiry IS NOT NULL AND expiry < :current_time';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['current_time' => time()]);
        return $stmt->rowCount();
    }

    /**
     * Executes the cleanup process
     * @return string Status message
     */
    public function execute(string $token): string {
        if (!($GLOBALS['utils']->isTokenValid(AccessLevel::server, $token) ?? false)) {
            throw new RuntimeException('Invalid token');
        }
        try {
            $deletedCount = $this->deleteExpiredUrls();

            if ($deletedCount === 0) {
                return 'No expired URLs found.';
            }

            $message = sprintf('Deleted %d expired URLs', $deletedCount);
            $GLOBALS['log']->debug($message);

            return $message;

        } catch (RuntimeException $e) {
            http_response_code(403);
            return $e->getMessage();
        } catch (\Exception $e) {
            http_response_code(500);
            $GLOBALS['log']->error($e->getMessage());
            return 'An error occurred during cleanup.';
        }
    }

}