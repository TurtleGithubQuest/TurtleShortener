include('head');
include('header');
<body>
<div class="wrapper">
    <div id="wrapper-content">
    <div class="tools">
        <form action="tools?t=clear" method="post">
            <input type="image" class="broom" src="img/svg/broom.svg" alt="Clean cookies" title="Clean cookies">
        </form>
    </div>
    <div class="index-box flex-col">
        <div class="title">Turtle Shortener <?php if(isset($isMobile) && $isMobile) echo '<div class="mobile">(mobile)</div>' ?></div>
        <form class="t-form flex-col" action="TurtleShortener/Misc/Shorten.php?sid=<?php
            if (isset($_GET['sid'])) echo $_GET['sid'];
            else echo session_id();
        ?>" method="post">
            <?php
                $expValue = $_GET["exp"] ?? 10080; # Default 1 week
                if ($expValue <= 0 || !filter_var($expValue, FILTER_VALIDATE_INT)) {
                    $expValue = 10080;
                }
                $expValue = (int)$expValue*60;
                $expTime = time() + ($expValue);
                $expirationDate = date('Y-m-d\TH:i', $expTime);
                $queryParams = $GLOBALS['utils']?->getQueryParams();
                echo '
                    <label for="url">translate("url-address")
                        <input type="url" name="url" placeholder="translate("url-address.placeholder")" spellcheck="false" maxlength="2083" required>
                    </label>
                    <label for="expiration">translate("expiration")
                        <input type="datetime-local" name="expiration" value="'.$expirationDate.'">
                    </label>
                    <div class="expiration-time">
                        <a href="'.$GLOBALS['utils']?->buildQuery("exp", 60, $queryParams).'">1 translate("hour")</a>
                        <a href="'.$GLOBALS['utils']?->buildQuery("exp", 2880, $queryParams).'">48 translate("hours")</a>
                        <a href="'.$GLOBALS['utils']?->buildQuery("exp", 10080, $queryParams).'">7 translate("days")</a>
                        <a href="'.$GLOBALS['utils']?->buildQuery("exp", 40320, $queryParams).'">1 translate("month")</a>
                    </div>
                    <label for="alias">Alias <input type="text" name="alias" placeholder="translate("alias.placeholder")" pattern="[a-zA-Z0-9\-_\.~]+" maxlength="6"></label>
                    <sup>[a-zA-Z0-9\-_\.~]+</sup>
                    <label for="searchable">translate("include_in_search")
                        <input type="text" name="searchable" value="'.(($_GET['searchable']??1) ? "true" : "false").'" hidden>
                        <a href="'.$GLOBALS['utils']?->buildQuery("searchable", !($_GET['searchable']??1), $queryParams).'">'.(($_GET['searchable'] ?? true) ? 'translate("1")' : 'translate("0")').'</a>
                    </label>
                    <input type="submit" value="translate("shorten")">';
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
            if ($size > 0) {
                echo '<div>' . $size . 'translate("shortened-found")</div>';
            }
            foreach (array_reverse($array, true) as $index => $value) {
                if (!is_string($value)) {
                    if ($value instanceof \TurtleShortener\Models\Shortened) {
                        $shortened = $value;
                    } else {
                        continue;
                    }
                } else {
                    $shortened = unserialize($value, ['allowed_classes' => [Shortened::class]]);
                }
                $url = $shortened->url;
                $shortenedUrl = $shortened->shortenedUrl;
                echo '<div class="result-table">
                    <div>'.$url.'</div>
                    <table>
                      <tr>
                        <th>translate("shortened-url")</th>
                        <td><a href="https://'.$shortenedUrl.'">'.$shortenedUrl.'</a>
                            <span class="copy-wrapper" title="click to copy url" copyValue="https://'.$shortenedUrl.'">
                                <img src="img/svg/copy.svg" alt="copy">
                                <img src="img/svg/success.svg" alt="copy-success">
                            </span>
                        </td>
                      </tr>
                      <tr>
                        <th>translate("created_at")</th>
                        <td unix="'.$shortened->created.'">'.$shortened->getCreationDate().'</td>
                      </tr>
                      <tr>
                        <th>translate("expiration")</th>
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
include('SeaEffects');
include('Scripts');
</body>