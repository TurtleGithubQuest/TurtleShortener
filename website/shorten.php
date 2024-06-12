<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/php/db/util.php');
require_once(__DIR__ . '/php/model/short.php');
require_once(__DIR__ . '/composer/vendor/autoload.php');
use Ulid\Ulid;
$pdo = DbUtil::getPdo();

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['url'])) {
    header("Location: index.php");
    $url = $_POST['url'];
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $_SESSION["error"] = "'$url' is not a valid url.";
        exit;
    }
    if (!isset($_SESSION["shortened_array"]))
        $_SESSION["shortened_array"] = array();
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
    try {
        $shortenedUrl = $_SERVER['HTTP_HOST'] . '/' . $shortcode;
        $shortened = serialize(new Shortened($shortcode, $shortenedUrl, $url, $expiry));
        $_SESSION["shortened_array"][] = $shortened;
    } catch(Throwable $e) {
        $_SESSION["error"] = '<script>console.log("Object creation error: '.$e->getMessage().'")</script>';
    }
    exit;
} else if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['s'])) {
    $shortCode = $_GET['s'];

    $stmt = $pdo->prepare("SELECT url FROM urls WHERE shortcode = ?");
    $stmt->execute([$shortCode]);
    $url = $stmt->fetchColumn();

    if($url) {
        header('Location: ' . $url);
        exit;
    }
    $_SESSION["error"] = "Shortcode '$shortCode' is not valid.";
    $_SESSION["error_code"] = "404";
    header('Location: /error.php');
}
