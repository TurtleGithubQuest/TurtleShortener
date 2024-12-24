<?php
declare(strict_types=1);

namespace TurtleShortener\Languages;

use RuntimeException;

class English extends Language {
    final protected const TRANSLATIONS = [
        'url-address' => 'Url address',
        'url-address.placeholder' => 'very long url..',
        'expiration' => 'Expiration',
        'shorten' => 'Shorten',
        'shortened-found' => ' shortened url(s) found:',
        'created_at' => 'Created at',
        'shortened-url' => 'Shortened URL',
        'preview-url' => 'Preview URL',
        'alias.placeholder' => '(optional) shortcode',
        'search-url' => 'Search URL',
        'found-nothing' => 'Nothing found',
        'include_in_search' => 'Include in search',
        'url_preview' => 'Shortened url preview',
        'target' => 'Target',
        'searchable' => 'Searchable',
        'statistics' => 'Statistics',
        'click_to_copy' => 'Click to copy',
        'none yet' => 'none yet',
        'countries' => 'Countries',
        'os' => 'Operating systems',
        'daily_visits' => 'Daily visits',
        'never' => 'Never',
        'info' => 'Info',
        'stats' => 'Stats',
        'sources' => 'Sources',
        'cities' => 'Cities',
        'turtle_images' => 'Turtle images',
        'placeholder_access_token' => 'Access token',
        'drag_and_drop_here' => 'Drag and drop here',
        'supported_extensions' => 'Supported extensions',
        1 => 'Yes',
        0 => 'No'
    ];

    public function __construct() {
        $this->setCode('en');
        $this->setName('English');
    }

    /**
     * @throws RuntimeException
     */
    public function get(string $key): string {
        return self::TRANSLATIONS[$key] ?? $key;
    }

}
