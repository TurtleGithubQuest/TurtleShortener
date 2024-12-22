<?php
declare(strict_types=1);

namespace TurtleShortener\Database;

use PDO;
use RuntimeException;
use TurtleShortener\Misc\AccessLevel;

class Migrate {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = DbUtil::getPdo();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Execute the database migration
     * @param string $token The authentication token
     * @return string Success message
     * @throws RuntimeException If token is invalid
     */
    public function execute(string $token): string {
        if (!($GLOBALS['utils']->isTokenValid(AccessLevel::admin, $token) ?? false)) {
            throw new RuntimeException('Invalid token');
        }

        $urls = 'CREATE TABLE IF NOT EXISTS urls (
            ulid VARCHAR(26) PRIMARY KEY,
            shortcode VARCHAR(6) NOT NULL, #-- url shortcode placeholder
            url VARCHAR(2083) NOT NULL, #-- origin url
            expiry BIGINT, #-- date of expiration, null = url will last forever
            created BIGINT DEFAULT (UNIX_TIMESTAMP()), #--DATE CREATED
            searchable BOOL
        )';

        $statistics = 'CREATE TABLE IF NOT EXISTS stats (
            ulid VARCHAR(26) PRIMARY KEY,
            url_ulid VARCHAR(26) NOT NULL,
            click_time BIGINT NOT NULL, #-- Timestamp of the click
            referrer VARCHAR(2083), #-- Referrer URL
            country VARCHAR(100), #-- Country of the user
            city VARCHAR(100), #-- City of the user
            user_agent VARCHAR(255), #-- User agent string
            ip_address VARCHAR(45), #-- IP address of the user, IPv6 compatible
            operating_system VARCHAR(100), #-- Operating system of the user
            click_source VARCHAR(100) #-- Source of the click (e.g., web, email, social)
        )';

        $stats_country = 'CREATE TABLE IF NOT EXISTS stats_country_summary (
            unix BIGINT,
            country VARCHAR(100) NOT NULL,
            visit_count INT NOT NULL,
            unique_visitors INT NOT NULL,
            PRIMARY KEY (unix, country)
        )';

        $stats_os = 'CREATE TABLE IF NOT EXISTS stats_os_summary (
            unix BIGINT,
            operating_system VARCHAR(100) NOT NULL,
            visit_count INT NOT NULL,
            unique_visitors INT NOT NULL,
            PRIMARY KEY (unix, operating_system)
        )';

        $stats_city = 'CREATE TABLE IF NOT EXISTS stats_city_summary (
            unix BIGINT,
            city VARCHAR(100) NOT NULL,
            visit_count INT NOT NULL,
            unique_visitors INT NOT NULL,
            PRIMARY KEY (unix, city)
        )';

        $stats_source = 'CREATE TABLE IF NOT EXISTS stats_source_summary (
            unix BIGINT,
            click_source VARCHAR(100) NOT NULL,
            visit_count INT NOT NULL,
            unique_visitors INT NOT NULL,
            PRIMARY KEY (unix, click_source)
        )';

        try {
            foreach ([$urls, $statistics, $stats_country, $stats_os, $stats_city, $stats_source] as $sql) {
                $this->pdo->exec($sql);
            }
            return 'Database migrated successfully.';
        } catch (\PDOException $e) {
            throw new RuntimeException('Database migration failed: ' . $e->getMessage());
        }
    }

}