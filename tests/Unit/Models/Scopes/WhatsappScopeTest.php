<?php

use Jenishev\Laravel\Contacts\Enums\ContactTypeEnum;
use Jenishev\Laravel\Contacts\Models\Contact;
use Workbench\App\Models\Customer;

beforeEach(function () {
    $this->customer = Customer::create(['name' => 'Test Customer']);
});

it('filters contacts by whatsapp type using local scope', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Whatsapp,
        'value' => '+996555123456',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555654321',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Whatsapp,
        'value' => '+996555111222',
    ]);

    $whatsappContacts = Contact::whatsapp()->get();

    expect($whatsappContacts)->toHaveCount(2)
        ->and($whatsappContacts->every(fn ($contact) => $contact->type === ContactTypeEnum::Whatsapp))->toBeTrue();
});

it('returns empty collection when no whatsapp contacts exist', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
    ]);

    $whatsappContacts = Contact::whatsapp()->get();

    expect($whatsappContacts)->toHaveCount(0);
});
