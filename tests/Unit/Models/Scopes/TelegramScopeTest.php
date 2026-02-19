<?php

use Jenishev\Laravel\Contacts\Enums\ContactTypeEnum;
use Jenishev\Laravel\Contacts\Models\Contact;
use Workbench\App\Models\Customer;

beforeEach(function () {
    $this->customer = Customer::create(['name' => 'Test Customer']);
});

it('filters contacts by telegram type using local scope', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Telegram,
        'value' => '@username1',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Telegram,
        'value' => '@username2',
    ]);

    $telegramContacts = Contact::telegram()->get();

    expect($telegramContacts)->toHaveCount(2)
        ->and($telegramContacts->every(fn ($contact) => $contact->type === ContactTypeEnum::Telegram))->toBeTrue();
});

it('returns empty collection when no telegram contacts exist', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
    ]);

    $telegramContacts = Contact::telegram()->get();

    expect($telegramContacts)->toHaveCount(0);
});
