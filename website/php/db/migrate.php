<?php
require_once('./util.php');
$pdo = DbUtil::getPdo();

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "CREATE TABLE IF NOT EXISTS urls (
    ulid VARCHAR(26) PRIMARY KEY,
    shortcode VARCHAR(6) NOT NULL, #-- url shortcode placeholder
    url VARCHAR(2083) NOT NULL, #-- origin url
    expiry TIMESTAMP, #-- date of expiration, null = url will last forever
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP #--DATE CREATED
)";

$pdo->exec($sql);

echo "Db migrated successfully.";