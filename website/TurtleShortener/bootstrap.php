<?php
namespace TurtleShortener {
    require_once(__DIR__ . '/Misc/Utils.php');
    require_once(__DIR__.'/Database/DbUtil.php');
    require_once(__DIR__ . '/Misc/LogUtil.php');
    require_once(__DIR__.'/Models/Shortened.php');
    use TurtleShortener\Misc\Utils;
    use TurtleShortener\Misc\LogUtil;

    global $user_language, $lang, $settings;

    $utils = new Utils();
    //$utils::loadLanguage();
    $GLOBALS['log'] = LogUtil::getInstance();
    $GLOBALS['utils'] = $utils;
    $GLOBALS['settings'] = require(__DIR__ . '/settings.php');
    $settings = $GLOBALS['settings'];
}
