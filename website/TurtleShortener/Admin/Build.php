<?php
declare(strict_types=1);

namespace TurtleShortener\Admin;

use DirectoryIterator;
use JsonException;
use ReflectionClass;
use RuntimeException;
use TurtleShortener\Languages\ILanguage;
use TurtleShortener\Misc\AccessLevel;
use function sprintf;

class Build {
    private const LAYOUT_FILES = ['Index', 'Admin', 'Preview'];
    private const LANGUAGES = ['en', 'cz'];

    public function __construct(
        private readonly string $destinationFolder = __DIR__ . '/../../generated/'
    ) {}

    /**
     * @throws JsonException
     */
    public function execute(string $token): string {
        if (!($GLOBALS['utils']->isTokenValid(AccessLevel::admin, $token) ?? false)) {
            throw new RuntimeException('Invalid token');
        }

        require_once __DIR__. '/../Languages/ILanguage.php';
        require_once __DIR__. '/../Languages/Language.php';

        $output = 'Generating files for languages..<br>';

        foreach (new DirectoryIterator(__DIR__. '/../Languages') as $fileInfo) {
            if ($fileInfo->isDot() || $fileInfo->getExtension() !== 'php') {
                continue;
            }
            $filePath = __DIR__ . '/../Languages/' .$fileInfo->getFilename();
            require_once $filePath;

            $languageClassName = 'TurtleShortener\\Languages\\' . $fileInfo->getBasename('.php');
            if (!class_exists($languageClassName) || interface_exists($languageClassName)) {
                continue;
            }
            $reflection = new ReflectionClass($languageClassName);
            if ($reflection->isAbstract()) {
                continue;
            }

            $output .= $fileInfo->getFilename(). ': ';
            $language = new $languageClassName();
            if (!($language instanceof ILanguage)) {
                throw new RuntimeException("The class $languageClassName must implement the Language interface.");
            }
            if (!isset($language->code, $language->name)){
                throw new RuntimeException("The class $languageClassName must have a code and name property.");
            }

            $generatedDir = $this->destinationFolder . $language->code;
            if (!is_dir($generatedDir) &&
                !mkdir($generatedDir, 0777, true) &&
                !is_dir($generatedDir)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $generatedDir));
            }

            foreach (self::LAYOUT_FILES as $file) {
                $fileNameLWR = strtolower($file);
                $output .= $fileNameLWR. ', ';
                $source = __DIR__ . "/../Templates/$file.php";
                $destination = $generatedDir . '/' . $fileNameLWR. '.php';

                $content = file_get_contents($source);
                if ($content === false) {
                    throw new RuntimeException("Failed to read template file: $source");
                }

                $content = preg_replace_callback('/include\([\'"]([^\'"]+)[\'"]\);/i', static function ($matches) {
                    $layoutName = ucfirst(strtolower($matches[1]));
                    $layoutPath = __DIR__ . "/../Templates/$layoutName.php";
                    if (file_exists($layoutPath)) {
                        return file_get_contents($layoutPath);
                    }
                    throw new RuntimeException("Layout file '{$layoutPath}' not found.");
                }, $content);

                $content = preg_replace_callback('/translate\(["\']([^"\']+)["\']\)/', static function ($matches) use ($language) {
                    return $language->get($matches[1]) ?? $matches[0];
                }, $content);

                $content = str_replace('%language_code%', $language->code, $content);
                $content .= '</html>';

                if (!file_put_contents($destination, $content)) {
                    throw new RuntimeException("Failed to write to file: $destination");
                }
            }
            $output .= '<br>';
        }

        foreach(['Landing', 'Preview'] as $handler) {
            $handlerName = strtolower($handler);
            $content = file_get_contents(__DIR__ . "/../Handlers/$handler.php");
            if ($content === false) {
                throw new RuntimeException("Failed to read handler file: $handler.php");
            }
            $content = str_replace(
                '$languages = [];',
                '$languages = ' . json_encode(self::LANGUAGES, JSON_THROW_ON_ERROR) . ';',
                $content
            );
            if (!file_put_contents($this->destinationFolder."/$handlerName.php", $content)) {
                throw new RuntimeException("Failed to write handler file: $handlerName.php");
            }
        }

        $output .= 'Build successful.';
        return $output;
    }

}