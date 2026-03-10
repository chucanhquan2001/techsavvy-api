<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\UserVisit;

interface UserVisitRepositoryInterface
{
    public function create(UserVisit $userVisit): UserVisit;
    public function findById(int $id): ?UserVisit;
    public function findAll(int $perPage = 15): array;
    public function findByIpAddress(string $ipAddress): array;
    public function findByFbclid(string $fbclid): ?UserVisit;
    public function findByGclid(string $gclid): ?UserVisit;
    public function delete(int $id): void;
}
