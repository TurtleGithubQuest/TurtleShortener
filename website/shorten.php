<?php
error_reporting(E_ALL);
if (isset($_GET["sid"]))
    session_id($_GET["sid"]);
else if (isset($_POST["sid"]))
    session_id($_POST["sid"]);
session_start();

require_once(__DIR__ . '/php/db/util.php');
require_once(__DIR__ . '/php/model/short.php');
require_once(__DIR__ . '/composer/vendor/autoload.php');
use Ulid\Ulid;
$pdo = DbUtil::getPdo();

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['url'])) {
    $host = filter_input(INPUT_SERVER, 'HTTP_HOST') ?? filter_input(INPUT_SERVER, 'REQUEST URI');
    //header("Location: index.php");
    $url = (string) $_POST['url'];
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
    try {
        $stmt = $pdo->prepare("SELECT shortcode, expiry, timestamp FROM urls WHERE url = ?");
        $stmt->execute([$url]);
        $data = $stmt->fetch();
        if ($data) {
           $shortcode = $data["shortcode"];
           $expiry = (int) $data["expiry"];
           $timestamp = strtotime($data['timestamp']);
        } else {
            $ulid = Ulid::generate();
            $shortcode = substr(md5(uniqid(rand(), true)), 0, 6);
            $stmt = $pdo->prepare("INSERT INTO urls (ulid, shortcode, url, expiry) VALUES (?, ?, ?, ?)");
            $stmt->execute([$ulid, $shortcode, $url, $expiry]);
            $timestamp = null;
        }
        $shortenedUrl = $_SERVER['HTTP_HOST'] . '/' . $shortcode;
        $shortened = serialize(new Shortened($shortcode, $shortenedUrl, $url, $expiry, $timestamp));
        if (!in_array($shortened, $_SESSION["shortened_array"]))
            $_SESSION["shortened_array"][] = $shortened;
        // Request is probably from user interface, redirect;
        if (isset($_SERVER['HTTP_REFERER']) && (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) == $host || parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) == parse_url($host, PHP_URL_HOST))) {
            $sessionId = session_id();
            header("Location: index.php?sid=$sessionId");
        } else {
            echo json_encode(['url' => $shortenedUrl]);
        }
    } catch(Throwable $e) {
        $errorMessage = $e->getMessage();
        if (!isset($errorMessage)) $errorMessage = $e->getTraceAsString();
        $_SESSION["error"] = '<script>console.error("Object creation error: '.$errorMessage.'")</script>';
    }
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
