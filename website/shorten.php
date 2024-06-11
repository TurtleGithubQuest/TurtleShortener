<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/php/db/util.php');
require_once(__DIR__ . '/php/model/short.php');
require_once(__DIR__ . '/../vendor/robinvdvleuten/ulid/src/ulid.php');
use Ulid\Ulid;
$pdo = DbUtil::getPdo();

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['url'])) {
    $url = $_POST['url'];
    $_SESSION["url"] = $url;
    header("Location: index.php");
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $_SESSION["error"] = "'$url' is not a valid url.";
        exit;
    }
    $expiry = null;
    if (!empty($_POST['expiration'])) {
        $timestamp = strtotime($_POST['expiration']);
        if ($timestamp !== false)
            $expiry = $timestamp;
    }
    $ulid = Ulid::generate();
    $shortcode = substr(md5(uniqid(rand(), true)), 0, 6);
    $stmt = $pdo->prepare("INSERT INTO urls (ulid, shortcode, url, expiry) VALUES (?, ?, ?, ?)");
    $stmt->execute([$ulid, $shortcode, $url, $expiry]);
    $_SESSION["shortenedUrl"] = $_SERVER['HTTP_HOST'] . '/' . $shortcode;
    try {
        $_SESSION["shortened"] = serialize(new Shortened($shortcode, $_SESSION["shortenedUrl"], $url, $expiry));
    } catch(Throwable $e) {
        $_SESSION["error"] = '<script>console.log("Object creation error: '.$e->getMessage().'")</script>';
    }
    $_SESSION["shortcode"] = $shortcode;
    $_SESSION["expiry"] = $expiry;
    exit;
} else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['shortcode'])) {
    $shortCode = $_GET['shortcode'];

    $stmt = $pdo->prepare("SELECT url FROM urls WHERE shortcode = ?");
    $stmt->execute([$shortCode]);
    $url = $stmt->fetchColumn();

    if($url) {
        header('Location: ' . $url);
        exit;
    }

    echo "URL not found!";
}
?>
