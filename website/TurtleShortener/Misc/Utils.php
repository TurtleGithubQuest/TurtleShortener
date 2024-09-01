<?php
namespace TurtleShortener\Misc;

use Exception;

class Utils {
    private const DEFAULT_LANGUAGE = "English";
    public static function getLanguage($userLang): string {
        $languages = [
            "cz" => "Czech",
            "en" => "English",
            "sk" => "Czech"
        ];
        $language = $languages[$userLang] ?? self::DEFAULT_LANGUAGE;
        if (file_exists(__DIR__ . "/../Languages/{$language}.php")) {
            return $language;
        } else {
            return self::DEFAULT_LANGUAGE;
        }
    }

    /**
     * @throws Exception
     */
    public static function loadLanguage(): void {
        $user_language = $_GET["lang"] ?? $_POST["lang"] ?? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $GLOBALS['userLangCode'] = $user_language;
        $user_language = self::getLanguage($user_language);
        $GLOBALS['userLang'] = $user_language;

        $languageClassPath = __DIR__ . "/../Languages/{$user_language}.php";
        if (!file_exists($languageClassPath)) {
            throw new Exception("Language file not found: {$languageClassPath}");
        }
        require_once(__DIR__. "/../Languages/Language.php");
        include_once($languageClassPath);
        $languageClass = "\\TurtleShortener\\Languages\\{$user_language}";
        if (!class_exists($languageClass)) {
            throw new Exception("Language class not found: {$languageClass}");
        }

        $GLOBALS['lang'] = new $languageClass;
    }
    public function validateTranslation($baseTranslation, $otherTranslation): array {
      $missingEntries = array_diff_key($baseTranslation, $otherTranslation);

      if (empty($missingEntries)) {
        return array();  // All entries are present in the other translation
      }
      else {
        return $missingEntries;  // Return keys that are missing in the other translation
      }
    }
    public function getProtocol(): string {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
    }
    public function getQueryParams(): array{
        parse_str($_SERVER['QUERY_STRING'] ?? "", $query_params);
        return $query_params;
    }
    public function buildQuery(string $key, $value, ?array $query_params = null, string $url = ''): string {
        $query_params = $query_params ?? self::getQueryParams();
        $query_params[$key] = $value;
        if (!empty($currentUrl))
            $query_params['url'] = $currentUrl;
        $new_query_string = http_build_query($query_params);
        return '?' . $new_query_string;
    }
}
