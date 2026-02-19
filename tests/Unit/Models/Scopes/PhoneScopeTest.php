<?php

use Jenishev\Laravel\Contacts\Enums\ContactTypeEnum;
use Jenishev\Laravel\Contacts\Models\Contact;
use Workbench\App\Models\Customer;

beforeEach(function () {
    $this->customer = Customer::create(['name' => 'Test Customer']);
});

it('filters contacts by phone type using local scope', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555654321',
    ]);

    $phoneContacts = Contact::phone()->get();

    expect($phoneContacts)->toHaveCount(2)
        ->and($phoneContacts->every(fn ($contact) => $contact->type === ContactTypeEnum::Phone))->toBeTrue();
});

it('returns empty collection when no phone contacts exist', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    $phoneContacts = Contact::phone()->get();

    expect($phoneContacts)->toHaveCount(0);
});

it('combines with other query constraints', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'is_primary' => true,
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555654321',
        'is_primary' => false,
    ]);

    $primaryPhoneContacts = Contact::phone()
        ->where('is_primary', true)
        ->get();

    expect($primaryPhoneContacts)->toHaveCount(1)
        ->and($primaryPhoneContacts->first()->is_primary)->toBeTrue();
});
