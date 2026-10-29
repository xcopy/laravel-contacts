<?php

use Jenishev\Laravel\Contacts\Enums\ContactTypes;
use Jenishev\Laravel\Contacts\Models\Contact;
use Workbench\App\Models\Customer;

beforeEach(function () {
    $this->customer = Customer::create(['name' => 'Test Customer']);
});

it('filters contacts by other type using local scope', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypes::Other,
        'value' => 'Custom value 1',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypes::Other,
        'value' => 'Custom value 2',
    ]);

    $otherContacts = Contact::other()->get();

    expect($otherContacts)->toHaveCount(2)
        ->and($otherContacts->every(fn ($contact) => $contact->type === ContactTypes::Other))->toBeTrue();
});

it('returns empty collection when no other contacts exist', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypes::Phone,
        'value' => '+996555123456',
    ]);

    $otherContacts = Contact::other()->get();

    expect($otherContacts)->toHaveCount(0);
});
