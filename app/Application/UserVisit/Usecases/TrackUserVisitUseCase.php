<?php

namespace App\Application\UserVisit\Usecases;

use App\Application\UserVisit\Commands\TrackUserVisitCommand;
use App\Application\UserVisit\DTOs\UserVisitDTO;
use App\Domain\Entities\UserVisit;
use App\Domain\Repositories\UserVisitRepositoryInterface;

class TrackUserVisitUseCase
{
    public function __construct(
        private UserVisitRepositoryInterface $repository
    ) {}

    public function execute(TrackUserVisitCommand $command): UserVisitDTO
    {
        $now = new \DateTimeImmutable();

        $entity = new UserVisit(
            id: 0,
            ipAddress: $command->ipAddress,
            userAgent: $command->userAgent,
            referer: $command->referer,
            fullUrl: $command->fullUrl,
            deviceType: $command->deviceType,
            browser: $command->browser,
            browserVersion: $command->browserVersion,
            platform: $command->platform,
            platformVersion: $command->platformVersion,
            countryCode: $command->countryCode,
            countryName: $command->countryName,
            fbclid: $command->fbclid,
            gclid: $command->gclid,
            utmSource: $command->utmSource,
            utmMedium: $command->utmMedium,
            utmCampaign: $command->utmCampaign,
            utmContent: $command->utmContent,
            utmTerm: $command->utmTerm,
            createdAt: $now,
            updatedAt: $now
        );

        $saved = $this->repository->create($entity);

        return UserVisitDTO::fromEntity($saved);
    }
}
