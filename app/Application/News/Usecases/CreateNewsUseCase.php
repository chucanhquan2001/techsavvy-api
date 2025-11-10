<?php

namespace App\Application\News\UseCases;

use App\Application\News\Commands\CreateNewsCommand;
use App\Application\News\DTOs\NewsDTO;
use App\Domain\Repositories\NewsRepositoryInterface;
use App\Domain\Entities\News;
use App\Domain\ValueObjects\Slug;

class CreateNewsUseCase
{
    public function __construct(private NewsRepositoryInterface $repo) {}

    public function execute(CreateNewsCommand $cmd): NewsDTO
    {
        $now = new \DateTimeImmutable();
        $entity = new News(0, $cmd->title, $cmd->content, Slug::fromTitle($cmd->title), $now, $now);
        $saved = $this->repo->create($entity);
        return NewsDTO::fromEntity($saved);
    }
}
