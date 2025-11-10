<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Slug;

class News
{
    public function __construct(
        public int $id,
        public string $title,
        public string $content,
        public Slug $slug,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $updatedAt
    )
    {
        
    }

    public function rename(string $title): void
    {
        $this->title = $title;
        $this->slug = Slug::fromTitle($title);
    }
}