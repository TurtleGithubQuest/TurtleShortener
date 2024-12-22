<?php

declare(strict_types=1);
namespace TurtleShortener;

use TurtleShortener\Models\GeoData;

require_once(__DIR__ . '/../TurtleShortener/bootstrap.php');

if (isset($_GET['sid'])) {
    session_id($_GET['sid']);
}
session_start();
$languages = [];
$user_language = $_GET['lang'] ?? 'en';

if (!\in_array($user_language, $languages, false)) {
    header('Location: /error.php?error='.urlencode("Language '$user_language' is not supported."));
}

$isMobile = $_GET['m'] ?? false;
$page = $_GET['page'] ?? 'index';

if (
    ($page === 'stats')
) {
    $from = strtotime($_GET['from'] ?? date('Y-m-d'));
    $to = strtotime($_GET['to'] ?? date('Y-m-d'));
    $geoDataRangeSummary = GeoData::fetchDateRangeSummary($from, $to);
    echo '<script>const geoDataRangeSummary = ' . ($geoDataRangeSummary ?? 'null') . '</script>';
}

include_once(__DIR__.'/'.$user_language."/$page.php");
