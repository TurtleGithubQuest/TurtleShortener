<?php
if (isset($_GET["sid"]))
    session_id($_GET["sid"]);
session_start();
require_once(__DIR__ . '/php/model/short.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>trt.ls</title>
    <link rel="stylesheet" href="css/turtle.css">
    <link rel="stylesheet" href="css/third-party/josetxu_turtle.css">
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">
    <script src="js/turtle.js"></script>
</head>
<body>
<div class="tools">
    <form action="php/tools.php?t=clear" method="post">
        <input type="image" class="broom" src="img/svg/broom.svg" alt="Clean cookies">
    </form>
</div>
<div class="turtle-box" style="position:absolute; top: 20%; right: 2%; opacity: 80%">
    <div class="bubbles">
        <div class="bubble b1"></div>
        <div class="bubble b2"></div>
        <div class="bubble b3"></div>
        <div class="bubble b4"></div>
        <div class="bubble b5"></div>
        <div class="bubble b6"></div>
        <div class="bubble b7"></div>
        <div class="bubble b8"></div>
        <div class="bubble b9"></div>
        <div class="bubble b10"></div>
    </div>
    <div class="bubbles mirror">
        <div class="bubble b1"></div>
        <div class="bubble b2"></div>
        <div class="bubble b3"></div>
        <div class="bubble b4"></div>
        <div class="bubble b5"></div>
        <div class="bubble b6"></div>
        <div class="bubble b7"></div>
        <div class="bubble b8"></div>
        <div class="bubble b9"></div>
        <div class="bubble b10"></div>
    </div>
    <div class="turtle">
        <div class="head"><div class="eyes"></div></div>
        <div class="leg1"></div>
        <div class="leg2"></div>
        <div class="leg3"></div>
        <div class="leg4"></div>
        <div class="tail"></div>
        <div class="body"><span></span></div>
        <div class="body-tail"></div>
    </div>
</div>
<div class="index-box flex-col">
    <div class="title">Turtle Shortener</div>
    <form class="t-form flex-col" action="shorten.php?sid=<?php
        if (isset($_GET['sid'])) echo $_GET['sid'];
        else echo session_id();
    ?>" method="post">
        <label>Url address<input type="text" name="url" placeholder="very long url.." spellcheck="false" maxlength="2083" required></label>
        <label>Expiration<input type="datetime-local" name="expiration" value="<?php echo date('Y-m-d\TH:i', strtotime('+1 week')); ?>"></label>
        <input type="submit" value="Shorten">
    </form>
    <section class="results flex-col"><?php
    if (isset($_SESSION["error"])) {
        echo '<p>'.$_SESSION["error"].'</p>';
        unset($_SESSION["error"]);
        exit;
    }
    if (isset($_SESSION["shortened_array"])) {
        $array = $_SESSION["shortened_array"];
        $size = count($array);
        if ($size > 0)
            echo "<div>$size shortened url(s) found:</div>";
        foreach (array_reverse($array, true) as $index => $value) {
            $shortened = unserialize($value);
            $url = $shortened->url;
            $shortenedUrl = $shortened->shortenedUrl;
            echo '<div class="result-table">
                <div>'.$url.'</div>
                <table>
                  <tr>
                    <th>shortened url</th>
                    <td><a href="https://'.$shortenedUrl.'">'.$shortenedUrl.'</a>
                        <span class="copy-wrapper" title="click to copy url" onclick="copyValue(this)" copyValue="https://'.$shortenedUrl.'">
                            <img src="img/svg/copy.svg" alt="copy">
                            <img src="img/svg/success.svg" alt="copy-success">
                        </span>
                    </td>
                  </tr>
                  <tr>
                    <th>created at</th>
                    <td unix="'.$shortened->created.'">'.$shortened->getCreationDate().'</td>
                  </tr>
                  <tr>
                    <th>expiration</th>
                    <td unix="'.$shortened->expiry.'">'.$shortened->getExpiryFormatted().'</td>
                  </tr>
                </table></div>
            ';
        }
    }
    ?>
    </section>
</div>
<script>
window.addEventListener('DOMContentLoaded', async (e) => {
    const dateInput = document.querySelector('input[type=datetime-local]')
    updateInputElementDate(dateInput)
    for (const el of document.querySelectorAll('[unix]')) {
        const unix = el.getAttribute('unix')*1000;
        updateElementTextDate(el, unix);
    }
});
</script>
</body>
</html>