<?php

namespace TurtleShortener\Languages;


interface ILanguage
{
    public function get(string $key): string;
}