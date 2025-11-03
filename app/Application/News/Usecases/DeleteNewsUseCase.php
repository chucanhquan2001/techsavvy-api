<?php

namespace App\Application\News\UseCases;

use App\Domain\Repositories\NewsRepositoryInterface;

class DeleteNewsUseCase
{
    public function __construct(private NewsRepositoryInterface $repo) {}

    public function execute(int $id): void
    {
        $this->repo->delete($id);
    }
}