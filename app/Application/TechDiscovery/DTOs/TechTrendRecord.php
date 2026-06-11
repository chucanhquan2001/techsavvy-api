<?php

namespace App\Application\TechDiscovery\DTOs;

use Carbon\CarbonInterface;

class TechTrendRecord
{
    public function __construct(
        public readonly string $type,
        public readonly string $source,
        public readonly string $sourceUrl,
        public readonly string $externalId,
        public readonly string $title,
        public readonly string $url,
        public readonly ?string $description = null,
        public readonly ?string $readmeExcerpt = null,
        public readonly ?string $summary = null,
        public readonly ?string $howItWorks = null,
        public readonly array $technologies = [],
        public readonly ?string $language = null,
        public readonly array $topics = [],
        public readonly ?int $stars = null,
        public readonly ?int $forks = null,
        public readonly ?int $starsToday = null,
        public readonly ?CarbonInterface $publishedAt = null,
        public readonly ?CarbonInterface $trendDate = null,
        public readonly array $rawPayload = [],
        public readonly string $status = 'ready',
    ) {}

    public function contentHash(): string
    {
        return hash('sha256', implode('|', [
            $this->title,
            $this->description,
            $this->readmeExcerpt,
            $this->summary,
            $this->howItWorks,
            json_encode($this->technologies),
            json_encode($this->topics),
        ]));
    }
}
