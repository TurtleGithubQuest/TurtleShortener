<?php
declare(strict_types=1);

namespace TurtleShortener\Languages;

class Czech extends Language {
    final protected const TRANSLATIONS = [
        'url-address' => 'Url adresa',
        'url-address.placeholder' => 'velmi dlouhý odkaz..',
        'expiration' => 'Expirace',
        'shorten' => 'Zkrátit',
        'shortened-found' => 'x zkrácený odkaz:',
        'created_at' => 'Vytvořeno',
        'shortened-url' => 'Zkrácený odkaz',
        'preview-url' => 'Informace o odkazu',
        'hour' => 'hodina',
        'hours' => 'hodin',
        'days' => 'dní',
        'month' => 'měsíc',
        'alias.placeholder' => 'kód (nepovinné)',
        'search-url' => 'Hledej odkaz',
        'found-nothing' => 'Nic nenalezeno',
        'include_in_search' => 'Zobrazit v hledání',
        'url_preview' => 'Náhled zkráceného odkazu',
        'target' => 'Odkaz',
        'searchable' => 'Vyhledatelné',
        'statistics' => 'Statistiky',
        'click_to_copy' => 'Zkopírovat kliknutím',
        'none yet' => 'Zatím žádné',
        'countries' => 'Země',
        'os' => 'Operační systémy',
        'daily_visits' => 'Denní návštěvy',
        'never' => 'Nikdy',
        'info' => 'Informace',
        'stats' => 'Statistiky',
        'sources' => 'Zdroj',
        'cities' => 'Města',
        'turtle_images' => 'Želvotky',
        'placeholder_access_token' => 'Api klíč',
        'drag_and_drop_here' => 'Tažením nahraješ',
        'supported_extensions' => 'Podporované formáty',
        1 => 'Ano',
        0 => 'Ne',
    ];

    public function __construct() {
        $this->setCode('cz');
        $this->setName('Czech');
    }

    public function get(string $key): string {
        if (!\array_key_exists($key, self::TRANSLATIONS)) {
            $GLOBALS['log']->debug('Czech translation not found for key: $key');
            return $key;
        }
        return self::TRANSLATIONS[$key];
    }

}
