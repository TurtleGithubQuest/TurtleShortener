<?php
namespace TurtleShortener\Languages;
use RuntimeException;

class English implements Language {
    const TRANSLATIONS = [
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

    /**
     * @throws RuntimeException
     */
    public function get(string $key): string
    {
        $translation = self::TRANSLATIONS[$key];
        if (!$translation) {
            throw new RuntimeException("Translation not found for key: $key");
        }
        return $translation;
    }
}
