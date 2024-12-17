<?php
namespace TurtleShortener\Misc;

use Exception;
use TurtleShortener\Models\GeoData;
class Utils {
    private const DEFAULT_LANGUAGE = 'English';
    public static function getLanguage($userLang): string {
        $languages = [
            'cz' => 'Czech',
            'en' => 'English',
            'sk' => 'Czech'
        ];
        $language = $languages[$userLang] ?? self::DEFAULT_LANGUAGE;
        if (file_exists(__DIR__ . "/../Languages/{$language}.php")) {
            return $language;
        }

        return self::DEFAULT_LANGUAGE;
    }

    /**
     * @throws Exception
     */
    public static function loadLanguage(): void {
        $user_language = $_GET['lang'] ?? $_POST['lang'] ?? strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? self::DEFAULT_LANGUAGE, 0, 2));
        $GLOBALS['userLangCode'] = $user_language;
        $user_language = self::getLanguage($user_language);
        $GLOBALS['userLang'] = $user_language;

        $languageClassPath = __DIR__ . "/../Languages/{$user_language}.php";
        if (!file_exists($languageClassPath)) {
            throw new \RuntimeException("Language file not found: {$languageClassPath}");
        }
        require_once(__DIR__. '/../Languages/Language.php');
        include_once($languageClassPath);
        $languageClass = "\\TurtleShortener\\Languages\\{$user_language}";
        if (!class_exists($languageClass)) {
            throw new \RuntimeException("Language class not found: {$languageClass}");
        }

        $GLOBALS['lang'] = new $languageClass;
    }

    public function validateTranslation($baseTranslation, $otherTranslation): array {
        $missingEntries = array_diff_key($baseTranslation, $otherTranslation);

        if (empty($missingEntries)) {
        return array();  // All entries are present in the other translation
        }

        return $missingEntries;  // Return keys that are missing in the other translation
    }

    public function getProtocol(): string {
        return ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) ? 'https' : 'http';
    }

    public function getQueryParams(): array{
        parse_str($_SERVER['QUERY_STRING'] ?? '', $query_params);
        return $query_params;
    }

    public function buildQuery(string $key, $value, ?array $query_params = null, ?string $url = null): string {
        $query_params = $query_params ?? $this->getQueryParams();
        $ignoreList = ['page', 'm', 'preview', 's'];
        foreach($ignoreList as $ignoreKey) {
            unset($query_params[$ignoreKey]);
        }
        $query_params[$key] = $value;
        if (!empty($url)) {
            $query_params['url'] = $url;
        }
        $new_query_string = http_build_query($query_params);
        return '?' . $new_query_string;
    }

    public function isTokenValid(AccessLevel $level, ?string $token): bool {
        $token =
            $token ??
            filter_input(INPUT_GET, 'token') ??
            filter_input(INPUT_POST, 'token');

        $admin_tokens = $GLOBALS['settings'][$level->name. '_tokens'] ?? [];
        if (\in_array($token, $admin_tokens, true)) {
            return true;
        }
        echo $level->name. ' access token is not valid.';
        return false;
    }

    public function getUserIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    function getUserOS() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $osArray = [
            'Windows', 'Mac', 'Linux', 'iPhone', 'Android'
        ];
        foreach ($osArray as $os) {
            if (str_contains($userAgent, $os)) {
                return $os;
            }
        }
        return 'Unknown';
    }

}
