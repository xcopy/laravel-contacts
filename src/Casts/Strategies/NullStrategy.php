<?php

namespace Jenishev\Laravel\Contacts\Casts\Strategies;

use Jenishev\Laravel\Contacts\Contracts\ContactValueStrategy;

/**
 * Fallback strategy that performs no validation or transformation.
 */
class NullStrategy implements ContactValueStrategy
{
    /**
     * {@inheritDoc}
     */
    public function get(mixed $value, array $attributes): mixed
    {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function set(mixed $raw, array $attributes): ?string
    {
        return (string) $raw;
    }
}
