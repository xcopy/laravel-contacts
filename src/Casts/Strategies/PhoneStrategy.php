<?php

namespace Jenishev\Laravel\Contacts\Casts\Strategies;

use Illuminate\Support\Str;
use Jenishev\Laravel\Contacts\Contracts\ContactValueStrategy;
use libphonenumber\PhoneNumberFormat;

/**
 * Validates and formats phone numbers with configurable format and country code support.
 */
class PhoneStrategy implements ContactValueStrategy
{
    use FailsWithType;

    private static array $cache = [];

    /**
     * {@inheritDoc}
     */
    public function get(mixed $value, array $attributes): string
    {
        if (! is_string($value) || $value === '') {
            return $value;
        }

        $country_code = Str::upper(trim($attributes['country_code'] ?? config('contacts.default_country_code', 'KG')));
        $format = config('contacts.phone_format_get', PhoneNumberFormat::NATIONAL->name);

        $key = $value . '|' . $country_code . '|' . $format;

        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        return self::$cache[$key] = phone($value, $country_code)->format($format);
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
            $this->fail("invalid country code format: '$country' (expected ISO 3166-1 alpha-2)");
        }

        $phone = phone($input, $country);

        if (! $phone->isValid()) {
            $this->fail("invalid phone number: '$input'");
        }

        if (! $phone->isOfCountry($country)) {
            $this->fail("number does not belong to country '$country': '$input'");
        }

        $format = config('contacts.phone_format_set', PhoneNumberFormat::NATIONAL->name);

        return $phone->format($format);
    }
}
