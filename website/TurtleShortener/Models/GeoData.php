<?php
namespace TurtleShortener\Models;
use PDO;
use TurtleShortener\Database\DbUtil;
use Ulid\Ulid;

class GeoData {
    public function __construct(
        public string $ulid,
        private string $ipAddress,
        private ?string $code,
        private ?string $continent,
        private ?string $country,
        private ?string $city,
        private ?string $userAgent,
        private ?string $operatingSystem,
        private ?string $clickSource
    ) {}
    public static function capture(?string $ip): ?GeoData
    {
        require_once(__DIR__ . '/../../composer/vendor/autoload.php');
        if (empty($ip)) {
            $ip = $GLOBALS['utils']->getUserIP();
        }
        $json = @file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip);
        if ($json === false) {
            return null; // Handle error appropriately
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        if (is_array($data)) {
            $ulid = Ulid::generate();
            $code = $data['geoplugin_countryCode'];
            $continent = $data['geoplugin_continentCode'];
            $country = $data['geoplugin_countryName'];
            $city = $data['geoplugin_city'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $ipAddress = $ip;
            $operatingSystem = $GLOBALS['utils']->getUserOS();
            $clickSource = null;

            return new self(
                $ulid,
                $ipAddress,
                $code,
                $continent,
                $country,
                $city,
                $userAgent,
                $operatingSystem,
                $clickSource
            );
        }
        return null;
    }
    public function saveToDatabase(string $url_ulid): bool {
        $pdo = DbUtil::getPdo();
        $ipAddress = $this->ipAddress;
        $currentTime = time();

        $query = "SELECT click_time FROM stats WHERE ip_address = :ip_address ORDER BY click_time DESC LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':ip_address', $ipAddress);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $lastInsertTime = $data['click_time'] ?? 0;

        if (($currentTime - $lastInsertTime) < 59) {
            return false;
        }
        $sql = "INSERT INTO stats (ulid, url_ulid, click_time, referrer, country, city, user_agent, ip_address, operating_system, click_source)
                VALUES (:ulid, :url_ulid, :click_time, :referrer, :country, :city, :user_agent, :ip_address, :operating_system, :click_source)";

        $stmt = $pdo->prepare($sql);
        $clickTime = time();
        $referrer = $_SERVER['HTTP_REFERER'] ?? 'Direct';

        $stmt->bindParam(':ulid', $this->ulid);
        $stmt->bindParam(':url_ulid', $url_ulid);
        $stmt->bindParam(':click_time', $clickTime);
        $stmt->bindParam(':referrer', $referrer);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':user_agent', $this->userAgent);
        $stmt->bindParam(':ip_address', $this->ipAddress);
        $stmt->bindParam(':operating_system', $this->operatingSystem);
        $stmt->bindParam(':click_source', $this->clickSource);

        return $stmt->execute();
    }
}