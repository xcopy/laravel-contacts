<?php

use Jenishev\Laravel\Contacts\Enums\ContactTypeEnum;
use Jenishev\Laravel\Contacts\Models\Contact;
use Workbench\App\Models\Customer;

beforeEach(function () {
    $this->customer = Customer::create(['name' => 'Test Customer']);
});

it('filters contacts by email type using local scope', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test1@example.com',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test2@example.com',
    ]);

    $emailContacts = Contact::email()->get();

    expect($emailContacts)->toHaveCount(2)
        ->and($emailContacts->every(fn ($contact) => $contact->type === ContactTypeEnum::Email))->toBeTrue();
});

it('returns empty collection when no email contacts exist', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
    ]);

    $emailContacts = Contact::email()->get();

    expect($emailContacts)->toHaveCount(0);
});

it('combines with other query constraints', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'primary@example.com',
        'is_primary' => true,
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'secondary@example.com',
        'is_primary' => false,
    ]);

    $primaryEmailContacts = Contact::email()
        ->where('is_primary', true)
        ->get();

    expect($primaryEmailContacts)->toHaveCount(1)
        ->and($primaryEmailContacts->first()->is_primary)->toBeTrue();
});
