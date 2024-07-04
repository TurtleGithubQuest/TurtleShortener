<?php global$user_language;?>
<nav>
    <section class="left">
        <!--<a class="title">Trt<sub>•</sub>ls</a>-->
    </section>
    <section class="right">
        <section class="collapsable">
            <section class="burger">
                <img src="img/svg/burger.svg" alt="burgerline1">
            </section>
            <section class="items">
                <form class="search" target="none" style="display: none">
                    <label><input name="q" type="text" placeholder="<?php echo $lang['search-url']; ?>"></label>
                    <input type="image" src="img/svg/magnifying-glass.svg" alt="Submit">
                </form>
                <div id="searchResult" class="d-none"></div>
            </section>
        </section>
        <div class="dropdown">
            <img src="img/svg/flag/<?php echo $user_language ?>.svg" alt="selected language"/>
            <?php
            $queryParams = getQueryParams();
            echo '
            <div class="dropdown-menu">
                <a href="'.buildQuery("lang", "en", $queryParams).'"><img src="img/svg/flag/en.svg" alt="English" />English</a>
                <a href="'.buildQuery("lang", "cz", $queryParams).'"><img src="img/svg/flag/cz.svg" alt="Czech" />Čeština</a>
            </div>';
            ?>
        </div>
    </section>
</nav>