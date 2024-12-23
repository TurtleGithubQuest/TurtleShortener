<?php
declare(strict_types=1);

namespace TurtleShortener\Models;

use DateTime;
use DateTimeZone;
use Exception;
use PDO;
use TurtleShortener\Database\DbUtil;

class Shortened {
    private DateTimeZone $timezone;

    public function __construct(
        public string $ulid,
        public string $shortcode,
        public ?string $shortenedUrl,
        public string $url,
        public ?int $expiry = null,
        public ?int $created = null,
        public bool $includeInSearch = true,
        public string $usr_timezone = 'Europe/Warsaw'
    ) {
        $this->created = $created ?? time();
        try {
            $this->timezone = new DateTimeZone($usr_timezone);
        } catch (Exception) {}
    }

    public static function new(
        string $ulid,
        string $shortcode,
        ?string $shortenedUrl,
        string $url,
        ?int $expiry = null,
        ?int $created = null,
        bool $includeInSearch = true,
        string $usr_timezone = 'Europe/Warsaw'
    ): Shortened {
        $shortened = new Shortened(
            $ulid,
            $shortcode,
             $shortenedUrl,
            $url,
            $expiry,
            $created,
            $includeInSearch,
            $usr_timezone
        );
        $pdo = DbUtil::getPdo();
        $stmt = $pdo->prepare('INSERT INTO urls (ulid, shortcode, url, expiry, searchable) VALUES (?, ?, ?, ?, ?)');

        $stmt->execute([
            $shortened->ulid,
            $shortened->shortcode,
            $shortened->url,
            $shortened->expiry,
            $shortened->includeInSearch
        ]);

        return $shortened;
    }

    public static function fetchByUrl(?string $shortcode, string $url): ?Shortened {
        $pdo = DbUtil::getPdo();
        $query = 'SELECT ulid, shortcode, url, expiry, created, searchable FROM urls WHERE url = :url OR shortcode = :shortcode LIMIT 1';
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':shortcode', $shortcode);
        $stmt->bindParam(':url', $url);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            return null;
        }
        $shortcode = $data['shortcode'];

        return new self(
            $data['ulid'],
            $shortcode,
            $_SERVER['HTTP_HOST']."/$shortcode",
            $data['url'] ?? null,
            $data['expiry'] ?? null,
            $data['created'] ?? null,
            (bool) $data['searchable']
        );
    }

    public static function fetch(string $shortcode, bool $full): ?Shortened {
        $pdo = DbUtil::getPdo();
        $query = 'SELECT ulid, url' . ($full ? ', expiry, created, searchable' : '') . ' FROM urls WHERE shortcode = :shortcode LIMIT 1';
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':shortcode', $shortcode);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            return null;
        }

        return new self(
            $data['ulid'],
            $shortcode,
            $_SERVER['HTTP_HOST']."/$shortcode",
            $data['url'] ?? null,
            $data['expiry'] ?? null,
            $data['created'] ?? null,
            (bool) ($data['searchable'] ?? false)
        );
    }

    public function getExpiryFormatted(string $dateformat='d-m-Y H:i:s'): ?string {
        if (!empty($this->expiry)) {
            try {
                $datetime = new DateTime('@' . $this->expiry);
                $datetime->setTimezone($this->timezone);
                return $datetime->format($dateformat);
            } catch (Exception) {}
        }
        return null;
    }

    public function getCreationDate(string $dateformat='d-m-Y H:i:s'): ?string {
        try {
            $datetime = new DateTime('@' . $this->created);
            $datetime->setTimezone($this->timezone);
            return $datetime->format($dateformat);
        } catch (Exception) {
            return null;
        }
    }

}
