<?php
namespace TurtleShortener {
    require_once(__DIR__.'/fn/Utils.php');
    require_once(__DIR__.'/db/DbUtil.php');
    require_once(__DIR__.'/fn/LogUtil.php');
    require_once(__DIR__.'/models/Shortened.php');
    use TurtleShortener\Misc\Utils;
    use TurtleShortener\Misc\LogUtil;

    global $user_language, $lang, $settings;

    $utils = new Utils();
    $utils->loadLanguage();
    $GLOBALS['log'] = LogUtil::getInstance();
    $GLOBALS['utils'] = $utils;
    $GLOBALS['settings'] = require_once(__DIR__ . '/settings.php');
    $settings = $GLOBALS['settings'];
}
