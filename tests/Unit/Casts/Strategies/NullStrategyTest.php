<?php

use Jenishev\Laravel\Contacts\Casts\Strategies\NullStrategy;

it('returns value as-is on get', function () {
    $strategy = new NullStrategy;

    expect($strategy->get('test', []))->toBe('test')
        ->and($strategy->get('123', []))->toBe('123')
        ->and($strategy->get('', []))->toBe('');
});

it('casts value to string on set', function () {
    $strategy = new NullStrategy;

    expect($strategy->set('test', []))->toBe('test')
        ->and($strategy->set(123, []))->toBe('123')
        ->and($strategy->set('', []))->toBe('');
});

it('handles null value on set', function () {
    $strategy = new NullStrategy;

    expect($strategy->set(null, []))->toBe('');
});

it('handles numeric values on set', function () {
    $strategy = new NullStrategy;

    expect($strategy->set(123, []))->toBe('123')
        ->and($strategy->set(45.67, []))->toBe('45.67');
});

it('handles boolean values on set', function () {
    $strategy = new NullStrategy;

    expect($strategy->set(true, []))->toBe('1')
        ->and($strategy->set(false, []))->toBe('');
});

it('handles mixed values', function () {
    $strategy = new NullStrategy;

    expect($strategy->get(null, []))->toBeNull()
        ->and($strategy->get(123, []))->toBe(123)
        ->and($strategy->get(true, []))->toBeTrue();
});

it('does not perform any validation', function () {
    $strategy = new NullStrategy;

    expect($strategy->set('any value', []))->toBe('any value')
        ->and($strategy->set('invalid@email', []))->toBe('invalid@email')
        ->and($strategy->set('not-a-url', []))->toBe('not-a-url');
});

it('preserves whitespace on set', function () {
    $strategy = new NullStrategy;

    expect($strategy->set('  test  ', []))->toBe('  test  ');
});

it('preserves case on set', function () {
    $strategy = new NullStrategy;

    expect($strategy->set('TeSt', []))->toBe('TeSt');
});
