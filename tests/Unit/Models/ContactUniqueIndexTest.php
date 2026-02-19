<?php

use Illuminate\Database\QueryException;
use Jenishev\Laravel\Contacts\Enums\ContactTypeEnum;
use Workbench\App\Models\Customer;

beforeEach(function () {
    $this->customer = Customer::create(['name' => 'Test Customer']);
});

it('enforces unique constraint on model_type, model_id, type, and value', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
    ]);

    expect(fn () => $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
    ]))->toThrow(QueryException::class);
});

it('allows same value for different types', function () {
    $phone = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
    ]);

    $whatsapp = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Whatsapp,
        'value' => '+996555123456',
    ]);

    expect($phone->value)->toBe('0555 123 456')
        ->and($phone->type)->not->toBe($whatsapp->type)
        ->and($this->customer->contacts()->count())->toBe(2);
});

it('allows same value and type for different models', function () {
    $contact1 = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'shared@example.com',
    ]);

    $customer2 = Customer::create(['name' => 'Second Customer']);

    $contact2 = $customer2->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'shared@example.com',
    ]);

    expect($contact1->value)->toBe($contact2->value)
        ->and($contact1->type)->toBe($contact2->type)
        ->and($contact1->model_id)->not->toBe($contact2->model_id);
});

it('allows different values for same model and type', function () {
    $phone1 = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
    ]);

    $phone2 = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555654321',
    ]);

    expect($phone1->type)->toBe($phone2->type)
        ->and($phone1->value)->not->toBe($phone2->value)
        ->and($this->customer->contacts()->where('type', ContactTypeEnum::Phone)->count())->toBe(2);
});

it('prevents duplicate contact even with different is_primary values', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
        'is_primary' => true,
    ]);

    expect(fn () => $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
        'is_primary' => false,
    ]))->toThrow(QueryException::class);
});

it('prevents duplicate contact even with different is_verified values', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
        'is_verified' => true,
    ]);

    expect(fn () => $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
        'is_verified' => false,
    ]))->toThrow(QueryException::class);
});

it('prevents duplicate contact even with different country_code values', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'country_code' => 'KG',
    ]);

    expect(fn () => $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'country_code' => 'US',
    ]))->toThrow(QueryException::class);
});

it('allows updating to same value when it is the same record', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    $contact->update(['is_verified' => true]);

    expect($contact->is_verified)->toBeTrue()
        ->and($contact->value)->toBe('test@example.com');
});

it('enforces unique constraint when updating value to existing value', function () {
    $contact1 = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'first@example.com',
    ]);

    $contact2 = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'second@example.com',
    ]);

    expect(fn () => $contact2->update(['value' => 'first@example.com']))
        ->toThrow(QueryException::class);
});

it('allows updating value to a non-existing value', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'old@example.com',
    ]);

    $contact->update(['value' => 'new@example.com']);

    expect($contact->value)->toBe('new@example.com');
});

it('enforces unique constraint across multiple fields combination', function () {
    $customer2 = Customer::create(['name' => 'Second Customer']);

    // Same value, same type, different model - should work
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
    ]);

    $customer2->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
    ]);

    // Same value, different type, same model - should work
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Whatsapp,
        'value' => '+996555123456',
    ]);

    // Same value, same type, same model - should fail
    expect(fn () => $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
    ]))->toThrow(QueryException::class);

    expect($this->customer->contacts()->count())->toBe(2)
        ->and($customer2->contacts()->count())->toBe(1);
});
