<?php
namespace TurtleShortener\Languages;

abstract class Language implements ILanguage {
    public readonly string $code;
    public readonly string $name;
    public function setCode(string $text): void {
        $this->code = $text;
    }
    public function setName(string $text): void {
        $this->name = $text;
    }
}