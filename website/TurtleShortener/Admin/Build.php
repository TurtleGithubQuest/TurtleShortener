<?php
namespace TurtleShortener\Admin;

use TurtleShortener\Misc\Utils;
use TurtleShortener\Languages\Language;

require_once(__DIR__ . '/../bootstrap.php');

$languages = ['en', 'cz'];
$utils = new Utils();
$utils::loadLanguage();
$layoutFiles = ['Index', "Admin"];
$destinationFolder = __DIR__ . "/../../generated/";

echo "Generating files for languages..<br>";
foreach ($languages as $lang) {
    echo $lang.": ";
    $userLangCode = $lang;
    $isMobile = false;

    $generatedDir = $destinationFolder . $lang;
    if (!is_dir($generatedDir) &&
        !mkdir($generatedDir, 0777, true) &&
        !is_dir($generatedDir)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $generatedDir));
    }
    /*
    * Load language class
    */
    $languageClassName = $utils::getLanguage($lang);
    $languageClassPath = __DIR__ . "/../Languages/$languageClassName.php";
    require_once($languageClassPath);
    $languageClass = "\\TurtleShortener\\Languages\\$languageClassName";
    $translations = new $languageClass();
    if (!($translations instanceof Language)) {
        throw new \RuntimeException("The class $languageClass must implement the Language interface.");
    }
    /*
    * Generate layout files
    */
    foreach ($layoutFiles as $file) {
        $fileNameLWR = strtolower($file);
        echo $fileNameLWR.", ";
        $source = __DIR__ . "/../Layout/$file.php";
        $destination = $generatedDir . '/' . $fileNameLWR.".php";

        $content = file_get_contents($source);

        $content = preg_replace_callback('/include\([\'"]([^\'"]+)[\'"]\);/i', static function ($matches) {
            $layoutName = ucfirst(strtolower($matches[1]));
            $layoutPath = __DIR__ . "/../Layout/$layoutName.php";
            if (file_exists($layoutPath)) {
                return file_get_contents($layoutPath);
            }
            throw new \RuntimeException("Layout file '{$layoutPath}' not found.");
        }, $content);

        $content = preg_replace_callback('/translate\(["\']([^"\']+)["\']\)/', static function ($matches) use ($translations) {
            return $translations->get($matches[1]) ?? $matches[0];
        }, $content);

        $content = str_replace('%language_code%', $lang, $content);

        $content .= "</html>";
        file_put_contents($destination, $content);
    }
    echo "<br>";
}
$landingContent = file_get_contents(__DIR__ . '/../Page/Landing.php');
$landingContent = str_replace(
    array('$languages = [];', '%language_code%'),
    array('$languages = ' . json_encode($languages, JSON_THROW_ON_ERROR) . ';', $lang),
    $landingContent
);
file_put_contents($destinationFolder."/index.php", $landingContent);
echo "Language-specific files generated successfully.";
