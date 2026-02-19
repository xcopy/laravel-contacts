<?php

use Jenishev\Laravel\Contacts\Enums\ContactTypeEnum;
use Jenishev\Laravel\Contacts\Models\Contact;
use Workbench\App\Models\Customer;

beforeEach(function () {
    $this->customer = Customer::create(['name' => 'Test Customer']);
});

it('filters contacts by url type using local scope', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Url,
        'value' => 'https://example.com',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Url,
        'value' => 'https://example.org',
    ]);

    $urlContacts = Contact::url()->get();

    expect($urlContacts)->toHaveCount(2)
        ->and($urlContacts->every(fn ($contact) => $contact->type === ContactTypeEnum::Url))->toBeTrue();
});

it('returns empty collection when no url contacts exist', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
    ]);

    $urlContacts = Contact::url()->get();

    expect($urlContacts)->toHaveCount(0);
});
