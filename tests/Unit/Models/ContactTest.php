<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Jenishev\Laravel\Contacts\Enums\ContactTypeEnum;
use Jenishev\Laravel\Contacts\Models\Contact;
use Workbench\App\Models\Customer;
use Workbench\App\Models\User;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->customer = Customer::create([
        'name' => 'Test Customer',
    ]);

    $this->user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',

    ]);

    $this->actingAs($this->user);
});

it('can create a contact via relationship', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
        'is_primary' => true,
        'is_verified' => false,
    ]);

    expect($contact)->toBeInstanceOf(Contact::class)
        ->and($contact->model_type)->toBe(Customer::class)
        ->and($contact->model_id)->toBe($this->customer->id)
        ->and($contact->type)->toBe(ContactTypeEnum::Email)
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
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->model_type)->toBe(Customer::class);
});

it('casts model_id as integer', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->model_id)->toBeInt();
});

it('casts type as ContactTypeEnum', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->type)->toBeInstanceOf(ContactTypeEnum::class);
});

it('casts value as string', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => 1234567890,
    ]);
})->throws(InvalidArgumentException::class);

it('casts is_primary as boolean', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
        'is_primary' => 1,
    ]);

    expect($contact->is_primary)->toBeBool();
});

it('casts is_verified as boolean', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
        'is_verified' => 0,
    ]);

    expect($contact->is_verified)->toBeBool();
});

it('has a morphTo relationship with model', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->model)->toBeInstanceOf(Customer::class)
        ->and($contact->model->id)->toBe($this->customer->id)
        ->and($contact->model->name)->toBe($this->customer->name);
});

it('can eager load model relationship', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    $contact = Contact::with('model')->first();

    expect($contact->relationLoaded('model'))->toBeTrue()
        ->and($contact->model)->toBeInstanceOf(Customer::class)
        ->and($contact->model->id)->toBe($this->customer->id);
});

it('can query contacts by model relationship', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    $anotherCustomer = Customer::create(['name' => 'Another Customer']);
    $anotherCustomer->contacts()->create([
        'type' => ContactTypeEnum::Email,
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
        'type' => ContactTypeEnum::Email,
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
        'type' => ContactTypeEnum::Email,
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
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
        'is_primary' => true,
        'is_verified' => true,
    ]);

    $this->assertDatabaseHas(config('contacts.table'), [
        'id' => $contact->id,
        'model_type' => Customer::class,
        'model_id' => $this->customer->id,
        'type' => ContactTypeEnum::Email->value,
        'value' => 'test@example.com',
        'is_primary' => true,
        'is_verified' => true,
    ]);
});

it('can create multiple contacts for same model', function () {
    $email = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
        'is_primary' => true,
    ]);

    $phone = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'is_primary' => false,
    ]);

    expect($this->customer->contacts()->count())->toBe(2)
        ->and($email->type)->toBe(ContactTypeEnum::Email)
        ->and($phone->type)->toBe(ContactTypeEnum::Phone);
});

it('can update contact value', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'old@example.com',
    ]);

    $contact->update(['value' => 'new@example.com']);

    expect($contact->fresh()->value)->toBe('new@example.com');
});

it('can delete contact', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    $contactId = $contact->id;
    $contact->delete();

    expect(Contact::find($contactId))->toBeNull();
});

it('defaults is_primary to false when not provided', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->is_primary)->toBeFalsy();
});

it('defaults is_verified to false when not provided', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->is_verified)->toBeFalsy();
});

it('can access contacts via relationship', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'email@example.com',
    ]);

    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '0555123456',
    ]);

    expect($this->customer->contacts)->toHaveCount(2)
        ->and($this->customer->contacts->first())->toBeInstanceOf(Contact::class);
});

it('automatically sets created_by when creating contact', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->created_by)->toBe($this->user->id)
        ->and($contact->updated_by)->toBe($this->user->id);
});

it('automatically sets updated_by when updating contact', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    $anotherUser = User::create([
        'name' => 'Another User',
        'email' => 'another@example.com',

    ]);

    $this->actingAs($anotherUser);

    $contact->update(['value' => 'updated@example.com']);

    expect($contact->created_by)->toBe($this->user->id)
        ->and($contact->updated_by)->toBe($anotherUser->id);
});

it('has creator relationship', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->creator)->toBeInstanceOf(User::class)
        ->and($contact->creator->id)->toBe($this->user->id)
        ->and($contact->creator->name)->toBe($this->user->name);
});

it('has updater relationship', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->updater)->toBeInstanceOf(User::class)
        ->and($contact->updater->id)->toBe($this->user->id)
        ->and($contact->updater->name)->toBe($this->user->name);
});

it('can eager load creator and updater relationships', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    $contact = Contact::with(['creator', 'updater'])->first();

    expect($contact->relationLoaded('creator'))->toBeTrue()
        ->and($contact->relationLoaded('updater'))->toBeTrue()
        ->and($contact->creator)->toBeInstanceOf(User::class)
        ->and($contact->updater)->toBeInstanceOf(User::class);
});

it('tracks different users for create and update', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    $updater = User::create([
        'name' => 'Updater User',
        'email' => 'updater@example.com',

    ]);

    $this->actingAs($updater);
    $contact->update(['is_verified' => true]);
    $contact->refresh();

    expect($contact->created_by)->toBe($this->user->id)
        ->and($contact->updated_by)->toBe($updater->id)
        ->and($contact->creator->name)->toBe('Test User')
        ->and($contact->updater->name)->toBe('Updater User');
});

it('stores created_by and updated_by in database', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    $this->assertDatabaseHas(config('contacts.table'), [
        'id' => $contact->id,
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);
});

it('can query contacts by created_by', function () {
    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test1@example.com',
    ]);

    $anotherUser = User::create([
        'name' => 'Another User',
        'email' => 'another@example.com',
    ]);

    $this->actingAs($anotherUser);

    $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test2@example.com',
    ]);

    $contactsByFirstUser = Contact::where('created_by', $this->user->id)->get();
    $contactsBySecondUser = Contact::where('created_by', $anotherUser->id)->get();

    expect($contactsByFirstUser)->toHaveCount(1)
        ->and($contactsBySecondUser)->toHaveCount(1)
        ->and($contactsByFirstUser->first()->value)->toBe('test1@example.com')
        ->and($contactsBySecondUser->first()->value)->toBe('test2@example.com');
});

it('casts created_by as integer', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->created_by)->toBeInt();
});

it('casts updated_by as integer', function () {
    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
    ]);

    expect($contact->updated_by)->toBeInt();
});
