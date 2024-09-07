<?php
namespace TurtleShortener\Tools;

namespace TurtleShortener\Tools;

$languages = ['en', 'cz'];
$layoutFiles = ['Index.php', 'Header.php'];

foreach ($languages as $lang) {
    $userLangCode = $lang;
    $isMobile = false;

    $generatedDir = __DIR__ . "/../generated/$lang";
    if (!is_dir($generatedDir)) {
        mkdir($generatedDir, 0777, true);
    }

    foreach ($layoutFiles as $file) {
        $source = __DIR__ . "/../Layout/$file";
        $destination = $generatedDir . '/' . $file;
        copy($source, $destination);
    }
}

echo "Language-specific files generated successfully.";
?>