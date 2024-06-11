<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="css/turtle.css">
</head>
<body>
<div class="index-box flex-col">
    <div class="title">Turtle Shortener</div>
    <form class="t-form flex-col" action="shorten.php" method="post">
        <label>Url to shorten<input type="url" name="url" placeholder="url.." spellcheck="false" maxlength="2083" required></label>
        <label>Expiration<input type="date" name="expiration"></label>
        <input type="submit" value="Shorten">
    </form>
    <?php
    require_once(__DIR__ . '/php/model/short.php');
    if (isset($_SESSION["error"])) {
        echo '<p>$_SESSION["error"]</p>';
        unset($_SESSION["error"]);
        exit;
    }
    $shortened = unserialize($_SESSION["shortened"]);
    if(isset($shortened)) {
        $url = $shortened->url;
        $shortenedUrl = $shortened->shortenedUrl;
        $expiry = $shortened->expiry;
        echo '<div class="table"> 
            <table>
              <tr>
                <th>url</th>
                <td><a href="'.$url.'">'.$url.'</a></td>
              </tr>
              <tr>
                <th>shortened url</th>
                <td><a href="'.$shortenedUrl.'">'.$shortenedUrl.'</a></td>
              </tr>
              <tr>
                <th>expiration</th>
                <td>'.$shortened->getExpiryFormatted().'</td>
              </tr>
            </table></div>
        ';
        //unset($_SESSION["shortcode"]);
        //unset($_SESSION["expiry"]);
        //unset($_SESSION["shortenedUrl"]);
    }
    ?>
</div>
</body>
</html>