<nav>
    <section class="left">
        <!--<a class="title">Trt<sub>•</sub>ls</a>-->
    </section>
    <section class="right">
        <section class="collapsable" data-collapsable="false">
            <section class="burger">
                <img src="/img/svg/burger.svg" alt="burgerline1">
            </section>
            <section class="items">
                <form class="search" target="_self" action="/api/v1/search">
                    <label><input name="q" type="text" placeholder="translate('search-url')"></label>
                    <label hidden><input type="text" name="lang" value="%language_code%"></label>
                    <input type="image" src="/img/svg/magnifying-glass.svg" alt="Submit">
                </form>
                <?php
                echo '<div id="searchResult"'.(empty($_GET["found"]) ? ' class="d-none"' : '').'>';
                if (!empty($_GET["found"])) {
                    $results = json_decode(urldecode($_GET["found"]), false, 512, JSON_THROW_ON_ERROR);
                    $host = explode(':', $_SERVER['HTTP_HOST'])[0]."/";
                    foreach ($results as $result) {
                        $r_url = $result->url ?? "";
                        $r_shortcode = $result->shortcode ?? "";
                        if (!empty($r_shortcode))
                            $r_shortcode .= '+';
                        echo '<div class="result">
                            <a class="shortcode" href="'.$r_shortcode.'">[?]</a>
                            <a href="'.$r_url.'">'.preg_replace("/(http:\/\/|https:\/\/|www\.)/", "", $r_url).'</a>
                        </div>';
                    }
                }
                echo '</div>';
                ?>
            </section>
        </section>
        <div class="dropdown">
            <img src="/img/svg/flag/%language_code%.svg" alt="selected language"/>
            <?php
            $queryParams = $GLOBALS['utils']?->getQueryParams();
            echo '
            <div class="dropdown-menu">
                <a href="'.$GLOBALS['utils']?->buildQuery("lang", "en", $queryParams).'"><img src="/img/svg/flag/en.svg" alt="English" />English</a>
                <a href="'.$GLOBALS['utils']?->buildQuery("lang", "cz", $queryParams).'"><img src="/img/svg/flag/cz.svg" alt="Czech" />Čeština</a>
            </div>';
            ?>
        </div>
    </section>
</nav>
<div id="alerts">
<label class="alert">
    <input type="checkbox" autocomplete="off" />
    <span class="notification-info">></span>
    <span class="content info">
        test <br>test
    </span>
</label>
<label class="alert">
    <input type="checkbox" autocomplete="off" />
    <span class="notification-info">></span>
    <span class="content info">
        1test <br>test2
    </span>
</label>
</div>