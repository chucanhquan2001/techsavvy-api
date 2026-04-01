<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\Contact as DomainContact;
use App\Domain\Repositories\ContactRepositoryInterface;
use App\Models\Contact as ModelContact;

class EloquentContactRepository implements ContactRepositoryInterface
{
    public function create(DomainContact $contact): DomainContact
    {
        $model = ModelContact::create([
            'user_id' => $contact->userId,
            'content' => $contact->content,
        ]);

        return $this->toDomain($model);
    }

    private function toDomain(ModelContact $model): DomainContact
    {
        return new DomainContact(
            id: $model->id,
            userId: $model->user_id,
            content: $model->content,
            createdAt: $model->created_at->toImmutable(),
            updatedAt: $model->updated_at->toImmutable()
        );
    }
}
