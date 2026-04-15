<?php

namespace App\Application\Contact\Usecases;

use App\Application\Contact\Commands\CreateContactCommand;
use App\Application\Contact\DTOs\ContactDTO;
use App\Domain\Entities\Contact;
use App\Domain\Repositories\ContactRepositoryInterface;

class CreateContactUseCase
{
    public function __construct(private ContactRepositoryInterface $repository) {}

    public function execute(CreateContactCommand $command): ContactDTO
    {
        $now = new \DateTimeImmutable();

        $contact = new Contact(
            id: 0,
            userId: $command->userId,
            content: $command->content,
            createdAt: $now,
            updatedAt: $now
        );

        $saved = $this->repository->create($contact);

        return ContactDTO::fromEntity($saved);
    }
}
