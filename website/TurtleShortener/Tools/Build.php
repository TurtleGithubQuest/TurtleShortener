<?php
namespace TurtleShortener\Tools;


$languages = ['en', 'cz'];
$template = file_get_contents('index_template.php');

foreach ($languages as $lang) {
    $userLangCode = $lang;
    $isMobile = false;

    $content = str_replace(
        ['{{lang}}', '{{userLangCode}}', '{{isMobile}}'],
        [$lang, $userLangCode, $isMobile ? 'true' : 'false'],
        $template
    );

    file_put_contents("index_$lang.php", $content);
}

echo "Language-specific files generated successfully.";
?>