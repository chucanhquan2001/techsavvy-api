<?php

namespace App\Application\News\UseCases;

use App\Application\News\Commands\UpdateNewsCommand;
use App\Application\News\DTOs\NewsDTO;
use App\Domain\Repositories\NewsRepositoryInterface;

class UpdateNewsUseCase
{
    public function __construct(private NewsRepositoryInterface $repo) {}

    public function execute(UpdateNewsCommand $cmd): ?NewsDTO
    {
        $news = $this->repo->findById($cmd->id);
        if (!$news) return null;

        $news->rename($cmd->title);
        $news->content = $cmd->content;

        $updated = $this->repo->update($news);
        return NewsDTO::fromEntity($updated);
    }
}
