<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Jenishev\Laravel\Contacts\Enums\ContactTypeEnum;
use Workbench\App\Models\Customer;
use Workbench\App\Models\User;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->customer = Customer::create([
        'name' => 'Test Customer',
    ]);

    $this->user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $this->actingAs($this->user);
});

it('casts value based on contact type', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->value)->toBe('test@example.com');
});

it('uses appropriate strategy based on type', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
    ]);

    expect($contact->value)->toBe('0555 123 456');
});

it('resolves strategy from enum instance', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'TEST@EXAMPLE.COM',
    ]);

    expect($contact->value)->toBe('test@example.com');
});

it('resolves strategy from string value', function () {
    $contact = $this->customer->contacts()->create([
        'type' => 'email',
        'value' => 'TEST@EXAMPLE.COM',
    ]);

    expect($contact->value)->toBe('test@example.com');
});

it('transforms value on set', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'TEST@Example.COM',
    ]);

    expect($contact->getRawOriginal('value'))->toBe('test@example.com');
});

it('retrieves value correctly on get', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Telegram,
        'value' => 'username',
    ]);

    $contact->refresh();

    expect($contact->value)->toBe('@username');
});

it('uses email strategy for email type', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'User@Example.Com',
    ]);

    expect($contact->value)->toBe('user@example.com');
});

it('uses phone strategy for phone type', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '0555123456',
    ]);

    expect($contact->getRawOriginal('value'))->toBe('0555 123 456');
});

it('uses url strategy for website type', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Website,
        'value' => 'example.com',
    ]);

    expect($contact->value)->toBe('https://example.com');
});

it('uses whatsapp strategy for whatsapp type', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Whatsapp,
        'value' => '0555123456',
    ]);

    expect($contact->getRawOriginal('value'))->toBe('+996555123456');
});

it('uses telegram strategy for telegram type', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Telegram,
        'value' => 'TestUser',
    ]);

    expect($contact->getRawOriginal('value'))->toBe('testuser')
        ->and($contact->value)->toBe('@testuser');
});

it('uses null strategy for other type', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Other,
        'value' => 'some value',
    ]);

    expect($contact->value)->toBe('some value');
});
