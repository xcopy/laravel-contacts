<?php

namespace Jenishev\Laravel\Contacts\Casts\Strategies;

use Illuminate\Support\Str;
use Jenishev\Laravel\Contacts\Concerns\HasContactValidation;
use Jenishev\Laravel\Contacts\Contracts\ContactValueStrategy;

/**
 * Validates and formats WhatsApp numbers to E.164 format with country code support.
 */
class WhatsAppStrategy implements ContactValueStrategy
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
        $input = trim((string) $raw);
        $country = Str::upper(trim($attributes['country_code'] ?? config('contacts.default_country_code', 'KG')));

        if ($input === '') {
            $this->fail('number is empty');
        }

        if ($country === '') {
            $this->fail('country code is required');
        }

        if (! preg_match('/^[A-Z]{2}$/', $country)) {
            $this->fail("invalid country code format: '$country'");
        }

        $phone = phone($input, $country);

        if (! $phone->isValid()) {
            $this->fail("invalid WhatsApp number: '$input'");
        }

        if (! $phone->isOfCountry($country)) {
            $this->fail("number does not belong to country '$country': '$input'");
        }

        return $phone->formatE164();
    }
}
