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
        "created_at" => "Created at",
        "shortened-url" => "Shortened URL",
        "preview-url" => "Preview URL",
        "alias.placeholder" => "(optional) shortcode",
        "search-url" => "Search URL",
        "found-nothing" => "Nothing found",
        "include_in_search" => "Include in search",
        "url_preview" => "Shortened url preview",
        "target" => "Target",
        "searchable" => "Searchable",
        "statistics" => "Statistics",
        "click_to_copy" => "Click to copy",
        1 => "yes",
        0 => "no"
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
