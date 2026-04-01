<?php

namespace App\Domain\Entities;

class Contact
{
    public function __construct(
        public int $id,
        public int $userId,
        public string $content,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $updatedAt
    ) {}
}
