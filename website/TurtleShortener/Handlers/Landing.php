<?php

declare(strict_types=1);
namespace TurtleShortener;

use TurtleShortener\Models\GeoData;

require_once(__DIR__ . '/../TurtleShortener/Handlers/BaseHandler.php');

class Landing extends \TurtleShortener\Handlers\BaseHandler {

    public function __construct(?string $page = null, ?string $userLanguage = null) {
        parent::__construct($page, $userLanguage);
        $this->include_page();
    }

    public function include_page(): void {
        if (
            ($this->page === 'stats')
        ) {
            $from = strtotime($_GET['from'] ?? date('Y-m-d', strtotime('-6 months')));
            $to = strtotime($_GET['to'] ?? date('Y-m-d'));
            $geoDataRangeSummary = GeoData::fetchDateRangeSummary($from, $to);
            echo '<script>const geoDataRangeSummary = ' . ($geoDataRangeSummary ?? 'null') . '</script>';
        }
        parent::include_page();
    }

}

new Landing();
