<?php

use Jenishev\Laravel\Contacts\Casts\Strategies\TelegramStrategy;

it('validates and normalizes telegram username', function () {
    $strategy = new TelegramStrategy;

    expect($strategy->set('username', []))->toBe('username')
        ->and($strategy->set('@username', []))->toBe('username')
        ->and($strategy->set('User_Name', []))->toBe('user_name');
});

it('returns username with @ prefix on get', function () {
    $strategy = new TelegramStrategy;

    expect($strategy->get('username', []))->toBe('@username')
        ->and($strategy->get('@username', []))->toBe('@username');
});

it('returns empty string without @ prefix on get', function () {
    $strategy = new TelegramStrategy;

    expect($strategy->get('', []))->toBe('');
});

it('removes @ prefix on set', function () {
    $strategy = new TelegramStrategy;

    expect($strategy->set('@username', []))->toBe('username');
});

it('converts username to lowercase', function () {
    $strategy = new TelegramStrategy;

    expect($strategy->set('USERNAME', []))->toBe('username')
        ->and($strategy->set('UserName', []))->toBe('username');
});

it('trims whitespace from username', function () {
    $strategy = new TelegramStrategy;

    expect($strategy->set('  username  ', []))->toBe('username');
});

it('throws exception for empty username', function () {
    $strategy = new TelegramStrategy;

    $strategy->set('', []);
})->throws(InvalidArgumentException::class);

it('throws exception for whitespace-only username', function () {
    $strategy = new TelegramStrategy;

    $strategy->set('   ', []);
})->throws(InvalidArgumentException::class);

it('throws exception for username shorter than 5 characters', function () {
    $strategy = new TelegramStrategy;

    $strategy->set('user', []);
})->throws(InvalidArgumentException::class);

it('throws exception for username longer than 32 characters', function () {
    $strategy = new TelegramStrategy;

    $strategy->set('user1234567890123456789012345678901', []);
})->throws(InvalidArgumentException::class);

it('throws exception for username with invalid characters', function () {
    $strategy = new TelegramStrategy;

    $strategy->set('user-name', []);
})->throws(InvalidArgumentException::class);

it('throws exception for username with consecutive underscores', function () {
    $strategy = new TelegramStrategy;

    $strategy->set('user__name', []);
})->throws(InvalidArgumentException::class);

it('throws exception for username starting with underscore', function () {
    $strategy = new TelegramStrategy;

    $strategy->set('_username', []);
})->throws(InvalidArgumentException::class);

it('throws exception for username ending with underscore', function () {
    $strategy = new TelegramStrategy;

    $strategy->set('username_', []);
})->throws(InvalidArgumentException::class);

it('accepts username with single underscore', function () {
    $strategy = new TelegramStrategy;

    expect($strategy->set('user_name', []))->toBe('user_name');
});

it('accepts username with numbers', function () {
    $strategy = new TelegramStrategy;

    expect($strategy->set('user123', []))->toBe('user123');
});

it('accepts 5 character username', function () {
    $strategy = new TelegramStrategy;

    expect($strategy->set('user1', []))->toBe('user1');
});

it('accepts 32 character username', function () {
    $strategy = new TelegramStrategy;

    expect($strategy->set('user123456789012345678901234567', []))->toBe('user123456789012345678901234567');
});

it('accepts alphanumeric username', function () {
    $strategy = new TelegramStrategy;

    expect($strategy->set('user123abc', []))->toBe('user123abc');
});

it('accepts username with mixed case and converts to lowercase', function () {
    $strategy = new TelegramStrategy;

    expect($strategy->set('UserName123', []))->toBe('username123');
});
