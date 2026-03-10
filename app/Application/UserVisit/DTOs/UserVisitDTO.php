<?php

namespace App\Application\UserVisit\DTOs;

use App\Domain\Entities\UserVisit;

class UserVisitDTO
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
        public string $createdAt,
        public string $updatedAt
    ) {}

    public static function fromEntity(UserVisit $userVisit): self
    {
        return new self(
            id: $userVisit->id,
            ipAddress: $userVisit->ipAddress,
            userAgent: $userVisit->userAgent,
            referer: $userVisit->referer,
            fullUrl: $userVisit->fullUrl,
            deviceType: $userVisit->deviceType,
            browser: $userVisit->browser,
            browserVersion: $userVisit->browserVersion,
            platform: $userVisit->platform,
            platformVersion: $userVisit->platformVersion,
            countryCode: $userVisit->countryCode,
            countryName: $userVisit->countryName,
            fbclid: $userVisit->fbclid,
            gclid: $userVisit->gclid,
            utmSource: $userVisit->utmSource,
            utmMedium: $userVisit->utmMedium,
            utmCampaign: $userVisit->utmCampaign,
            utmContent: $userVisit->utmContent,
            utmTerm: $userVisit->utmTerm,
            createdAt: $userVisit->createdAt->format('Y-m-d H:i:s'),
            updatedAt: $userVisit->updatedAt->format('Y-m-d H:i:s')
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'referer' => $this->referer,
            'full_url' => $this->fullUrl,
            'device_type' => $this->deviceType,
            'browser' => $this->browser,
            'browser_version' => $this->browserVersion,
            'platform' => $this->platform,
            'platform_version' => $this->platformVersion,
            'country_code' => $this->countryCode,
            'country_name' => $this->countryName,
            'fbclid' => $this->fbclid,
            'gclid' => $this->gclid,
            'utm_source' => $this->utmSource,
            'utm_medium' => $this->utmMedium,
            'utm_campaign' => $this->utmCampaign,
            'utm_content' => $this->utmContent,
            'utm_term' => $this->utmTerm,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
