<?php

use Jenishev\Laravel\Contacts\Enums\ContactTypes;
use Jenishev\Laravel\Contacts\Models\Contact;
use Workbench\App\Models\Customer;

beforeEach(function () {
    $this->customer = Customer::create(['name' => 'Test Customer']);
});

it('filters contacts by telegram type using local scope', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypes::Telegram,
        'value' => '@username1',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypes::Telegram,
        'value' => '@username2',
    ]);

    $telegramContacts = Contact::telegram()->get();

    expect($telegramContacts)->toHaveCount(2)
        ->and($telegramContacts->every(fn ($contact) => $contact->type === ContactTypes::Telegram))->toBeTrue();
});

it('returns empty collection when no telegram contacts exist', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypes::Phone,
        'value' => '+996555123456',
    ]);

    $telegramContacts = Contact::telegram()->get();

    expect($telegramContacts)->toHaveCount(0);
});
