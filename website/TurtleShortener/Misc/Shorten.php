<?php
declare(strict_types=1);

namespace TurtleShortener\Misc;

error_reporting(E_ALL);

if (isset($_GET['sid']) || isset($_POST['sid'])) {
    $session_id = $_GET['sid'] ?? $_POST['sid'];
    session_id($session_id);
}
session_start();

require_once(__DIR__. '/../bootstrap.php');
require_once(__DIR__ . '/../../composer/vendor/autoload.php');

use Throwable;
use TurtleShortener\Models\Shortened;
use Ulid\Ulid;
use function in_array;

class Shorten {
    private function handleUrlCreation(string $url): array {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("'$url' is not a valid url.");
        }

        $shortcode = $_POST['alias'] ?? null;
        $shortened = Shortened::fetchByUrl($shortcode, $url);

        if (!$shortened) {
            $shortened = $this->createNewShortened($url);
        }

        $this->updateSession($shortened);
        return ['url' => $shortened->shortenedUrl];
    }

    private function createNewShortened(string $url): Shortened {
        $alias = $_POST['alias'] ?? '!';
        $pattern = '/^[a-zA-Z0-9\-_.~]+$/';
        $shortcode = preg_match($pattern, $alias) ? $alias : substr(md5(uniqid((string)mt_rand(), true)), 0, 6);

        $expiry = $this->parseExpiry($_POST['expiration'] ?? '');
        $includeInSearch = !isset($_POST['searchable']) || $_POST['searchable'] === 'true';

        return Shortened::new(
            (string) Ulid::generate(),
            $shortcode,
            $_SERVER['HTTP_HOST'] . '/' . $shortcode,
            $url,
            $expiry,
            null,
            $includeInSearch
        );
    }

    private function parseExpiry(?string $expirationDate): ?int {
        if (empty($expirationDate)) {
            return null;
        }
        $timestamp = strtotime($expirationDate);
        return $timestamp !== false ? $timestamp : null;
    }

    private function updateSession(Shortened $shortened): void {
        if (!isset($_SESSION['shortened_array'])) {
            $_SESSION['shortened_array'] = [];
        }
        if (!in_array($shortened, $_SESSION['shortened_array'], false)) {
            $_SESSION['shortened_array'][] = $shortened;
        }
    }

    private function shouldRedirect(): bool {
        $host = filter_input(INPUT_SERVER, 'HTTP_HOST') ?? filter_input(INPUT_SERVER, 'REQUEST_URI');
        return isset($_SERVER['HTTP_REFERER']) &&
               (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) === $host ||
                parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) === parse_url($host, PHP_URL_HOST));
    }

    public function handleRequest(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
            try {
                $json_data = $this->handleUrlCreation($_POST['url']);

                if ($this->shouldRedirect()) {
                    header('Location: ' . $GLOBALS['utils']->getProtocol() . '://' . $_SERVER['HTTP_HOST'] . '/?sid=' . session_id());
                    return;
                }

                echo json_encode($json_data);

            } catch (Throwable $e) {
                $errorMessage = $e->getMessage() ?: $e->getTraceAsString();
                $_SESSION['error'] = 'Object creation error: ' . $errorMessage;
                $json_data = ['error' => $errorMessage];
                $GLOBALS['log']->debug(json_encode($errorMessage, JSON_THROW_ON_ERROR));

                if (!$this->shouldRedirect()) {
                    echo json_encode($json_data, JSON_THROW_ON_ERROR);
                }
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['s'])) {
            $shortcode = $_GET['s'];
            $shortened = Shortened::fetch($shortcode, false);

            if ($shortened) {
                header('Location: ' . $shortened->url);
                exit;
            }

            $_SESSION['error'] = "Shortcode '$shortcode' is not valid.";
            $_SESSION['error_code'] = '404';
            header('Location: /error.php');
            exit;
        }
    }

}

$handler = new Shorten();
$handler->handleRequest();
