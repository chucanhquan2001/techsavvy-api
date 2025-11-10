<?php

namespace App\Application\News\DTOs;

use App\Domain\Entities\News;

class NewsDTO
{
    public function __construct(
        public int $id,
        public string $title,
        public string $content,
        public string $slug,
        public string $createdAt,
        public string $updatedAt
    ) {}

    public static function fromEntity(News $news): self
    {
        return new self(
            $news->id,
            $news->title,
            $news->content,
            (string)$news->slug,
            $news->createdAt->format('Y-m-d H:i:s'),
            $news->updatedAt->format('Y-m-d H:i:s')
        );
    }
}