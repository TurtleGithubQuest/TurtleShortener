<?php
namespace TurtleShortener\Misc;

require_once(__DIR__ . '/../bootstrap.php');

use JetBrains\PhpStorm\NoReturn;
use PDO;
use TurtleShortener\Database\DbUtil;

$q = $_POST['q'] ?? ($_GET['q'] ?? null);
#[NoReturn] function dataReturn($data): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($data)) {
            http_response_code(404);
        }
        header('Content-type: application/json');
        echo $data;
    } else {
        header('Location: /?found=' . urlencode($data));
    }
    exit;
}
if(empty($q)) {
    dataReturn(null);
}
$pdo = DbUtil::getPdo();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$searchQuery = '%'.$q.'%';
$host = '%'.explode(':', $_SERVER['HTTP_HOST'])[0].'%';
//Do not change to wildcard
$stmt = $pdo->prepare("SELECT ulid, shortcode, url, expiry, created FROM urls WHERE (shortcode LIKE ? OR url LIKE ?) AND url NOT LIKE ? AND (searchable != 0 OR searchable IS NULL) LIMIT 10");
$stmt->execute([$searchQuery, $searchQuery, $host]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$data = json_encode($data);
dataReturn($data);
