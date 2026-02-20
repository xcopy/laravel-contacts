<?php

namespace Jenishev\Laravel\Contacts\Casts\Strategies;

use InvalidArgumentException;

/**
 * Provides validation error handling for contact strategies.
 */
trait FailsWithType
{
    /**
     * Throw an exception with a formatted error message.
     *
     * @throws InvalidArgumentException
     */
    protected function fail(string $message): never
    {
        $type = class_basename(static::class);
        $type = preg_replace('/Strategy$/', '', $type);

        throw new InvalidArgumentException("{$type}: {$message}");
    }
}
