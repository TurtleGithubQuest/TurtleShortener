<?php
$token = filter_input(INPUT_GET, 'token')
    ?? filter_input(INPUT_POST, 'token');
$settings = require(__DIR__."/../settings.php");
$allowed = $settings["server_tokens"];
if (!in_array($token, $allowed)) {
    echo "Access token is not valid.";
    exit;
}
require_once('util.php');
$pdo = DbUtil::getPdo();

$query = "DELETE FROM urls WHERE expiry IS NOT NULL AND expiry < ?";
$currentUnix = time();
$stmt = $pdo->prepare($query);
$stmt->execute([$currentUnix]);
$rowCount = $stmt->rowCount();

if ($rowCount > 0) {
    require_once(__DIR__.'/../log.php');
    $log = LogUtil::getInstance();
    $log->log("Deleted (".$rowCount.") expired urls.");
    echo "Deleted (".$rowCount.") expired urls.";
} else echo "No expired urls found.";
