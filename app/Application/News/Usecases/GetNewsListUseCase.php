<?php

namespace App\Application\News\UseCases;

use App\Application\News\DTOs\NewsDTO;
use App\Domain\Repositories\NewsRepositoryInterface;

class GetNewsListUseCase
{
    public function __construct(private NewsRepositoryInterface $repo) {}

    public function execute(): array
    {
        return array_map(fn($news) => NewsDTO::fromEntity($news), $this->repo->findAll());
    }
}