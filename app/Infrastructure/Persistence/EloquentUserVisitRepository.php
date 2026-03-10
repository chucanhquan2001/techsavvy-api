<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\UserVisit as DomainUserVisit;
use App\Domain\Repositories\UserVisitRepositoryInterface;
use App\Models\UserVisit as ModelUserVisit;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentUserVisitRepository implements UserVisitRepositoryInterface
{
    public function create(DomainUserVisit $userVisit): DomainUserVisit
    {
        $model = ModelUserVisit::create([
            'ip_address' => $userVisit->ipAddress,
            'user_agent' => $userVisit->userAgent,
            'referer' => $userVisit->referer,
            'full_url' => $userVisit->fullUrl,
            'device_type' => $userVisit->deviceType,
            'browser' => $userVisit->browser,
            'browser_version' => $userVisit->browserVersion,
            'platform' => $userVisit->platform,
            'platform_version' => $userVisit->platformVersion,
            'country_code' => $userVisit->countryCode,
            'country_name' => $userVisit->countryName,
            'fbclid' => $userVisit->fbclid,
            'gclid' => $userVisit->gclid,
            'utm_source' => $userVisit->utmSource,
            'utm_medium' => $userVisit->utmMedium,
            'utm_campaign' => $userVisit->utmCampaign,
            'utm_content' => $userVisit->utmContent,
            'utm_term' => $userVisit->utmTerm,
        ]);

        return $this->toDomain($model);
    }

    public function findById(int $id): ?DomainUserVisit
    {
        $model = ModelUserVisit::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function findAll(int $perPage = 15): array
    {
        $models = ModelUserVisit::orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $models->items();
    }

    public function findByIpAddress(string $ipAddress): array
    {
        $models = ModelUserVisit::where('ip_address', $ipAddress)
            ->orderBy('created_at', 'desc')
            ->get();

        return $models->map(fn($m) => $this->toDomain($m))->all();
    }

    public function findByFbclid(string $fbclid): ?DomainUserVisit
    {
        $model = ModelUserVisit::where('fbclid', $fbclid)->first();
        return $model ? $this->toDomain($model) : null;
    }

    public function findByGclid(string $gclid): ?DomainUserVisit
    {
        $model = ModelUserVisit::where('gclid', $gclid)->first();
        return $model ? $this->toDomain($model) : null;
    }

    public function delete(int $id): void
    {
        ModelUserVisit::destroy($id);
    }

    private function toDomain(ModelUserVisit $model): DomainUserVisit
    {
        return new DomainUserVisit(
            id: $model->id,
            ipAddress: $model->ip_address,
            userAgent: $model->user_agent,
            referer: $model->referer,
            fullUrl: $model->full_url,
            deviceType: $model->device_type,
            browser: $model->browser,
            browserVersion: $model->browser_version,
            platform: $model->platform,
            platformVersion: $model->platform_version,
            countryCode: $model->country_code,
            countryName: $model->country_name,
            fbclid: $model->fbclid,
            gclid: $model->gclid,
            utmSource: $model->utm_source,
            utmMedium: $model->utm_medium,
            utmCampaign: $model->utm_campaign,
            utmContent: $model->utm_content,
            utmTerm: $model->utm_term,
            createdAt: $model->created_at->toImmutable(),
            updatedAt: $model->updated_at->toImmutable()
        );
    }
}
