<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Jenishev\Laravel\Contacts\Enums\ContactTypes;
use Jenishev\Laravel\Contacts\Models\Contact;
use Workbench\App\Models\Customer;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->customer = Customer::create([
        'name' => 'Test Customer',
    ]);
});

it('can create a contact via relationship', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
        'is_primary' => true,
        'is_verified' => false,
    ]);

    expect($contact)->toBeInstanceOf(Contact::class)
        ->and($contact->model_type)->toBe(Customer::class)
        ->and($contact->model_id)->toBe($this->customer->id)
        ->and($contact->type)->toBe(ContactTypes::Email)
        ->and($contact->value)->toBe('test@example.com')
        ->and($contact->is_primary)->toBeTrue()
        ->and($contact->is_verified)->toBeFalse();
});

it('uses the table name from config', function () {
    $contact = new Contact;

    expect($contact->getTable())->toBe(config('contacts.table'));
});

it('casts model_type as model class', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->model_type)->toBe(Customer::class);
});

it('casts model_id as integer', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->model_id)->toBeInt();
});

it('casts type as ContactTypes', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->type)->toBeInstanceOf(ContactTypes::class);
});

it('casts value as string', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypes::Phone,
        'value' => 1234567890,
    ]);
})->throws(InvalidArgumentException::class);

it('casts is_primary as boolean', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
        'is_primary' => 1,
    ]);

    expect($contact->is_primary)->toBeBool();
});

it('casts is_verified as boolean', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
        'is_verified' => 0,
    ]);

    expect($contact->is_verified)->toBeBool();
});

it('has a morphTo relationship with model', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->model)->toBeInstanceOf(Customer::class)
        ->and($contact->model->id)->toBe($this->customer->id)
        ->and($contact->model->name)->toBe($this->customer->name);
});

it('can eager load model relationship', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
    ]);

    $contact = Contact::with('model')->first();

    expect($contact->relationLoaded('model'))->toBeTrue()
        ->and($contact->model)->toBeInstanceOf(Customer::class)
        ->and($contact->model->id)->toBe($this->customer->id);
});

it('can query contacts by model relationship', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
    ]);

    $anotherCustomer = Customer::create(['name' => 'Another Customer']);
    $anotherCustomer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'another@example.com',
    ]);

    $contacts = Contact::whereHasMorph('model', [Customer::class], function ($query) {
        $query->where('name', 'Test Customer');
    })->get();

    expect($contacts)->toHaveCount(1)
        ->and($contacts->first()->value)->toBe('test@example.com');
});

it('model relationship returns null for deleted parent', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
    ]);

    $customerId = $this->customer->id;
    $this->customer->delete();

    $contact->refresh();

    expect($contact->model)->toBeNull()
        ->and($contact->model_id)->toBe($customerId);
});

it('can access model relationship multiple times without extra queries', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
    ]);

    $contact->load('model');

    $model1 = $contact->model;
    $model2 = $contact->model;

    expect($model1)->toBe($model2)
        ->and($model1->id)->toBe($this->customer->id);
});

it('stores contact in database', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
        'is_primary' => true,
        'is_verified' => true,
    ]);

    $this->assertDatabaseHas(config('contacts.table'), [
        'id' => $contact->id,
        'model_type' => Customer::class,
        'model_id' => $this->customer->id,
        'type' => ContactTypes::Email->value,
        'value' => 'test@example.com',
        'is_primary' => true,
        'is_verified' => true,
    ]);
});

it('can create multiple contacts for same model', function () {
    $email = $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
        'is_primary' => true,
    ]);

    $phone = $this->customer->contacts()->create([
        'type' => ContactTypes::Phone,
        'value' => '+996555123456',
        'is_primary' => false,
    ]);

    expect($this->customer->contacts()->count())->toBe(2)
        ->and($email->type)->toBe(ContactTypes::Email)
        ->and($phone->type)->toBe(ContactTypes::Phone);
});

it('can update contact value', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'old@example.com',
    ]);

    $contact->update(['value' => 'new@example.com']);

    expect($contact->fresh()->value)->toBe('new@example.com');
});

it('can delete contact', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
    ]);

    $contactId = $contact->id;
    $contact->delete();

    expect(Contact::find($contactId))->toBeNull();
});

it('defaults is_primary to false when not provided', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->is_primary)->toBeFalsy();
});

it('defaults is_verified to false when not provided', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->is_verified)->toBeFalsy();
});

it('can access contacts via relationship', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypes::Email,
        'value' => 'email@example.com',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypes::Phone,
        'value' => '0555123456',
    ]);

    expect($this->customer->contacts)->toHaveCount(2)
        ->and($this->customer->contacts->first())->toBeInstanceOf(Contact::class);
});
