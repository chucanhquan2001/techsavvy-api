<?php

namespace App\Application\News\Commands;

class UpdateNewsCommand
{
    public function __construct(
        public int $id,
        public string $title,
        public string $content
    ) {}
}