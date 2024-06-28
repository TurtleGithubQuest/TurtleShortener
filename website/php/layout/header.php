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
                <a><?php echo $lang['preview-url']; ?></a>
            </section>
        </section>
        <div class="dropdown">
            <img src="img/svg/flag/<?php echo $user_language ?>.svg" alt="selected language"/>
            <div class="dropdown-menu">
                <a href="?lang=en"><img src="img/svg/flag/en.svg" alt="English" />English</a>
                <a href="?lang=cz"><img src="img/svg/flag/cz.svg" alt="Czech" />Čeština</a>
            </div>
        </div>
    </section>
</nav>

