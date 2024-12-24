<?php
declare(strict_types=1);
namespace TurtleShortener\Handlers;

require_once(__DIR__ . '/../bootstrap.php');

class BaseHandler {
    public bool $isMobile = false;

    public function __construct(
        public string|null $page = null,
        public string|null $userLanguage = null,
    ) {
        if (isset($_GET['sid'])) {
            session_id($_GET['sid']);
        }

        session_start();

        $this->isMobile = (bool)($_GET['m'] ?? false);
        if (empty($this->userLanguage)) {
            $languages = [];
            $this->userLanguage = $_GET['lang'] ?? 'en';

            /*if (!\in_array($this->userLanguage, $languages, false)) {
                header('Location: /error.php?error='.urlencode("Language '$this->userLanguage' is not supported."));
            }*/
        }

        if (empty($page)) {
            $this->page = $_GET['page'] ?? 'index';
        }

    }

    public function include_page(): void {
        global $isMobile;
        global $page;
        global $userLanguage;
        include_once(__DIR__.'/../../generated/'.$this->userLanguage."/$this->page.php");
    }

}

