<?php

use Illuminate\Support\Facades\DB;
use Jenishev\Laravel\Contacts\Enums\ContactTypeEnum;
use Workbench\App\Models\Customer;

beforeEach(function () {
    $this->customer = Customer::create(['name' => 'Test Customer']);
});

it('creates a contact with is_primary set to true', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'is_primary' => true,
    ]);

    expect($contact->is_primary)->toBeTrue()
        ->and($contact->type)->toBe(ContactTypeEnum::Phone);
});

it('creates a contact with is_primary set to false by default', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->is_primary)->toBeFalse();
});

it('resets other primary contacts when creating a new primary contact', function () {
    $firstPhone = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'is_primary' => true,
    ]);

    expect($firstPhone->is_primary)->toBeTrue();

    $secondPhone = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555654321',
        'is_primary' => true,
    ]);

    expect($secondPhone->is_primary)->toBeTrue();

    $firstPhone->refresh();
    expect($firstPhone->is_primary)->toBeFalse();
});

it('does not affect primary contacts of different types when creating a new primary contact', function () {
    $primaryPhone = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'is_primary' => true,
    ]);

    $primaryEmail = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
        'is_primary' => true,
    ]);

    $primaryPhone->refresh();

    expect($primaryPhone->is_primary)->toBeTrue()
        ->and($primaryEmail->is_primary)->toBeTrue();
});

it('does not affect primary contacts of different models when creating a new primary contact', function () {
    $customer2 = Customer::create(['name' => 'Second Customer']);

    $customer1Phone = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'is_primary' => true,
    ]);

    $customer2Phone = $customer2->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555654321',
        'is_primary' => true,
    ]);

    $customer1Phone->refresh();

    expect($customer1Phone->is_primary)->toBeTrue()
        ->and($customer2Phone->is_primary)->toBeTrue();
});

it('updates a contact value', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'old@example.com',
    ]);

    $contact->update(['value' => 'new@example.com']);

    expect($contact->value)->toBe('new@example.com');
});

it('resets other primary contacts when updating is_primary to true', function () {
    $firstPhone = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'is_primary' => true,
    ]);

    $secondPhone = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555654321',
        'is_primary' => false,
    ]);

    $secondPhone->update(['is_primary' => true]);

    $firstPhone->refresh();

    expect($secondPhone->is_primary)->toBeTrue()
        ->and($firstPhone->is_primary)->toBeFalse();
});

it('does not reset other primary contacts when updating non-primary field', function () {
    $firstPhone = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'is_primary' => true,
    ]);

    $secondPhone = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555654321',
        'is_primary' => false,
    ]);

    $secondPhone->update(['is_verified' => true]);

    $firstPhone->refresh();

    expect($firstPhone->is_primary)->toBeTrue()
        ->and($secondPhone->is_primary)->toBeFalse()
        ->and($secondPhone->is_verified)->toBeTrue();
});

it('does not reset other primary contacts when updating is_primary from true to true', function () {
    $primaryPhone = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'is_primary' => true,
    ]);

    $secondPhone = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555654321',
        'is_primary' => false,
    ]);

    DB::enableQueryLog();

    // Updating is_primary when it's already true shouldn't trigger any queries
    $primaryPhone->update(['is_primary' => true]);

    $queries = DB::getQueryLog();

    // Should have no queries because is_primary didn't change
    expect($queries)->toHaveCount(0);

    // Verify the second phone is still not primary
    $secondPhone->refresh();
    expect($secondPhone->is_primary)->toBeFalse();
});

it('does not reset other primary contacts when is_primary stays true but other fields change', function () {
    $primaryPhone = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'is_primary' => true,
        'is_verified' => false,
    ]);

    $secondPhone = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555654321',
        'is_primary' => false,
    ]);

    DB::flushQueryLog();
    DB::enableQueryLog();

    // Update another field while keeping is_primary true
    $primaryPhone->update([
        'is_primary' => true,
        'is_verified' => true,
    ]);

    $queries = DB::getQueryLog();

    // Should have only the update query for the contact, not the reset query
    // because is_primary didn't change (was true, still true)
    expect($queries)->toHaveCount(1);

    // Verify the second phone is still not primary
    $secondPhone->refresh();
    expect($secondPhone->is_primary)->toBeFalse()
        ->and($primaryPhone->is_verified)->toBeTrue();
});

it('allows updating is_primary from true to false', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'is_primary' => true,
    ]);

    $contact->update(['is_primary' => false]);

    expect($contact->is_primary)->toBeFalse();
});

it('creates multiple contacts of different types for the same model', function () {
    $phone = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
    ]);

    $email = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    $whatsapp = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Whatsapp,
        'value' => '+996555654321',
    ]);

    expect($this->customer->contacts()->count())->toBe(3)
        ->and($phone->type)->toBe(ContactTypeEnum::Phone)
        ->and($email->type)->toBe(ContactTypeEnum::Email)
        ->and($whatsapp->type)->toBe(ContactTypeEnum::Whatsapp);
});

it('creates contacts with country_code', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'country_code' => 'KG',
    ]);

    expect($contact->country_code)->toBe('KG');
});

it('updates country_code', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'country_code' => 'KG',
    ]);

    $contact->update(['country_code' => 'US']);

    expect($contact->country_code)->toBe('US');
});
