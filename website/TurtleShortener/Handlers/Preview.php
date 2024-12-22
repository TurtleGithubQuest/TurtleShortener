<?php

declare(strict_types=1);
namespace TurtleShortener\Page;

use TurtleShortener\Models\GeoData;
use TurtleShortener\Models\Shortened;
use Exception;
use ValueError;

$is_bot = false;
$preview_mode = $_GET['preview'] ?? false;
if (!\is_bool($preview_mode)) {
    $preview_mode = $preview_mode === 'true';
}
$shortened = null;

if (isset($_GET['s'])) {
    $shortcode = $_GET['s'];
} else {
    header('Location: /error.php?error=' . urlencode('no shortcode provided.'));
    exit;
}

try {
    require_once(__DIR__. '/../TurtleShortener/bootstrap.php');
    $shortened = Shortened::fetch($shortcode, $preview_mode);
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $is_bot = str_contains($userAgent, 'bot');
} catch(Exception $e) {
    $GLOBALS['log']->error('Error processing shortcode: '. $e->getMessage());
} finally {
    if ($shortened === null) {
        header('Location: /error.php?error=' . urlencode("404: '$shortcode' not found."));
        exit;
    }

    if (!$is_bot && !$preview_mode) {
        header('Location: ' . $shortened->url);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta property="og:type" content="website">
    <title></title>
    <?php
        $title = 'trt.ls';
        $options = [
            'http' => [
                'method' => 'GET',
                'header' => 'User-Agent: TurtleBot (trt.ls)'
            ]
        ];
        try {
            $html = @file_get_contents($shortened->url, false, stream_context_create($options));
            if ($html !== false) {
               preg_match_all('~<title>([^<]*)</title>|<meta property="og:description" content="([^<]*)"~i', $html, $matches);
            }
            $title = !empty($matches[1][0]) ? $matches[1][0] : null;
            $description = !empty($matches[2][0]) ? $matches[2][0] : null;
        } catch(ValueError $err) {
            echo 'Could not fetch the webpage content';
        } finally {
            echo '<title>'.$title.'</title>';
        }
        echo '<meta property="og:url" content="'.$shortened->url.'">';
        echo '<meta property="og:image" content="'.$shortened->url.'">';
        echo '<meta property="og:image:secure_url" content="'.$shortened->url.'">';
        echo '<meta property="og:title" content="'.$title.'">';
        if (isset($description)) {
            echo '<meta property="og:description" content="' . $description . '">';
        }
    ?>
<?php
if ($preview_mode) {
    $languages = [];
    $user_language = $_GET['lang'] ?? 'en';
    if (!\in_array($user_language, $languages, true)) {
        $user_language = 'en';
    }
    $geoDataSummary = GeoData::fetchSummary($shortened->ulid);
    include_once(__DIR__."/$user_language/preview.php");
} else {
    $geoData = GeoData::capture();
    $geoData?->saveToDatabase($shortened->ulid);
    echo '</head>';
}
?>
</html>
