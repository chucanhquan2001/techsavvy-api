<?php

namespace App\Application\TechDiscovery\Contracts;

interface TechTrendProviderInterface
{
    public function key(): string;

    public function fetch(): array;
}
