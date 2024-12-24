<?php

declare(strict_types=1);
namespace TurtleShortener;

require_once(__DIR__ . '/../TurtleShortener/Handlers/BaseHandler.php');

class Image extends \TurtleShortener\Handlers\BaseHandler {
    public function __construct() {
        parent::__construct('image');
        global $img_name_length;
        global $img_extensions;

        $img_name_length = $GLOBALS['settings']['img_name_length'];
        $img_extensions = $GLOBALS['settings']['img_extensions'];
        $this->include_page();
    }

}

new Image();

