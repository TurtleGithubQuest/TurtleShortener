<?php
namespace TurtleShortener\Admin;

$languages = ['en', 'cz'];
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

        // Replace translate('key') with the corresponding value from $translations
        $content = preg_replace_callback('/translate\(\'([^\']+)\'\)/', function ($matches) use ($translations, $lang) {
            $key = $matches[1];
            return $translations[$lang][$key] ?? $matches[0];
        }, $content);

        // Write the modified content to the destination file
        file_put_contents($destination, $content);
    }
}

echo "Language-specific files generated successfully.";
?>