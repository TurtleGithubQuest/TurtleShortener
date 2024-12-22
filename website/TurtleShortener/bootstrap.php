<?php
declare(strict_types=1);

namespace TurtleShortener {
    require_once(__DIR__.'/Misc/Utils.php');
    require_once(__DIR__.'/Database/DbUtil.php');
    require_once(__DIR__.'/Misc/LogUtil.php');
    require_once(__DIR__.'/Misc/AccessLevel.php');
    require_once(__DIR__.'/Models/Shortened.php');
    require_once(__DIR__.'/Models/GeoData.php');
    require_once(__DIR__.'/Admin/CommandExecutor.php');
    require_once(__DIR__.'/Database/UpKeep.php');
    require_once(__DIR__.'/Database/Migrate.php');
    require_once(__DIR__.'/Admin/Build.php');
    require_once(__DIR__.'/Admin/StatSummary.php');
    require_once(__DIR__.'/Misc/Search.php');
    use TurtleShortener\Misc\Utils;
    use TurtleShortener\Misc\LogUtil;

    $utils = new Utils();
    $GLOBALS['log'] = LogUtil::getInstance();
    $GLOBALS['utils'] = $utils;
    $GLOBALS['settings'] = require(__DIR__ . '/settings.php');
}
