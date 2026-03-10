<?php

namespace App\Domain\Entities;

class UserVisit
{
    public function __construct(
        public int $id,
        public string $ipAddress,
        public ?string $userAgent,
        public ?string $referer,
        public ?string $fullUrl,
        public ?string $deviceType,
        public ?string $browser,
        public ?string $browserVersion,
        public ?string $platform,
        public ?string $platformVersion,
        public ?string $countryCode,
        public ?string $countryName,
        public ?string $fbclid,
        public ?string $gclid,
        public ?string $utmSource,
        public ?string $utmMedium,
        public ?string $utmCampaign,
        public ?string $utmContent,
        public ?string $utmTerm,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $updatedAt
    ) {}
}
