<?php

use Jenishev\Laravel\Contacts\Enums\ContactTypeEnum;

it('has all expected cases', function () {
    $cases = ContactTypeEnum::cases();

    expect($cases)->toContain(ContactTypeEnum::Phone)
        ->and($cases)->toContain(ContactTypeEnum::Email)
        ->and($cases)->toContain(ContactTypeEnum::Whatsapp)
        ->and($cases)->toContain(ContactTypeEnum::Telegram)
        ->and($cases)->toContain(ContactTypeEnum::Url)
        ->and($cases)->toContain(ContactTypeEnum::Other);
});

it('has correct values for each case', function () {
    expect(ContactTypeEnum::Phone->value)->toBe('phone')
        ->and(ContactTypeEnum::Email->value)->toBe('email')
        ->and(ContactTypeEnum::Whatsapp->value)->toBe('whatsapp')
        ->and(ContactTypeEnum::Telegram->value)->toBe('telegram')
        ->and(ContactTypeEnum::Url->value)->toBe('url')
        ->and(ContactTypeEnum::Other->value)->toBe('other');
});

it('can be instantiated from value', function () {
    expect(ContactTypeEnum::from('phone'))->toBe(ContactTypeEnum::Phone)
        ->and(ContactTypeEnum::from('email'))->toBe(ContactTypeEnum::Email)
        ->and(ContactTypeEnum::from('whatsapp'))->toBe(ContactTypeEnum::Whatsapp)
        ->and(ContactTypeEnum::from('telegram'))->toBe(ContactTypeEnum::Telegram)
        ->and(ContactTypeEnum::from('url'))->toBe(ContactTypeEnum::Url)
        ->and(ContactTypeEnum::from('other'))->toBe(ContactTypeEnum::Other);
});

it('can be instantiated with tryFrom', function () {
    /** @noinspection PhpCaseWithValueNotFoundInEnumInspection */
    expect(ContactTypeEnum::tryFrom('phone'))->toBe(ContactTypeEnum::Phone)
        ->and(ContactTypeEnum::tryFrom('email'))->toBe(ContactTypeEnum::Email)
        ->and(ContactTypeEnum::tryFrom('invalid'))->toBeNull();
});

it('throws exception for invalid value with from', function () {
    /** @noinspection PhpCaseWithValueNotFoundInEnumInspection */
    /** @noinspection PhpExpressionResultUnusedInspection */
    ContactTypeEnum::from('invalid');
})->throws(ValueError::class);

it('can be compared', function () {
    $phone1 = ContactTypeEnum::Phone;
    $phone2 = ContactTypeEnum::Phone;
    $email = ContactTypeEnum::Email;

    expect($phone1)->toBe($phone2)
        ->and($phone1)->not->toBe($email)
        ->and($phone1 === $phone2)->toBeTrue()
        ->and($phone1 === $email)->toBeFalse();
});

it('returns all values using HasValues trait', function () {
    $values = ContactTypeEnum::values();

    expect($values)->toBeArray()
        ->and($values)->toContain('phone')
        ->and($values)->toContain('email')
        ->and($values)->toContain('whatsapp')
        ->and($values)->toContain('telegram')
        ->and($values)->toContain('url')
        ->and($values)->toContain('other');
});

it('returns choices using HasChoices trait', function () {
    $choices = ContactTypeEnum::choices();

    expect($choices)
        ->toBeArray()
        ->and($choices)
        ->toHaveKeys(['phone', 'email', 'whatsapp', 'telegram', 'url', 'other']);
});

it('choices return case names as values', function () {
    $choices = ContactTypeEnum::choices();

    expect($choices['phone'])->toBe('Phone')
        ->and($choices['email'])->toBe('Email')
        ->and($choices['whatsapp'])->toBe('WhatsApp')
        ->and($choices['telegram'])->toBe('Telegram')
        ->and($choices['url'])->toBe('URL')
        ->and($choices['other'])->toBe('Other');
});

it('values are unique', function () {
    $values = ContactTypeEnum::values();
    $uniqueValues = array_unique($values);

    expect($values)->toHaveCount(count($uniqueValues));
});

it('case names follow PascalCase convention', function () {
    foreach (ContactTypeEnum::cases() as $case) {
        expect($case->name)->toMatch('/^[A-Z][a-zA-Z]*$/');
    }
});

it('values are lowercase', function () {
    foreach (ContactTypeEnum::cases() as $case) {
        expect($case->value)->toBe(strtolower($case->value));
    }
});

it('has messaging app cases', function () {
    expect(ContactTypeEnum::Whatsapp)->toBeInstanceOf(ContactTypeEnum::class)
        ->and(ContactTypeEnum::Telegram)->toBeInstanceOf(ContactTypeEnum::class)
        ->and(ContactTypeEnum::Whatsapp->value)->toBe('whatsapp')
        ->and(ContactTypeEnum::Telegram->value)->toBe('telegram');
});

it('has digital contact cases', function () {
    expect(ContactTypeEnum::Email)->toBeInstanceOf(ContactTypeEnum::class)
        ->and(ContactTypeEnum::Url)->toBeInstanceOf(ContactTypeEnum::class)
        ->and(ContactTypeEnum::Email->value)->toBe('email')
        ->and(ContactTypeEnum::Url->value)->toBe('url');
});

it('has other as fallback case', function () {
    expect(ContactTypeEnum::Other)->toBeInstanceOf(ContactTypeEnum::class)
        ->and(ContactTypeEnum::Other->value)->toBe('other');
});
