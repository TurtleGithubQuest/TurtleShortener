<?php
declare(strict_types=1);

namespace TurtleShortener\Misc;

use JetBrains\PhpStorm\NoReturn;
use PDO;
use RuntimeException;
use TurtleShortener\Database\DbUtil;

class Search {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = DbUtil::getPdo();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Execute the search command
     * @param string $q
     * @return string JSON encoded search results
     */
    public function execute(string $q): string {
        try {
            $searchQuery = '%' . trim($q) . '%';
            $host = '%' . explode(':', $_SERVER['HTTP_HOST'])[0] . '%';

            //Do not change to wildcard
            $stmt = $this->pdo->prepare('SELECT ulid, shortcode, url, expiry, created FROM urls WHERE (shortcode LIKE ? OR url LIKE ?) AND url NOT LIKE ? AND (searchable != 0 OR searchable IS NULL) LIMIT 10');
            $stmt->execute([$searchQuery, $searchQuery, $host]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
            $this->handleResponse(json_encode($data, JSON_THROW_ON_ERROR));
        } catch (\PDOException $e) {
            throw new RuntimeException('Search failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle the search response
     * @param string $data The search results
     */
    #[NoReturn] public function handleResponse(string $data): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-type: application/json');
            if (empty($data)) {
                http_response_code(404);
                echo json_encode(['error' => 'No results found'], JSON_THROW_ON_ERROR);
            } else {
                echo $data;
            }
        } else {
            header('Location: /?found=' . urlencode($data));
        }
        exit;
    }

}