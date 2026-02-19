<?php

use Jenishev\Laravel\Contacts\Casts\Strategies\UrlStrategy;

it('validates and normalizes URLs', function () {
    $strategy = new UrlStrategy;

    expect($strategy->set('https://example.com', []))->toBe('https://example.com')
        ->and($strategy->set('http://example.com', []))->toBe('http://example.com')
        ->and($strategy->set('example.com', []))->toBe('https://example.com');
});

it('returns URL as string on get', function () {
    $strategy = new UrlStrategy;

    expect($strategy->get('https://example.com', []))->toBe('https://example.com')
        ->and($strategy->get('https://example.com', []))->toBeString();
});

it('adds https prefix when missing', function () {
    $strategy = new UrlStrategy;

    expect($strategy->set('example.com', []))->toBe('https://example.com')
        ->and($strategy->set('www.example.com', []))->toBe('https://www.example.com');
});

it('preserves http scheme when provided', function () {
    $strategy = new UrlStrategy;

    expect($strategy->set('http://example.com', []))->toBe('http://example.com');
});

it('preserves https scheme when provided', function () {
    $strategy = new UrlStrategy;

    expect($strategy->set('https://example.com', []))->toBe('https://example.com');
});

it('removes trailing slash', function () {
    $strategy = new UrlStrategy;

    expect($strategy->set('https://example.com/', []))->toBe('https://example.com')
        ->and($strategy->set('https://example.com/path/', []))->toBe('https://example.com/path');
});

it('trims whitespace from URL', function () {
    $strategy = new UrlStrategy;

    expect($strategy->set('  https://example.com  ', []))->toBe('https://example.com');
});

it('throws exception for empty URL', function () {
    $strategy = new UrlStrategy;

    $strategy->set('', []);
})->throws(InvalidArgumentException::class);

it('throws exception for whitespace-only URL', function () {
    $strategy = new UrlStrategy;

    $strategy->set('   ', []);
})->throws(InvalidArgumentException::class);

it('throws exception for invalid URL format', function () {
    $strategy = new UrlStrategy;

    $strategy->set('not a url', []);
})->throws(InvalidArgumentException::class);

it('accepts URL with path', function () {
    $strategy = new UrlStrategy;

    expect($strategy->set('https://example.com/path/to/page', []))->toBe('https://example.com/path/to/page');
});

it('accepts URL with query parameters', function () {
    $strategy = new UrlStrategy;

    expect($strategy->set('https://example.com?param=value', []))->toBe('https://example.com?param=value');
});

it('accepts URL with fragment', function () {
    $strategy = new UrlStrategy;

    expect($strategy->set('https://example.com#section', []))->toBe('https://example.com#section');
});

it('accepts URL with subdomain', function () {
    $strategy = new UrlStrategy;

    expect($strategy->set('https://sub.example.com', []))->toBe('https://sub.example.com');
});

it('accepts URL with port', function () {
    $strategy = new UrlStrategy;

    expect($strategy->set('https://example.com:8080', []))->toBe('https://example.com:8080');
});

it('accepts URL with authentication', function () {
    $strategy = new UrlStrategy;

    expect($strategy->set('https://user:pass@example.com', []))->toBe('https://user:pass@example.com');
});

it('accepts URL with international domain name', function () {
    $strategy = new UrlStrategy;

    expect($strategy->set('https://münchen.de', []))->toBe('https://münchen.de');
});

it('accepts URL with hyphens in domain', function () {
    $strategy = new UrlStrategy;

    expect($strategy->set('https://my-site.com', []))->toBe('https://my-site.com');
});

it('accepts complex URL with multiple components', function () {
    $strategy = new UrlStrategy;

    expect($strategy->set('https://user:pass@sub.example.com:8080/path?query=1#hash', []))
        ->toBe('https://user:pass@sub.example.com:8080/path?query=1#hash');
});
