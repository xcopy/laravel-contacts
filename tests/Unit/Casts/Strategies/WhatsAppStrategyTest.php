<?php

use Jenishev\Laravel\Contacts\Casts\Strategies\WhatsAppStrategy;

it('formats WhatsApp number to E164', function () {
    $strategy = new WhatsAppStrategy;

    expect($strategy->set('+996555123456', ['country_code' => 'KG']))->toBe('+996555123456')
        ->and($strategy->set('0555123456', ['country_code' => 'KG']))->toBe('+996555123456');
});

it('returns WhatsApp number as string on get', function () {
    $strategy = new WhatsAppStrategy;

    expect($strategy->get('+996555123456', []))->toBe('+996555123456')
        ->and($strategy->get('+996555123456', []))->toBeString();
});

it('uses default country code from config', function () {
    config()->set('contacts.default_country_code', 'KG');

    $strategy = new WhatsAppStrategy;

    expect($strategy->set('0555123456', []))->toBe('+996555123456');
});

it('accepts country code from attributes', function () {
    $strategy = new WhatsAppStrategy;

    expect($strategy->set('+12025551234', ['country_code' => 'US']))->toBe('+12025551234');
});

it('validates WhatsApp number is valid', function () {
    $strategy = new WhatsAppStrategy;

    expect($strategy->set('+996555123456', ['country_code' => 'KG']))->toBe('+996555123456');
});

it('throws exception for empty WhatsApp number', function () {
    $strategy = new WhatsAppStrategy;

    $strategy->set('', ['country_code' => 'KG']);
})->throws(InvalidArgumentException::class);

it('throws exception for whitespace-only WhatsApp number', function () {
    $strategy = new WhatsAppStrategy;

    $strategy->set('   ', ['country_code' => 'KG']);
})->throws(InvalidArgumentException::class);

it('throws exception when country code is missing', function () {
    $strategy = new WhatsAppStrategy;

    $strategy->set('+996555123456', ['country_code' => '']);
})->throws(InvalidArgumentException::class);

it('throws exception for invalid country code format', function () {
    $strategy = new WhatsAppStrategy;

    $strategy->set('+996555123456', ['country_code' => 'KGZ']);
})->throws(InvalidArgumentException::class);

it('throws exception for invalid WhatsApp number', function () {
    $strategy = new WhatsAppStrategy;

    $strategy->set('123', ['country_code' => 'KG']);
})->throws(InvalidArgumentException::class);

it('throws exception when number does not belong to country', function () {
    $strategy = new WhatsAppStrategy;

    $strategy->set('+12025551234', ['country_code' => 'KG']);
})->throws(InvalidArgumentException::class);

it('trims input before validation', function () {
    $strategy = new WhatsAppStrategy;

    expect($strategy->set('  +996555123456  ', ['country_code' => 'KG']))->toBe('+996555123456');
});

it('normalizes country code to uppercase', function () {
    $strategy = new WhatsAppStrategy;

    expect($strategy->set('+996555123456', ['country_code' => 'kg']))->toBe('+996555123456');
});

it('accepts number with country code prefix', function () {
    $strategy = new WhatsAppStrategy;

    expect($strategy->set('+996555123456', ['country_code' => 'KG']))->toBe('+996555123456');
});

it('accepts number without country code prefix', function () {
    $strategy = new WhatsAppStrategy;

    expect($strategy->set('0555123456', ['country_code' => 'KG']))->toBe('+996555123456');
});
