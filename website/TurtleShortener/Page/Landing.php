<?php
namespace TurtleShortener;

use TurtleShortener\Models\Shortened;

require_once(__DIR__ . '/../TurtleShortener/bootstrap.php');

if (isset($_GET["sid"])) {
    session_id($_GET["sid"]);
}
session_start();
$languages = [];
$user_language = "%language_code%";

if (!in_array($user_language, $languages, false)) {
    header('Location: /error.php?error='.urlencode("Language '$user_language' is not supported."));
}
//$lang = $GLOBALS['lang'];
$isMobile = $_GET['m'] ?? false;
$page = $_GET['page'] ?? false;
?>
<?php
if ($page) {
    include_once(__DIR__.'/'.$user_language."/$page.php");
} else {
    include_once(__DIR__.'/'.$user_language.'/index.php');
}
?>