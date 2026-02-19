<?php

namespace Jenishev\Laravel\Contacts\Casts\Strategies;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Jenishev\Laravel\Contacts\Concerns\HasContactValidation;
use Jenishev\Laravel\Contacts\Contracts\ContactValueStrategy;

/**
 * Validates and normalizes email addresses (RFC compliant, lowercased).
 */
class EmailStrategy implements ContactValueStrategy
{
    use HasContactValidation;

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
            $this->fail('address is empty');
        }

        $email = strtolower($value);

        if (! (new EmailValidator)->isValid($email, new RFCValidation)) {
            $this->fail("invalid format: '$value'");
        }

        return $email;
    }
}
