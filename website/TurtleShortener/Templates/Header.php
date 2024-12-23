<?php $queryParams = $GLOBALS['utils']?->getQueryParams(); ?>
<nav>
    <section class="left">
        <a class="title" href="/">Trt<sub>•</sub>ls</a>
    </section>
    <section class="right">
        <section class="collapsable" data-collapsable="false">
            <section class="burger">
                <img src="/img/svg/burger.svg" alt="burgerline1">
            </section>
            <section class="items">
                <!-- <a href="/info">translate('info')</a> -->
                <a href="/stats">translate('stats')</a>
                <form class="search" target="_self" action="/api/v1/search">
                    <label><input name="q" type="text" placeholder="translate('search-url')"></label>
                    <label hidden><input type="text" name="lang" value="%language_code%"></label>
                    <input type="image" src="/img/svg/magnifying-glass.svg" alt="Submit">
                </form>
                <div id="searchResult">
                    <?php
                    if (!empty($_GET['found'])) {
                        $results = json_decode(urldecode($_GET['found']), false, 512, JSON_THROW_ON_ERROR);
                        $host = explode(':', $_SERVER['HTTP_HOST'])[0]. '/';
                        $appendNothing = true;
                        foreach ($results as $result) {
                            $appendNothing = false;
                            $r_url = $result->url ?? '';
                            $r_shortcode = $result->shortcode ?? '';
                            if (!empty($r_shortcode)) {
                                $r_shortcode .= '+';
                            }
                            echo '<div class="result">
                                <a class="shortcode" href="'.$r_shortcode.'">[?]</a>
                                <a href="'.$r_url.'">'.preg_replace('/(http:\/\/|https:\/\/|www\.)/', '', $r_url).'</a>
                            </div>';
                        }
                        if ($appendNothing) {
                            echo '<div class="result">translate("found-nothing")</div>';
                        }
                    }
                    ?>
                </div>
            </section>
        </section>
        <div class="dropdown">
            <img src="/img/svg/flag/%language_code%.svg" alt="selected language"/>
            <?='
            <div class="dropdown-menu">
                <a href="' . $GLOBALS['utils']?->buildQuery('lang', 'en', $queryParams) . '"><img src="/img/svg/flag/en.svg" alt="English" />English</a>
                <a href="' . $GLOBALS['utils']?->buildQuery('lang', 'cz', $queryParams) . '"><img src="/img/svg/flag/cz.svg" alt="Czech" />Čeština</a>
            </div>
            '?>
        </div>
    </section>
</nav>
<div id="alerts"></div>