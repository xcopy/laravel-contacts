<?php

namespace Jenishev\Laravel\Contacts\Casts\Strategies;

use Jenishev\Laravel\Contacts\Contracts\ContactValueStrategy;

/**
 * Validates and normalizes Telegram usernames (5-32 chars, alphanumeric + underscore).
 */
class TelegramStrategy implements ContactValueStrategy
{
    use FailsWithType;

    /**
     * {@inheritDoc}
     */
    public function get(mixed $value, array $attributes): string
    {
        $value = (string) $value;

        if ($value !== '' && ! str_starts_with($value, '@')) {
            return '@' . $value;
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function set(mixed $raw, array $attributes): string
    {
        $value = trim((string) $raw);

        if ($value === '') {
            $this->fail('username is empty');
        }

        $username = ltrim($value, '@');

        if (! preg_match('/^[a-zA-Z0-9_]{5,32}$/', $username)) {
            $this->fail("invalid username format: '$value' (5–32 chars, a-z0-9_)");
        }

        if (str_contains($username, '__')) {
            $this->fail("consecutive underscores not allowed: '$value'");
        }

        if (str_starts_with($username, '_') || str_ends_with($username, '_')) {
            $this->fail("cannot start or end with underscore: '$value'");
        }

        return strtolower($username);
    }
}
