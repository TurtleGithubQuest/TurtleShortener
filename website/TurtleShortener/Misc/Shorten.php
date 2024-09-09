<?php
namespace TurtleShortener\Misc;

error_reporting(E_ALL);
if (isset($_GET["sid"])) {
    session_id($_GET["sid"]);
} else if (isset($_POST["sid"])) {
    session_id($_POST["sid"]);
}
session_start();

/*require_once(__DIR__ . '/../db/util.php');
require_once(__DIR__ . '/../model/short.php');
require_once(__DIR__ . '/utils.php');*/
require_once(__DIR__. '/../bootstrap.php');
require_once(__DIR__ . '/../../composer/vendor/autoload.php');

use Throwable;
use TurtleShortener\Database\DbUtil;
use TurtleShortener\Models\Shortened;
use Ulid\Ulid;

$dbUtil = new DbUtil();
$pdo = $dbUtil::getPdo();

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    $url = (string) $_POST['url'];
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $_SESSION["error"] = "'$url' is not a valid url.";
        exit;
    }
    $host = filter_input(INPUT_SERVER, 'HTTP_HOST') ?? filter_input(INPUT_SERVER, 'REQUEST URI');
    $should_redirect = isset($_SERVER['HTTP_REFERER']) && (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) === $host || parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) === parse_url($host, PHP_URL_HOST));
    if (!isset($_SESSION["shortened_array"]))
        $_SESSION["shortened_array"] = array();
    try {
        $shortcode = $_POST["alias"]??null;
        $shortened = Shortened::fetch_by_url($shortcode, $url);
        $searchable = !isset($_POST['searchable']) || $_POST['searchable'] === 'true';
        //$stmt = $pdo->prepare("SELECT shortcode, expiry, created FROM urls WHERE url = ? OR shortcode = ?");
        //$stmt->execute([$url, ]);
        //$data = $stmt->fetch();
        if (!$shortened) {
            $alias = $_POST["alias"] ?? "!";
            $pattern = '/^[a-zA-Z0-9\-_.~]+$/';
            $shortcode = preg_match($pattern, $alias) ? $alias : substr(md5(uniqid(mt_rand(), true)), 0, 6);

            $expiry = (!empty($_POST['expiration']) && ($timestamp = strtotime($_POST['expiration'])) !== false) ? $timestamp : null;
            $shortened = new Shortened(
                Ulid::generate(),
                $shortcode,
                $_SERVER['HTTP_HOST'] . '/' . $shortcode,
                $url,
                $expiry,
                null,
                $searchable
            );
           //$shortcode = $data["shortcode"];
           //$expiry = (int) $data["expiry"];
           //$created = strtotime($data['created']);
        }// else {
           // $stmt = $pdo->prepare("INSERT INTO urls (ulid, shortcode, url, expiry, searchable) VALUES (?, ?, ?, ?, ?)");
           // $stmt->execute([$ulid, $shortcode, $url, $expiry, $searchable]);
           // $created = null;
        //}
        //$shortened = serialize(new Shortened($shortcode, $shortenedUrl, $url, $expiry, $created));
        $ser_shortened = serialize($shortened);
        if (!in_array($shortened, $_SESSION["shortened_array"], false)) {
            $_SESSION["shortened_array"][] = $shortened;
        }
        // Request is probably not from user interface, create json data;
        if (!$should_redirect) {
            $json_data = ['url' => $shortened->shortenedUrl];
        }
    } catch(Throwable $e) {
        $errorMessage = $e->getMessage();
        if (!isset($errorMessage)) {
            $errorMessage = $e->getTraceAsString();
        }
        $_SESSION["error"] = 'Object creation error: ' . $errorMessage;
        $json_data = ['error' => $errorMessage];
        $GLOBALS['log']->debug(json_encode($errorMessage, JSON_THROW_ON_ERROR));
    } finally {
        if ($should_redirect) {
            header('Location: ' . $GLOBALS['utils']->getProtocol() . '://' . $_SERVER['HTTP_HOST'] . '/?sid=' . session_id());
        } else {
            echo json_encode($json_data);
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['s'])) {
    $shortcode = $_GET['s'];
    $shortened = Shortened::fetch($shortcode, false);
    /*$stmt = $pdo->prepare("SELECT url FROM urls WHERE shortcode = ?");
    $stmt->execute([$shortCode]);
    $url = $stmt->fetchColumn();*/

    if($shortened) {
        header('Location: ' . $shortened->url);
        exit;
    }
    $_SESSION["error"] = "Shortcode '$shortcode' is not valid.";
    $_SESSION["error_code"] = "404";
    header('Location: /error.php');
}
