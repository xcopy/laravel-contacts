<?php

use Jenishev\Laravel\Contacts\Casts\Strategies\EmailStrategy;

it('validates and normalizes email addresses', function () {
    $strategy = new EmailStrategy;

    expect($strategy->set('test@example.com', []))->toBe('test@example.com')
        ->and($strategy->set('TEST@EXAMPLE.COM', []))->toBe('test@example.com')
        ->and($strategy->set(' test@example.com ', []))->toBe('test@example.com');
});

it('returns email as string on get', function () {
    $strategy = new EmailStrategy;

    expect($strategy->get('test@example.com', []))->toBe('test@example.com')
        ->and($strategy->get('test@example.com', []))->toBeString();
});

it('converts email to lowercase', function () {
    $strategy = new EmailStrategy;

    expect($strategy->set('User@Domain.COM', []))->toBe('user@domain.com');
});

it('trims whitespace from email', function () {
    $strategy = new EmailStrategy;

    expect($strategy->set('  test@example.com  ', []))->toBe('test@example.com');
});

it('throws exception for empty email', function () {
    $strategy = new EmailStrategy;

    $strategy->set('', []);
})->throws(InvalidArgumentException::class);

it('throws exception for whitespace-only email', function () {
    $strategy = new EmailStrategy;

    $strategy->set('   ', []);
})->throws(InvalidArgumentException::class);

it('throws exception for invalid email format', function () {
    $strategy = new EmailStrategy;

    $strategy->set('invalid-email', []);
})->throws(InvalidArgumentException::class);

it('throws exception for email without domain', function () {
    $strategy = new EmailStrategy;

    $strategy->set('test@', []);
})->throws(InvalidArgumentException::class);

it('throws exception for email without at symbol', function () {
    $strategy = new EmailStrategy;

    $strategy->set('test.example.com', []);
})->throws(InvalidArgumentException::class);

it('accepts valid international domain names', function () {
    $strategy = new EmailStrategy;

    expect($strategy->set('test@münchen.de', []))->toBe('test@münchen.de');
});

it('accepts email with plus addressing', function () {
    $strategy = new EmailStrategy;

    expect($strategy->set('user+tag@example.com', []))->toBe('user+tag@example.com');
});

it('accepts email with dots in local part', function () {
    $strategy = new EmailStrategy;

    expect($strategy->set('first.last@example.com', []))->toBe('first.last@example.com');
});

it('accepts email with numbers', function () {
    $strategy = new EmailStrategy;

    expect($strategy->set('user123@example456.com', []))->toBe('user123@example456.com');
});

it('accepts email with hyphens in domain', function () {
    $strategy = new EmailStrategy;

    expect($strategy->set('test@my-domain.com', []))->toBe('test@my-domain.com');
});
