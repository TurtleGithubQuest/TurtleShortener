<?php
namespace TurtleShortener\Page;

use TurtleShortener\Models\GeoData;
use TurtleShortener\Models\Shortened;
use Exception;
use ValueError;

$is_bot = false;
$preview_mode = $_GET['preview'] ?? false;
try {
    if (isset($_GET['s'])) {
        require_once(__DIR__. '/../TurtleShortener/bootstrap.php');
        //require_once(__DIR__ . '/../db/util.php');
        $shortcode = $_GET['s'];
        try {
            $shortened = Shortened::fetch($shortcode, $preview_mode);
        } catch (Exception $e) {
            echo "Error fetching data.";
            exit;
        }
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $is_bot = str_contains($userAgent, "bot");
    } else {
        echo "No shortcode provided.";
        exit;
    }
} catch(Exception $e) {
} finally {
    if (!empty($shortened->url)) {
        if (!$is_bot && !$preview_mode) {
            header('Location: ' . $shortened->url);
        }
    } else {
        header('Location: /error.php?error=' . urlencode('Shortened url "' . ($shortened->url ?? 'none') . '" not found.'));
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
        $title = "trt.ls";
        $options = array(
            'http' => array(
                'method' => 'GET',
                'header' => 'User-Agent: TurtleBot (trt.ls)'
            )
        );
        try {
            $html = @file_get_contents($shortened->url, false, stream_context_create($options));
            preg_match_all('~<title>([^<]*)</title>|<meta property="og:description" content="([^<]*)"~i', $html, $matches);
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
    //require_once(__DIR__ . '/../model/short.php');
    $languages = [];
    $user_language = $_GET['lang'] ?? "en";
    if (!in_array($user_language, $languages)) {
        $user_language = "en";
    }
    include_once(__DIR__.'/'.$user_language.'/preview.php');
} else {
    $geoData = GeoData::capture(null);
    $geoData?->saveToDatabase($shortened->shortcode);
    echo '</head>';
}
?>
</html>
