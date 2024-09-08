<?php
namespace TurtleShortener\Admin;

use DirectoryIterator;
use ReflectionClass;
use TurtleShortener\Languages\ILanguage;
use TurtleShortener\Misc\AccessLevel;

require_once(__DIR__ . '/../bootstrap.php');

if (!($GLOBALS['utils']->isTokenValid(AccessLevel::admin, null) ?? false)) {
    exit;
}

require_once __DIR__."/../Languages/ILanguage.php";
require_once __DIR__."/../Languages/Language.php";

$languages = ['en', 'cz'];
//$utils = new Utils();
//$utils::loadLanguage();
$layoutFiles = ['Index', "Admin"];
$destinationFolder = __DIR__ . "/../../generated/";

echo "Generating files for languages..<br>";
foreach (new DirectoryIterator(__DIR__."/../Languages") as $fileInfo) {
    if ($fileInfo->isDot() || $fileInfo->getExtension() !== 'php') {
        continue;
    }
    $filePath = __DIR__ . "/../Languages/".$fileInfo->getFilename();
    require_once $filePath;

    $languageClassName = 'TurtleShortener\\Languages\\' . $fileInfo->getBasename('.php');
    //$userLangCode = $lang;
    if (!class_exists($languageClassName) || interface_exists($languageClassName)) {
        continue;
    }
    $reflection = new ReflectionClass($languageClassName);
    if ($reflection->isAbstract()) {
        continue;
    }
    echo $fileInfo->getFilename().": ";
    $isMobile = false;
    $language = new $languageClassName();
    if (!($language instanceof ILanguage)) {
        throw new \RuntimeException("The class $languageClassName must implement the Language interface.");
    }
    if (!isset($language->code, $language->name)){
        throw new \RuntimeException("The class $languageClassName must have a code and name property.");
    }
    $generatedDir = $destinationFolder . $language->code;
    if (!is_dir($generatedDir) &&
        !mkdir($generatedDir, 0777, true) &&
        !is_dir($generatedDir)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $generatedDir));
    }
    /*
    * Load language class
    */
    //$languageClassName = $utils::getLanguage($lang);
    //$languageClassPath = __DIR__ . "/../Languages/$languageClassName.php";
    //require_once($languageClassPath);
    //$languageClass = "\\TurtleShortener\\Languages\\$languageClassName";
    //$translations = new $languageClass();

    /*
    * Generate layout files
    */
    foreach ($layoutFiles as $file) {
        $fileNameLWR = strtolower($file);
        echo $fileNameLWR.", ";
        $source = __DIR__ . "/../Templates/$file.php";
        $destination = $generatedDir . '/' . $fileNameLWR.".php";

        $content = file_get_contents($source);

        $content = preg_replace_callback('/include\([\'"]([^\'"]+)[\'"]\);/i', static function ($matches) {
            $layoutName = ucfirst(strtolower($matches[1]));
            $layoutPath = __DIR__ . "/../Templates/$layoutName.php";
            if (file_exists($layoutPath)) {
                return file_get_contents($layoutPath);
            }
            throw new \RuntimeException("Layout file '{$layoutPath}' not found.");
        }, $content);

        $content = preg_replace_callback('/translate\(["\']([^"\']+)["\']\)/', static function ($matches) use ($language) {
            return $language->get($matches[1]) ?? $matches[0];
        }, $content);

        $content = str_replace('%language_code%', $language->code, $content);

        $content .= "</html>";
        file_put_contents($destination, $content);
    }
    echo "<br>";
}
$landingContent = file_get_contents(__DIR__ . '/../Handlers/Landing.php');
$landingContent = str_replace(
    '$languages = [];',
    '$languages = ' . json_encode($languages, JSON_THROW_ON_ERROR) . ';',
    $landingContent
);
file_put_contents($destinationFolder."/index.php", $landingContent);
echo "Language-specific files generated successfully.";
