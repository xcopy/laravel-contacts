<?php

namespace Jenishev\Laravel\Contacts\Casts\Strategies;

/**
 * Strategy interface for contact value validation and normalization.
 */
interface Strategy
{
    /**
     * Transform the stored value when retrieving from a database.
     */
    public function get(mixed $value, array $attributes): mixed;

    /**
     * Validate and normalize the value before storing to a database.
     */
    public function set(mixed $raw, array $attributes): ?string;
}
