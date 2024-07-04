<?php
if($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_POST['q'])) {
    http_response_code(404);
    echo json_encode(array(null=>"Nothing found"));
    exit;
}
require_once(__DIR__."/../db/util.php");
$pdo = DbUtil::getPdo();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$searchQuery = '%'.$_POST['q'].'%';
$host = '%'.explode(':', $_SERVER['HTTP_HOST'])[0].'%';
//Do not change to wildcard
$stmt = $pdo->prepare( "SELECT ulid, shortcode, url, expiry, created FROM urls WHERE (shortcode LIKE ? OR url LIKE ?) AND url NOT LIKE ? LIMIT 10");
$stmt->execute([$searchQuery, $searchQuery, $host]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($data))
    $data = array(null => "Nothing found");
echo json_encode($data);