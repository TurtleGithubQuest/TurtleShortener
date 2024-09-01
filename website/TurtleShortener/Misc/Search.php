<?php
namespace TurtleShortener\Misc;

global $lang;
require_once(__DIR__ . '/../bootstrap.php');

use PDO;
use TurtleShortener\Database\DbUtil;

$GLOBALS['utils']->loadLanguage();
$q = $_POST['q'] ?? $_GET['q'];
function dataReturn($data): void {
    global $user_language;
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
        echo $data;
    else
        header('Location: /?found='.urlencode($data).'&lang='.$user_language);
}
if(empty($q)) {
    http_response_code(404);
    dataReturn(json_encode(array(null => array("url"=>$lang["found-nothing"]))));
    exit;
}
$pdo = DbUtil::getPdo();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$searchQuery = '%'.$q.'%';
$host = '%'.explode(':', $_SERVER['HTTP_HOST'])[0].'%';
//Do not change to wildcard
$stmt = $pdo->prepare("SELECT ulid, shortcode, url, expiry, created FROM urls WHERE (shortcode LIKE ? OR url LIKE ?) AND url NOT LIKE ? AND (searchable != 0 OR searchable IS NULL) LIMIT 10");
$stmt->execute([$searchQuery, $searchQuery, $host]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($data))
    $data = array(null => array("url"=>$lang["found-nothing"]));
$data = json_encode($data);
dataReturn($data);
