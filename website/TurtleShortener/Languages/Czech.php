<?php
namespace TurtleShortener\Languages;

class Czech implements Language {
    const TRANSLATIONS = [
        "url-address" => "Url adresa",
        "url-address.placeholder" => "velmi dlouhý odkaz..",
        "expiration" => "Expirace",
        "shorten" => "Zkrátit",
        "shortened-found" => "x zkrácený odkaz:",
        "created-at" => "Vytvořeno",
        "shortened-url" => "Zkrácený odkaz",
        "preview-url" => "Informace o odkazu",
        "hour" => "hodina",
        "hours" => "hodin",
        "days" => "dní",
        "month" => "měsíc",
        "alias.placeholder" => "kód (nepovinné)",
        "search-url" => "Hledej odkaz",
        "found-nothing" => "Nic nenalezeno",
        "include_in_search" => "Zobrazit v hledání",
        1 => "ano",
        0 => "ne",
    ];
    public function get(string $key): string
    {
        $translation = self::TRANSLATIONS[$key];
        if (isset($translation)) {
            $GLOBALS['log']->debug("Czech translation not found for key: $key");
        }
        return $translation ?? "";
    }
}
