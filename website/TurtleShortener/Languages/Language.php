<?php
namespace TurtleShortener\Languages;

interface Language {
    public function get(string $key): string;
}