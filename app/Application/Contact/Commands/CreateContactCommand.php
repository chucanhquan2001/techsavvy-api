<?php

namespace App\Application\Contact\Commands;

class CreateContactCommand
{
    public function __construct(
        public int $userId,
        public string $content
    ) {}
}
