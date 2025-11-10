<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Repositories\NewsRepositoryInterface;
use App\Domain\Entities\News;
use App\Domain\ValueObjects\Slug;
use App\Models\News as EloquentNews;

class EloquentNewsRepository implements NewsRepositoryInterface
{
    public function findById(int $id): ?News
    {
        $model = EloquentNews::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findAll(): array
    {
        return array_map([$this, 'toEntity'], EloquentNews::all()->all());
    }

    public function create(News $news): News
    {
        $model = EloquentNews::create([
            'title' => $news->title,
            'slug' => (string)$news->slug,
            'content' => $news->content,
        ]);
        return $this->toEntity($model);
    }

    public function update(News $news): News
    {
        $model = EloquentNews::findOrFail($news->id);
        $model->update([
            'title' => $news->title,
            'slug' => (string)$news->slug,
            'content' => $news->content,
        ]);
        return $this->toEntity($model);
    }

    public function delete(int $id): void
    {
        EloquentNews::destroy($id);
    }

    private function toEntity(EloquentNews $model): News
    {
        return new News(
            $model->id,
            $model->title,
            $model->content,
            new Slug($model->slug),
            new \DateTimeImmutable($model->created_at),
            new \DateTimeImmutable($model->updated_at)
        );
    }
}