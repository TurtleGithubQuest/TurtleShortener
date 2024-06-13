<?php
$token = filter_input(INPUT_GET, 'token')
    ?? filter_input(INPUT_POST, 'token');
$settings = require(__DIR__."/../settings.php");
$admin_tokens = $settings["admin_tokens"];
if (!in_array($token, $admin_tokens)) {
    echo "Access token is not valid.";
    exit;
}
require_once('util.php');
$pdo = DbUtil::getPdo();

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "CREATE TABLE IF NOT EXISTS urls (
    ulid VARCHAR(26) PRIMARY KEY,
    shortcode VARCHAR(6) NOT NULL, #-- url shortcode placeholder
    url VARCHAR(2083) NOT NULL, #-- origin url
    expiry BIGINT, #-- date of expiration, null = url will last forever
    created BIGINT DEFAULT (UNIX_TIMESTAMP()) #--DATE CREATED
)";

$pdo->exec($sql);

echo "Db migrated successfully.";