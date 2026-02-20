<?php

namespace Jenishev\Laravel\Contacts\Casts\Strategies;

use Jenishev\Laravel\Contacts\Contracts\ContactValueStrategy;
use League\Uri\Contracts\UriException;
use League\Uri\Uri;

/**
 * Validates and normalizes URLs (auto-adds https://, removes trailing slash).
 */
class UrlStrategy implements ContactValueStrategy
{
    use FailsWithType;

    /**
     * {@inheritDoc}
     */
    public function get(mixed $value, array $attributes): string
    {
        return (string) $value;
    }

    /**
     * {@inheritDoc}
     */
    public function set(mixed $raw, array $attributes): string
    {
        $value = trim((string) $raw);

        if ($value === '') {
            $this->fail('is empty');
        }

        if (! preg_match('#^https?://#i', $value)) {
            $value = 'https://' . $value;
        }

        $value = rtrim($value, '/');

        try {
            Uri::new($value);

            return $value;
        } catch (UriException $e) {
            $this->fail("invalid format: '$raw' (" . $e->getMessage() . ')');
        }
    }
}
