<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\News;

interface NewsRepositoryInterface
{
    public function findById(int $id): ?News;
    public function findAll(): array;
    public function create(News $news): News;
    public function update(News $news): News;
    public function delete(int $id): void; // not return
}