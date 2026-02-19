<?php

use Jenishev\Laravel\Contacts\Casts\Strategies\PhoneStrategy;
use libphonenumber\PhoneNumberFormat;

it('formats phone number to NATIONAL by default', function () {
    $strategy = new PhoneStrategy;

    expect($strategy->set('+996555123456', ['country_code' => 'KG']))->toBe('0555 123 456')
        ->and($strategy->set('0555123456', ['country_code' => 'KG']))->toBe('0555 123 456');
});

it('formats phone number based on config', function () {
    config()->set('contacts.phone_format_set', PhoneNumberFormat::E164->name);

    $strategy = new PhoneStrategy;

    expect($strategy->set('+996555123456', ['country_code' => 'KG']))->toBe('+996555123456');
});

it('returns formatted string on get', function () {
    $strategy = new PhoneStrategy;

    $result = $strategy->get('0555 123456', ['country_code' => 'KG']);

    expect($result)->toBe('0555 123 456');
});

it('formats get based on config', function () {
    config()->set('contacts.phone_format_get', PhoneNumberFormat::E164->name);

    $strategy = new PhoneStrategy;

    expect($strategy->get('0555123456', ['country_code' => 'KG']))->toBe('+996555123456');
});

it('returns string if value is empty on get', function () {
    $strategy = new PhoneStrategy;

    expect($strategy->get('', ['country_code' => 'KG']))->toBe('');
});

it('uses default country code from config', function () {
    config()->set('contacts.default_country_code', 'KG');

    $strategy = new PhoneStrategy;

    expect($strategy->set('0555123456', []))->toBe('0555 123 456');
});

it('accepts country code from attributes', function () {
    $strategy = new PhoneStrategy;

    expect($strategy->set('2025551234', ['country_code' => 'US']))->toBe('(202) 555-1234');
});

it('validates phone number is valid', function () {
    $strategy = new PhoneStrategy;

    expect($strategy->set('+996555123456', ['country_code' => 'KG']))->toBe('0555 123 456');
});

it('throws exception for empty phone number', function () {
    $strategy = new PhoneStrategy;

    $strategy->set('', ['country_code' => 'KG']);
})->throws(InvalidArgumentException::class);

it('throws exception for whitespace-only phone number', function () {
    $strategy = new PhoneStrategy;

    $strategy->set('   ', ['country_code' => 'KG']);
})->throws(InvalidArgumentException::class);

it('throws exception when country code is missing', function () {
    $strategy = new PhoneStrategy;

    $strategy->set('+996555123456', ['country_code' => '']);
})->throws(InvalidArgumentException::class);

it('throws exception for invalid country code format', function () {
    $strategy = new PhoneStrategy;

    $strategy->set('+996555123456', ['country_code' => 'KGZ']);
})->throws(InvalidArgumentException::class);

it('throws exception for invalid phone number', function () {
    $strategy = new PhoneStrategy;

    $strategy->set('123', ['country_code' => 'KG']);
})->throws(InvalidArgumentException::class);

it('throws exception when number does not belong to country', function () {
    $strategy = new PhoneStrategy;

    $strategy->set('+12025551234', ['country_code' => 'KG']);
})->throws(InvalidArgumentException::class);

it('trims input before validation', function () {
    $strategy = new PhoneStrategy;

    expect($strategy->set('  +996555123456  ', ['country_code' => 'KG']))->toBe('0555 123 456');
});

it('normalizes country code to uppercase', function () {
    $strategy = new PhoneStrategy;

    expect($strategy->set('+996555123456', ['country_code' => 'kg']))->toBe('0555 123 456');
});

it('caches phone number instances', function () {
    $strategy = new PhoneStrategy;

    $first = $strategy->get('0555 123 456', ['country_code' => 'KG']);
    $second = $strategy->get('0555 123 456', ['country_code' => 'KG']);

    expect($first)->toBe($second);
});

it('accepts phone with country code prefix', function () {
    $strategy = new PhoneStrategy;

    expect($strategy->set('+996555123456', ['country_code' => 'KG']))->toBe('0555 123 456');
});

it('accepts phone without country code prefix', function () {
    $strategy = new PhoneStrategy;

    expect($strategy->set('0555123456', ['country_code' => 'KG']))->toBe('0555 123 456');
});
