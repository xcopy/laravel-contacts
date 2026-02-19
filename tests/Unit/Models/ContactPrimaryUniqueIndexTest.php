<?php

use Illuminate\Support\Facades\DB;
use Jenishev\Laravel\Contacts\Enums\ContactTypeEnum;
use Workbench\App\Models\Customer;

beforeEach(function () {
    $this->customer = Customer::create(['name' => 'Test Customer']);
});

it('enforces unique primary contact per model and type on PostgreSQL and SQLite', function () {
    $driver = DB::getDriverName();

    if (! in_array($driver, ['pgsql', 'sqlite'])) {
        $this->markTestSkipped("This test only runs on PostgreSQL and SQLite. Current driver: {$driver}");
    }

    $contact1 = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'is_primary' => true,
    ]);

    // Creating another primary contact should reset the first one's is_primary
    $contact2 = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555654321',
        'is_primary' => true,
    ]);

    $contact1->refresh();

    expect($contact2->is_primary)->toBeTrue()
        ->and($contact1->is_primary)->toBeFalse();
})->skip(fn () => ! in_array(DB::getDriverName(), ['pgsql', 'sqlite']));

it('allows multiple non-primary contacts of same type on PostgreSQL and SQLite', function () {
    $driver = DB::getDriverName();

    if (! in_array($driver, ['pgsql', 'sqlite'])) {
        $this->markTestSkipped("This test only runs on PostgreSQL and SQLite. Current driver: {$driver}");
    }

    $contact1 = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'is_primary' => false,
    ]);

    $contact2 = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555654321',
        'is_primary' => false,
    ]);

    expect($this->customer->contacts()->where('type', ContactTypeEnum::Phone)->count())->toBe(2)
        ->and($contact1->is_primary)->toBeFalse()
        ->and($contact2->is_primary)->toBeFalse();
})->skip(fn () => ! in_array(DB::getDriverName(), ['pgsql', 'sqlite']));

it('allows primary contacts of different types for same model on PostgreSQL and SQLite', function () {
    $driver = DB::getDriverName();

    if (! in_array($driver, ['pgsql', 'sqlite'])) {
        $this->markTestSkipped("This test only runs on PostgreSQL and SQLite. Current driver: {$driver}");
    }

    $phoneContact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'is_primary' => true,
    ]);

    $emailContact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Email,
        'value' => 'test@example.com',
        'is_primary' => true,
    ]);

    expect($phoneContact->is_primary)->toBeTrue()
        ->and($emailContact->is_primary)->toBeTrue();
})->skip(fn () => ! in_array(DB::getDriverName(), ['pgsql', 'sqlite']));

it('allows primary contacts of same type for different models on PostgreSQL and SQLite', function () {
    $driver = DB::getDriverName();

    if (! in_array($driver, ['pgsql', 'sqlite'])) {
        $this->markTestSkipped("This test only runs on PostgreSQL and SQLite. Current driver: {$driver}");
    }

    $customer2 = Customer::create(['name' => 'Second Customer']);

    $contact1 = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'is_primary' => true,
    ]);

    $contact2 = $customer2->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555654321',
        'is_primary' => true,
    ]);

    expect($contact1->is_primary)->toBeTrue()
        ->and($contact2->is_primary)->toBeTrue();
})->skip(fn () => ! in_array(DB::getDriverName(), ['pgsql', 'sqlite']));

it('allows updating primary contact to non-primary on PostgreSQL and SQLite', function () {
    $driver = DB::getDriverName();

    if (! in_array($driver, ['pgsql', 'sqlite'])) {
        $this->markTestSkipped("This test only runs on PostgreSQL and SQLite. Current driver: {$driver}");
    }

    $contact = $this->customer->contacts()->create([
        'type' => ContactTypeEnum::Phone,
        'value' => '+996555123456',
        'is_primary' => true,
    ]);

    $contact->update(['is_primary' => false]);

    expect($contact->is_primary)->toBeFalse();
})->skip(fn () => ! in_array(DB::getDriverName(), ['pgsql', 'sqlite']));
