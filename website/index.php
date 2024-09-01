<?php
namespace TurtleShortener;

require_once(__DIR__ . '/TurtleShortener/bootstrap.php');

if (isset($_GET["sid"])) {
    session_id($_GET["sid"]);
}
session_start();

?>
<!DOCTYPE html>
<html lang="<?php echo $user_language; ?>">
<head>
    <meta charset="UTF-8">
    <title>trt.ls</title>
    <link rel="stylesheet" href="css/turtle.css">
    <link rel="stylesheet" href="css/third-party/josetxu_turtle.css">
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">
</head>
<body>
<?php include_once __DIR__ . "/TurtleShortener/Layout/Header.php" ?>
<div class="wrapper">
    <div id="wrapper-content">
    <div class="tools">
        <form action="TurtleShortener/Tools.php?t=clear" method="post">
            <input type="image" class="broom" src="img/svg/broom.svg" alt="Clean cookies" title="Clean cookies">
        </form>
    </div>
    <div id="sea-effects">
        <div class="bubbley bubbley--1"></div>
        <div class="bubbley bubbley--2"></div>
        <div class="bubbley bubbley--3"></div>
        <div class="bubbley bubbley--4"></div>
        <div class="bubbley bubbley--5"></div>
        <div class="bubbley bubbley--6"></div>
        <div class="bubbley bubbley--7"></div>
        <div class="bubbley bubbley--8"></div>
        <div class="bubbley bubbley--9"></div>
        <div class="bubbley bubbley--10"></div>
        <div class="bubbley bubbley--11"></div>
        <div class="bubbley bubbley--12"></div>
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
    </div>
    <div class="index-box flex-col">
        <div class="title">Turtle Shortener</div>
        <form class="t-form flex-col" action="TurtleShortener/Misc/Shorten.php?sid=<?php
            if (isset($_GET['sid'])) echo $_GET['sid'];
            else echo session_id();
        ?>" method="post">
            <?php
                $expValue = $_GET["exp"] ?? 10080; # Default 1 week
                if (!filter_var($expValue, FILTER_VALIDATE_INT) || $expValue <= 0) {
                    $expValue = 10080;
                }
                $expValue = (int)$expValue*60;
                $expTime = time() + ($expValue);
                $expirationDate = date('Y-m-d\TH:i', $expTime);
                $queryParams = $GLOBALS['utils']?->getQueryParams();
                echo '
                    <label>'.$lang['url-address'].'<input type="url" name="url" placeholder="'.$lang['url-address.placeholder'].'" spellcheck="false" maxlength="2083" required></label>
                    <label>'.$lang['expiration'].'<input type="datetime-local" name="expiration" value="'.$expirationDate.'"></label>
                    <div class="expiration-time">
                        <a href="'.$GLOBALS['utils']?->buildQuery("exp", 360, $queryParams).'">6 '.$lang['hours'].'</a>
                        <a href="'.$GLOBALS['utils']?->buildQuery("exp", 2880, $queryParams).'">48 '.$lang['hours'].'</a>
                        <a href="'.$GLOBALS['utils']?->buildQuery("exp", 20160, $queryParams).'">14 '.$lang['days'].'</a>
                        <a href="'.$GLOBALS['utils']?->buildQuery("exp", 40320, $queryParams).'">1 '.$lang['month'].'</a>
                    </div>
                    <label for="alias">Alias <input type="text" name="alias" placeholder="'.$lang['alias.placeholder'].'" pattern="[a-zA-Z0-9\-_\.~]+" maxlength="6"></label>
                    <sup>[a-zA-Z0-9\-_\.~]+</sup>
                    <label>'.$lang['include_in_search'].'<input type="text" name="searchable" value="'.(($_GET['searchable']??1) ? "true" : "false").'" hidden>
                    <a href="'.$GLOBALS['utils']?->buildQuery("searchable", !($_GET['searchable']??1), $queryParams).'">'.$lang[$_GET['searchable']??1].'</a></label>
                    <input type="submit" value="'.$lang['shorten'].'">';
            ?>
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
                echo '<div>'.$size.$lang['shortened-found'].'</div>';
            foreach (array_reverse($array, true) as $index => $value) {
                $shortened = unserialize($value);
                $url = $shortened->url;
                $shortenedUrl = $shortened->shortenedUrl;
                echo '<div class="result-table">
                    <div>'.$url.'</div>
                    <table>
                      <tr>
                        <th>'.$lang['shortened-url'].'</th>
                        <td><a href="https://'.$shortenedUrl.'">'.$shortenedUrl.'</a>
                            <span class="copy-wrapper" title="click to copy url" copyValue="https://'.$shortenedUrl.'">
                                <img src="img/svg/copy.svg" alt="copy">
                                <img src="img/svg/success.svg" alt="copy-success">
                            </span>
                        </td>
                      </tr>
                      <tr>
                        <th>'.$lang['created-at'].'</th>
                        <td unix="'.$shortened->created.'">'.$shortened->getCreationDate().'</td>
                      </tr>
                      <tr>
                        <th>'.$lang['expiration'].'</th>
                        <td unix="'.$shortened->expiry.'">'.$shortened->getExpiryFormatted().'</td>
                      </tr>
                    </table></div>
                ';
            }
        }
        ?>
        </section>
    </div>
    </div>
</div>
<iframe style="display: none" id="none"></iframe>
<script src="js/turtle.js"></script>
</body>
</html>