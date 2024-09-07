<?php
namespace TurtleShortener\Admin;
use TurtleShortener\Misc\Utils;
use TurtleShortener\Languages\Language;

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

    foreach ($layoutFiles as $file) {
        $source = __DIR__ . "/../Layout/$file";
        $destination = $generatedDir . '/' . $file;

        // Read the content of the source file
        $content = file_get_contents($source);

        // Load the language file using Utils
        $languageClassName = $utils::getLanguage($lang);
        $languageClassPath = __DIR__ . "/../Languages/{$languageClassName}.php";
        require_once($languageClassPath);
        $languageClass = "\\TurtleShortener\\Languages\\{$languageClassName}";
        $translations = new $languageClass(); // $translations is an instance of Language

        // Replace translate('key') with the corresponding value from $translations
        $content = preg_replace_callback('/translate\(\'([^\']+)\'\)/', function ($matches) use ($translations) {
            $key = $matches[1];
            if ($translations instanceof Language) {
                return $translations->get($key) ?? $matches[0];
            } else {
                throw new \RuntimeException("The class {$languageClass} must implement the Language interface.");
            }
        }, $content);

        // Write the modified content to the destination file
        file_put_contents($destination, $content);
    }
}

echo "Language-specific files generated successfully.";
?>