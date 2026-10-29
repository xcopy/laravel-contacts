<?php

use Jenishev\Laravel\Contacts\Enums\ContactTypes;

it('has all expected cases', function () {
    $cases = ContactTypes::cases();

    expect($cases)->toContain(ContactTypes::Phone)
        ->and($cases)->toContain(ContactTypes::Email)
        ->and($cases)->toContain(ContactTypes::Whatsapp)
        ->and($cases)->toContain(ContactTypes::Telegram)
        ->and($cases)->toContain(ContactTypes::Url)
        ->and($cases)->toContain(ContactTypes::Other);
});

it('has correct values for each case', function () {
    expect(ContactTypes::Phone->value)->toBe('phone')
        ->and(ContactTypes::Email->value)->toBe('email')
        ->and(ContactTypes::Whatsapp->value)->toBe('whatsapp')
        ->and(ContactTypes::Telegram->value)->toBe('telegram')
        ->and(ContactTypes::Url->value)->toBe('url')
        ->and(ContactTypes::Other->value)->toBe('other');
});

it('can be instantiated from value', function () {
    expect(ContactTypes::from('phone'))->toBe(ContactTypes::Phone)
        ->and(ContactTypes::from('email'))->toBe(ContactTypes::Email)
        ->and(ContactTypes::from('whatsapp'))->toBe(ContactTypes::Whatsapp)
        ->and(ContactTypes::from('telegram'))->toBe(ContactTypes::Telegram)
        ->and(ContactTypes::from('url'))->toBe(ContactTypes::Url)
        ->and(ContactTypes::from('other'))->toBe(ContactTypes::Other);
});

it('can be instantiated with tryFrom', function () {
    /** @noinspection PhpCaseWithValueNotFoundInEnumInspection */
    expect(ContactTypes::tryFrom('phone'))->toBe(ContactTypes::Phone)
        ->and(ContactTypes::tryFrom('email'))->toBe(ContactTypes::Email)
        ->and(ContactTypes::tryFrom('invalid'))->toBeNull();
});

it('throws exception for invalid value with from', function () {
    /** @noinspection PhpCaseWithValueNotFoundInEnumInspection */
    /** @noinspection PhpExpressionResultUnusedInspection */
    ContactTypes::from('invalid');
})->throws(ValueError::class);

it('can be compared', function () {
    $phone1 = ContactTypes::Phone;
    $phone2 = ContactTypes::Phone;
    $email = ContactTypes::Email;

    expect($phone1)->toBe($phone2)
        ->and($phone1)->not->toBe($email)
        ->and($phone1 === $phone2)->toBeTrue()
        ->and($phone1 === $email)->toBeFalse();
});

it('returns all values using HasValues trait', function () {
    $values = ContactTypes::values();

    expect($values)->toBeArray()
        ->and($values)->toContain('phone')
        ->and($values)->toContain('email')
        ->and($values)->toContain('whatsapp')
        ->and($values)->toContain('telegram')
        ->and($values)->toContain('url')
        ->and($values)->toContain('other');
});

it('returns choices using HasChoices trait', function () {
    $choices = ContactTypes::choices();

    expect($choices)
        ->toBeArray()
        ->and($choices)
        ->toHaveKeys(['phone', 'email', 'whatsapp', 'telegram', 'url', 'other']);
});

it('choices return case names as values', function () {
    $choices = ContactTypes::choices();

    expect($choices['phone'])->toBe('Phone')
        ->and($choices['email'])->toBe('Email')
        ->and($choices['whatsapp'])->toBe('WhatsApp')
        ->and($choices['telegram'])->toBe('Telegram')
        ->and($choices['url'])->toBe('URL')
        ->and($choices['other'])->toBe('Other');
});

it('values are unique', function () {
    $values = ContactTypes::values();
    $uniqueValues = array_unique($values);

    expect($values)->toHaveCount(count($uniqueValues));
});

it('case names follow PascalCase convention', function () {
    foreach (ContactTypes::cases() as $case) {
        expect($case->name)->toMatch('/^[A-Z][a-zA-Z]*$/');
    }
});

it('values are lowercase', function () {
    foreach (ContactTypes::cases() as $case) {
        expect($case->value)->toBe(strtolower($case->value));
    }
});

it('has messaging app cases', function () {
    expect(ContactTypes::Whatsapp)->toBeInstanceOf(ContactTypes::class)
        ->and(ContactTypes::Telegram)->toBeInstanceOf(ContactTypes::class)
        ->and(ContactTypes::Whatsapp->value)->toBe('whatsapp')
        ->and(ContactTypes::Telegram->value)->toBe('telegram');
});

it('has digital contact cases', function () {
    expect(ContactTypes::Email)->toBeInstanceOf(ContactTypes::class)
        ->and(ContactTypes::Url)->toBeInstanceOf(ContactTypes::class)
        ->and(ContactTypes::Email->value)->toBe('email')
        ->and(ContactTypes::Url->value)->toBe('url');
});

it('has other as fallback case', function () {
    expect(ContactTypes::Other)->toBeInstanceOf(ContactTypes::class)
        ->and(ContactTypes::Other->value)->toBe('other');
});
