<?php

use Jenishev\Laravel\Contacts\Enums\ContactTypeEnum;
use Jenishev\Laravel\Contacts\Models\Contact;
use Workbench\App\Models\Customer;

beforeEach(function () {
    $this->customer = Customer::create(['name' => 'Test Customer']);
});

it('filters contacts by website type using local scope', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Website,
        'value' => 'https://example.com',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Website,
        'value' => 'https://example.org',
    ]);

    $websiteContacts = Contact::website()->get();

    expect($websiteContacts)->toHaveCount(2)
        ->and($websiteContacts->every(fn ($contact) => $contact->type === ContactTypeEnum::Website))->toBeTrue();
});

it('returns empty collection when no website contacts exist', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
    ]);

    $websiteContacts = Contact::website()->get();

    expect($websiteContacts)->toHaveCount(0);
});
