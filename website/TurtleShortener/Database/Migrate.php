<?php
namespace TurtleShortener\Database;

use PDO;
use TurtleShortener\Misc\AccessLevel;

require_once(__DIR__.'/../bootstrap.php');

if (!($GLOBALS['utils']->isTokenValid(AccessLevel::admin, null) ?? false)) {
    exit;
}
$pdo = DbUtil::getPdo();

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$urls = "CREATE TABLE IF NOT EXISTS urls (
    ulid VARCHAR(26) PRIMARY KEY,
    shortcode VARCHAR(6) NOT NULL, #-- url shortcode placeholder
    url VARCHAR(2083) NOT NULL, #-- origin url
    expiry BIGINT, #-- date of expiration, null = url will last forever
    created BIGINT DEFAULT (UNIX_TIMESTAMP()), #--DATE CREATED
    searchable BOOL
)";
$statistics = "CREATE TABLE IF NOT EXISTS stats (
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
)";
foreach ([$urls, $statistics] as $sql) {
    $pdo->exec($sql);
}

echo "Db migrated successfully.";
