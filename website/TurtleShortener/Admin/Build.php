<?php
namespace TurtleShortener\Admin;

use TurtleShortener\Misc\Utils;
use TurtleShortener\Languages\Language;

require_once(__DIR__ . '/../bootstrap.php');

$languages = ['en', 'cz'];
$utils = new Utils();
$layoutFiles = ['Index.php', 'Header.php'];

foreach ($languages as $lang) {
    $userLangCode = $lang;
    $isMobile = false;

    $generatedDir = __DIR__ . "/../generated/$lang";
    if (!is_dir($generatedDir) &&
        !mkdir($generatedDir, 0777, true) &&
        !is_dir($generatedDir)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $generatedDir));
    }

    echo "Generating files for language '".$lang."'.<br>";
    foreach ($layoutFiles as $file) {
        $source = __DIR__ . "/../Layout/$file";
        $destination = $generatedDir . '/' . $file;

        $content = file_get_contents($source);

        $languageClassName = $utils::getLanguage($lang);
        $languageClassPath = __DIR__ . "/../Languages/{$languageClassName}.php";
        require_once($languageClassPath);
        $languageClass = "\\TurtleShortener\\Languages\\{$languageClassName}";
        $translations = new $languageClass();

        $content = preg_replace_callback('/translate\(["\']([^"\']+)["\']\)/', static function ($matches) use ($translations) {
            $key = $matches[1];
            if ($translations instanceof Language) {
                return $translations->get($key) ?? $matches[0];
            }
            $className = get_class($translations);
            throw new \RuntimeException("The class $className must implement the Language interface.");
        }, $content);

        $content = preg_replace_callback('/include\(["\']([^"\']+)["\']\);/', static function ($matches) {
            $includePath = __DIR__ . '/../' . $matches[1];
            if (file_exists($includePath)) {
                return file_get_contents($includePath);
            }
            throw new \RuntimeException("Included file '$includePath' not found.");
        }, $content);

        $content = str_replace('%language_code%', $lang, $content);

        file_put_contents($destination, $content);
    }
}

echo "Language-specific files generated successfully.";
