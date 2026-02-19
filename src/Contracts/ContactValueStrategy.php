<?php

namespace Jenishev\Laravel\Contacts\Contracts;

/**
 * Strategy interface for contact value validation and normalization.
 */
interface ContactValueStrategy
{
    /**
     * Transform the stored value when retrieving from database.
     */
    public function get(mixed $value, array $attributes): mixed;

    /**
     * Validate and normalize the value before storing to database.
     */
    public function set(mixed $raw, array $attributes): ?string;
}
