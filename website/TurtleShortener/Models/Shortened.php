<?php
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
        public ?int $expiry=null,
        public ?int $created=null,
        public ?bool $includeInSearch=true,
        public string $usr_timezone = 'Europe/Warsaw'
    ) {
        $this->created = $created ?? time();
        $this->includeInSearch = $includeInSearch === null ?? true;
        try {
            $this->timezone = new DateTimeZone($usr_timezone);
        } catch (Exception $e) {}
    }
    public static function fetch_by_url(?string $shortcode, string $url): ?Shortened {
        $pdo = DbUtil::getPdo();
        $query = "SELECT ulid, shortcode, url, expiry, created, searchable FROM urls WHERE url = :url OR shortcode = :shortcode LIMIT 1";
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
            $data['searchable'] ?? null
        );
    }
    public static function fetch(string $shortcode, bool $full): ?Shortened {
        $pdo = DbUtil::getPdo();
        $query = $full ?
            "SELECT ulid, url, expiry, created, searchable FROM urls WHERE shortcode = :shortcode LIMIT 1" :
            "SELECT ulid, url FROM urls WHERE shortcode = :shortcode LIMIT 1";
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
            $data['searchable'] ?? null
        );
    }

    public function getExpiryFormatted(string $dateformat='d-m-Y H:i:s'): ?string {
        $result = "never";
        if ($this->expiry !== null) {
            try {
                $datetime = new DateTime('@' . $this->expiry);
                $datetime->setTimezone($this->timezone);
                $result = $datetime->format($dateformat);
            } catch (Exception $e) {
                return null;
            }
        }
        return $result;
    }

    public function getCreationDate(string $dateformat='d-m-Y H:i:s'): ?string {
        try {
            $datetime = new DateTime('@' . $this->created);
            $datetime->setTimezone($this->timezone);
            return $datetime->format($dateformat);
        } catch (Exception $e) {
            return null;
        }
    }
}
