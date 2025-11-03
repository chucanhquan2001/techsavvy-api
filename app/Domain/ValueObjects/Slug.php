<?php

namespace App\Domain\ValueObjects;

final class Slug
{
    public function __construct(private string $value)
    {
        if (trim($value) === '') {
            throw new \InvalidArgumentException('Slug cannot be empty');
        }
    }
    
    public function value(): string
    {
        return $this->value;
    }

    public static function fromTitle(string $title): self
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        return new self($slug);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}