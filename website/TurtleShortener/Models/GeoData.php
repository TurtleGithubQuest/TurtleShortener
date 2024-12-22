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

    /**
     * @throws \JsonException
     */
    public static function capture(?string $ip=null): ?GeoData {
        require_once(__DIR__ . '/../../composer/vendor/autoload.php');
        if (empty($ip)) {
            $ip = $GLOBALS['utils']->getUserIP();
        }
        $json = @file_get_contents('http://www.geoplugin.net/json.gp?ip=' . $ip);
        if ($json === false) {
            return null; // Handle error appropriately
        }

        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        if (\is_array($data)) {
            $ulid = Ulid::generate();
            $code = $data['geoplugin_countryCode'];
            $continent = $data['geoplugin_continentCode'];
            $country = $data['geoplugin_countryCode'];
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
        $len = \strlen($url_ulid);
        if ($len !== 26) {
            $GLOBALS['log']->error("Invalid URL ULID: $url_ulid. Length: $len/26");
            return false;
        }
        $pdo = DbUtil::getPdo();
        $ipAddress = $this->ipAddress;
        $currentTime = time();

        $query = 'SELECT click_time FROM stats WHERE ip_address = :ip_address ORDER BY click_time DESC LIMIT 1';
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':ip_address', $ipAddress);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $lastInsertTime = $data['click_time'] ?? 0;

        if (($currentTime - $lastInsertTime) < 59) {
            return false;
        }
        $sql = 'INSERT INTO stats (ulid, url_ulid, click_time, referrer, country, city, user_agent, ip_address, operating_system, click_source)
                VALUES (:ulid, :url_ulid, :click_time, :referrer, :country, :city, :user_agent, :ip_address, :operating_system, :click_source)';

        $stmt = $pdo->prepare($sql);
        $clickTime = time();
        $referrer = $_SERVER['HTTP_REFERER'] ?? 'Direct';

        $stmt->bindParam(':ulid', $this->ulid);
        $stmt->bindParam(':url_ulid', $url_ulid);
        $stmt->bindParam(':click_time', $clickTime);
        $stmt->bindParam(':referrer', $referrer);
        $stmt->bindValue(':country', $this->country ? strtolower($this->country) : null);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':user_agent', $this->userAgent);
        $stmt->bindParam(':ip_address', $this->ipAddress);
        $stmt->bindValue(':operating_system', $this->operatingSystem ? strtolower($this->operatingSystem) : null);
        $stmt->bindParam(':click_source', $this->clickSource);

        return $stmt->execute();
    }

    public static function fetchSummary(?string $url_ulid): ?string {
        if (!$url_ulid) {
            return null;
        }
        $pdo = DbUtil::getPdo();

        $sql = '
            SELECT 
                COUNT(*) as total_clicks,
                AVG(click_time) as avg_click_time
            FROM stats 
            WHERE url_ulid = :url_ulid
        ';

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':url_ulid', $url_ulid);
        $stmt->execute();
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($summary && $summary['total_clicks'] > 0) {
            $totalClicks = $summary['total_clicks'];

            $detailsSql = '
                SELECT 
                    country,
                    city,
                    operating_system,
                    user_agent,
                    COUNT(*) as count
                FROM stats 
                WHERE url_ulid = :url_ulid
                GROUP BY country, city, operating_system, user_agent
            ';

            $detailsStmt = $pdo->prepare($detailsSql);
            $detailsStmt->bindParam(':url_ulid', $url_ulid);
            $detailsStmt->execute();
            $details = $detailsStmt->fetchAll(PDO::FETCH_ASSOC);
            $countries = [];
            $cities = [];
            $operatingSystems = [];
            //$userAgents = [];

            foreach ($details as $detail) {
                $percentage = ($detail['count'] / $totalClicks) * 100;

                if ($detail['country']) {
                    $countries[$detail['country']] = [
                        'name' => $detail['country'],
                        'count' => ($countries[$detail['country']]['count'] ?? 0) + $detail['count'],
                        'percentage' => ($countries[$detail['country']]['percentage'] ?? 0) + $percentage
                    ];
                }
                if ($detail['city']) {
                    $cities[$detail['city']] = [
                        'name' => $detail['city'],
                        'count' => ($cities[$detail['city']]['count'] ?? 0) + $detail['count'],
                        'percentage' => ($cities[$detail['city']]['percentage'] ?? 0) + $percentage
                    ];
                }
                if ($detail['operating_system']) {
                    $operatingSystems[$detail['operating_system']] = [
                        'name' => $detail['operating_system'],
                        'count' => ($operatingSystems[$detail['operating_system']]['count'] ?? 0) + $detail['count'],
                        'percentage' => ($operatingSystems[$detail['operating_system']]['percentage'] ?? 0) + $percentage
                    ];
                }
                /*if ($detail['user_agent']) {
                    $userAgents[$detail['user_agent']] = [
                        'name' => $detail['user_agent'],
                        'count' => ($userAgents[$detail['user_agent']]['count'] ?? 0) + $detail['count'],
                        'percentage' => ($userAgents[$detail['user_agent']]['percentage'] ?? 0) + $percentage
                    ];
                }*/
            }

            $summary['countries'] = array_values($countries);
            $summary['cities'] = array_values($cities);
            $summary['operating_systems'] = array_values($operatingSystems);
            //$summary['user_agents'] = array_values($userAgents);

            $clicksByDaySql = '
                SELECT 
                    FLOOR(click_time / 86400) * 86400 as unix,
                    COUNT(*) as count
                FROM stats 
                WHERE click_time IS NOT NULL AND url_ulid = :url_ulid
                GROUP BY unix
            ';

            $clicksByDayStmt = $pdo->prepare($clicksByDaySql);
            $clicksByDayStmt->bindParam(':url_ulid', $url_ulid);
            $clicksByDayStmt->execute();
            $clicksByDay = $clicksByDayStmt->fetchAll(PDO::FETCH_ASSOC);

            $summary['clicks_by_day'] = $clicksByDay;

            try {
                $summary = json_encode($summary, JSON_THROW_ON_ERROR);
            } catch(\Exception $ex) {
                $GLOBALS['log']->error('Error encoding GeoData summary: ' .$ex->getMessage());
                $summary = null;
            }
            return $summary;
        }
        return null;
    }

    public static function fetchDateRangeSummary(int $from, int $to): ?string {
        $pdo = DbUtil::getPdo();

        // Fetch country statistics
        $countrySql = 'SELECT unix, country, visit_count, unique_visitors 
                      FROM stats_country_summary 
                      WHERE unix BETWEEN :from AND :to';

        $countryStmt = $pdo->prepare($countrySql);
        $countryStmt->bindParam(':from', $from);
        $countryStmt->bindParam(':to', $to);
        $countryStmt->execute();
        $countryStats = $countryStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch OS statistics
        $osSql = 'SELECT unix, operating_system, visit_count, unique_visitors 
                 FROM stats_os_summary 
                 WHERE unix BETWEEN :from AND :to';

        $osStmt = $pdo->prepare($osSql);
        $osStmt->bindParam(':from', $from);
        $osStmt->bindParam(':to', $to);
        $osStmt->execute();
        $osStats = $osStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch city statistics
        $citySql = 'SELECT unix, city, visit_count, unique_visitors 
                   FROM stats_city_summary 
                   WHERE unix BETWEEN :from AND :to';

        $cityStmt = $pdo->prepare($citySql);
        $cityStmt->bindParam(':from', $from);
        $cityStmt->bindParam(':to', $to);
        $cityStmt->execute();
        $cityStats = $cityStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch source statistics
        $sourceSql = 'SELECT unix, click_source, visit_count, unique_visitors 
                     FROM stats_source_summary 
                     WHERE unix BETWEEN :from AND :to';

        $sourceStmt = $pdo->prepare($sourceSql);
        $sourceStmt->bindParam(':from', $from);
        $sourceStmt->bindParam(':to', $to);
        $sourceStmt->execute();
        $sourceStats = $sourceStmt->fetchAll(PDO::FETCH_ASSOC);

        $summary = [
            'date_range' => [
                'from' => $from,
                'to' => $to
            ],
            'country_stats' => $countryStats,
            'os_stats' => $osStats,
            'city_stats' => $cityStats,
            'source_stats' => $sourceStats
        ];

        try {
            return json_encode($summary, JSON_THROW_ON_ERROR);
        } catch(\Exception $ex) {
            $GLOBALS['log']->error('Error encoding date range summary: ' . $ex->getMessage());
            return null;
        }
    }

}