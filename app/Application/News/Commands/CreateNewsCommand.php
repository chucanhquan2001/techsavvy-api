<?php

namespace App\Application\News\Commands;

class CreateNewsCommand
{
    public function __construct(
        public string $title,
        public string $content
    ) {}
}