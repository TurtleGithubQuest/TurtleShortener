<?php
namespace TurtleShortener\Languages;
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
    public function get(string $key): string
    {
        return self::TRANSLATIONS[$key] ?? $key;
    }
}
