<?php
namespace Website\php\fn;

class Utils {
    public function getLanguage($userLang) {
        if (file_exists(__DIR__ . "/../lang/{$userLang}.php")) {
            return $userLang;
        } else {
            return $userLang == "sk" ? "cz" : "en";
        }
    }
    public function loadLanguage(): void {
        global$user_language;
        $user_language = $_GET["lang"] ?? $_POST["lang"] ?? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $user_language = self::getLanguage($user_language);
        include_once(__DIR__ . "/../lang/".$user_language.".php");
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