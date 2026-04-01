<?php

namespace App\Application\Contact\DTOs;

use App\Domain\Entities\Contact;

class ContactDTO
{
    public function __construct(
        public int $id,
        public int $userId,
        public string $content,
        public string $createdAt,
        public string $updatedAt
    ) {}

    public static function fromEntity(Contact $contact): self
    {
        return new self(
            id: $contact->id,
            userId: $contact->userId,
            content: $contact->content,
            createdAt: $contact->createdAt->format('Y-m-d H:i:s'),
            updatedAt: $contact->updatedAt->format('Y-m-d H:i:s')
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'content' => $this->content,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
