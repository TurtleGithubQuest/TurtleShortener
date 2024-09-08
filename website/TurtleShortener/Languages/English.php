<?php
namespace TurtleShortener\Languages;

use RuntimeException;
use TurtleShortener\Misc\LogUtil;

class English extends Language {
    final protected const TRANSLATIONS = [
        "url-address" => "Url address",
        "url-address.placeholder" => "very long url..",
        "expiration" => "Expiration",
        "shorten" => "Shorten",
        "shortened-found" => " shortened url(s) found:",
        "created-at" => "Created at",
        "shortened-url" => "Shortened URL",
        "preview-url" => "Preview URL",
        "alias.placeholder" => "(optional) shortcode",
        "search-url" => "Search URL",
        "found-nothing" => "Nothing found",
        "include_in_search" => "Include in search",
        1 => "true",
        0 => "false"
    ];

    public function __construct()
    {
        $this->setCode("en");
        $this->setName("English");
    }

    /**
     * @throws RuntimeException
     */
    public function get(string $key): string
    {
        return self::TRANSLATIONS[$key] ?? $key;
    }
}
