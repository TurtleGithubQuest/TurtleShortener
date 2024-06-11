<?php
session_start();
require_once(__DIR__ . '/php/model/short.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>trt.ls</title>
    <link rel="stylesheet" href="css/turtle.css">
</head>
<body>
<div class="tools">
    <form action="php/fix.php" method="post">
        <input type="image" class="broom" src="svg/broom.svg" alt="Clean cookies">
    </form>
</div>
<div class="index-box flex-col">
    <div class="title">Turtle Shortener</div>
    <form class="t-form flex-col" action="shorten.php" method="post">
        <label>Url address<input type="url" name="url" placeholder="very long url.." spellcheck="false" maxlength="2083" required></label>
        <label>Expiration<input type="date" name="expiration"></label>
        <input type="submit" value="Shorten">
    </form>
    <section class="results flex-col"><?php
    if (isset($_SESSION["error"])) {
        echo '<p>$_SESSION["error"]</p>';
        unset($_SESSION["error"]);
        exit;
    }
    if (isset($_SESSION["shortened_array"])) {
        $array = $_SESSION["shortened_array"];
        $size = count($array);
        echo "<div>$size result(s) found:</div>";
        foreach (array_reverse($_SESSION["shortened_array"], true) as $index => $value) {
            $shortened = unserialize($value);
            $url = $shortened->url;
            $shortenedUrl = $shortened->shortenedUrl;
            echo '<div class="result-table">
                <div>'.$url.'</div>
                <table>
                  <tr>
                    <th>shortened url</th>
                    <td><a href="https://'.$shortenedUrl.'">https://'.$shortenedUrl.'</a></td>
                  </tr>
                  <tr>
                    <th>created at</th>
                    <td>'.$shortened->getCreationDate().'</td>
                  </tr>
                  <tr>
                    <th>expiration</th>
                    <td>'.$shortened->getExpiryFormatted().'</td>
                  </tr>
                </table></div>
            ';
        }
    }
    ?>
    </section>
</div>
</body>
</html>