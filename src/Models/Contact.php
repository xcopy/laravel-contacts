<?php

namespace Jenishev\Laravel\Contacts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Jenishev\Laravel\Contacts\Contracts\HasContacts;
use Jenishev\Laravel\Contacts\Enums\ContactTypeEnum;
use Jenishev\Laravel\Support\Eloquent\Casts\AsModelClass;
use RichanFongdasen\EloquentBlameable\BlameableTrait;

/**
 * Contact model for managing contact information.
 */
class Contact extends Model
{
    use BlameableTrait;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('contacts.table');

        $this->guarded = [$this->primaryKey];
    }

    /**
     * {@inheritDoc}
     */
    protected function casts(): array
    {
        return [
            'model_type' => AsModelClass::of(HasContacts::class),
            'model_id' => 'integer',
            'type' => ContactTypeEnum::class,
            'value' => 'string',
            'is_primary' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    /**
     * Get the parent model that owns the contact.
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * {@inheritDoc}
     */
    public function blameable(): array
    {
        return [
            'user' => config('contacts.user_model'),
            'createdBy' => 'created_by',
            'updatedBy' => 'updated_by',
        ];
    }
}
