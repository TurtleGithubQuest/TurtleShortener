<?php

declare(strict_types=1);
namespace TurtleShortener\Admin;

use RuntimeException;
use PDO;
use TurtleShortener\Database\DbUtil;
use TurtleShortener\Misc\AccessLevel;

class StatSummary {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = DbUtil::getPdo();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Creates a summary of geo data for the current day
     * @param string $token Access token for authentication
     * @return string Result message
     * @throws \JsonException
     */
    public function execute(string $token): string {
        if (!($GLOBALS['utils']->isTokenValid(AccessLevel::admin, $token) &&
            !$GLOBALS['utils']->isTokenValid(AccessLevel::server, $token))
        ) {
            throw new RuntimeException('Invalid token');
        }

        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $unix_today = strtotime($today);

        // Get country stats
        $countryStmt = $this->pdo->prepare('
            SELECT 
                country,
                COUNT(*) as visit_count,
                COUNT(DISTINCT ip_address) as unique_visitors
            FROM stats
            WHERE click_time >= ? AND click_time < ?
            GROUP BY country
        ');
        $countryStmt->execute([$unix_today, strtotime($tomorrow)]);
        $countryStats = $countryStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get OS stats
        $osStmt = $this->pdo->prepare('
            SELECT 
                operating_system,
                COUNT(*) as visit_count,
                COUNT(DISTINCT ip_address) as unique_visitors
            FROM stats 
            WHERE click_time >= ? AND click_time < ?
            GROUP BY operating_system
        ');
        $osStmt->execute([$unix_today, strtotime($tomorrow)]);
        $osStats = $osStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get city stats
        $cityStmt = $this->pdo->prepare('
            SELECT 
                city,
                COUNT(*) as visit_count,
                COUNT(DISTINCT ip_address) as unique_visitors
            FROM stats 
            WHERE click_time >= ? AND click_time < ?
            GROUP BY city
        ');
        $cityStmt->execute([$unix_today, strtotime($tomorrow)]);
        $cityStats = $cityStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get source stats
        $sourceStmt = $this->pdo->prepare("
            SELECT 
                COALESCE(click_source, 'unknown') as click_source,
                COUNT(*) as visit_count,
                COUNT(DISTINCT ip_address) as unique_visitors
            FROM stats 
            WHERE click_time >= ? AND click_time < ?
            GROUP BY COALESCE(click_source, 'unknown')
        ");
        $sourceStmt->execute([$unix_today, strtotime($tomorrow)]);
        $sourceStats = $sourceStmt->fetchAll(PDO::FETCH_ASSOC);

        // Store summaries in respective tables
        $countryInsert = $this->pdo->prepare('
            REPLACE INTO stats_country_summary (unix, country, visit_count, unique_visitors) 
            VALUES (?, ?, ?, ?)
        ');
        $osInsert = $this->pdo->prepare('
            REPLACE INTO stats_os_summary (unix, operating_system, visit_count, unique_visitors) 
            VALUES (?, ?, ?, ?)
        ');
        $cityInsert = $this->pdo->prepare('
            REPLACE INTO stats_city_summary (unix, city, visit_count, unique_visitors) 
            VALUES (?, ?, ?, ?)
        ');
        $sourceInsert = $this->pdo->prepare('
            REPLACE INTO stats_source_summary (unix, click_source, visit_count, unique_visitors) 
            VALUES (?, ?, ?, ?)
        ');

        // Insert country stats
        foreach ($countryStats as $stat) {
            $countryInsert->execute([
                $unix_today,
                $stat['country'],
                $stat['visit_count'],
                $stat['unique_visitors']
            ]);
        }

        // Insert OS stats
        foreach ($osStats as $stat) {
            $osInsert->execute([
                $unix_today,
                $stat['operating_system'],
                $stat['visit_count'],
                $stat['unique_visitors']
            ]);
        }

        // Insert city stats
        foreach ($cityStats as $stat) {
            $cityInsert->execute([
                $unix_today,
                $stat['city'],
                $stat['visit_count'],
                $stat['unique_visitors']
            ]);
        }

        // Insert source stats
        foreach ($sourceStats as $stat) {
            $sourceInsert->execute([
                $unix_today,
                $stat['click_source'],
                $stat['visit_count'],
                $stat['unique_visitors']
            ]);
        }

        return json_encode([
            'status' => 'success',
            'message' => 'Statistics summary created for ' . $today,
            'data' => [
                'country' => $countryStats,
                'os' => $osStats,
                'city' => $cityStats,
                'source' => $sourceStats
            ]
        ], JSON_THROW_ON_ERROR);
    }

}