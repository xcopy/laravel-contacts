<?php

namespace Jenishev\Laravel\Contacts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Jenishev\Laravel\Contacts\Casts\AsValue;
use Jenishev\Laravel\Contacts\Contracts\Contactable;
use Jenishev\Laravel\Contacts\Enums\ContactTypes;
use Jenishev\Laravel\Support\Eloquent\Casts\AsModelClass;
use RichanFongdasen\EloquentBlameable\BlameableTrait;

/**
 * Contact model for managing contact information.
 *
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property ContactTypes $type
 * @property mixed $value
 * @property string|null $country_code
 * @property bool $is_primary
 * @property bool $is_verified
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class Contact extends Model
{
    use BlameableTrait;

    /**
     * {@inheritDoc}
     */
    protected $attributes = [
        'is_primary' => false,
        'is_verified' => false,
    ];

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
    protected static function booted(): void
    {
        static::creating(function (Contact $contact) {
            if ($contact->is_primary) {
                static::resetOtherPrimary($contact);
            }
        });

        static::updating(function (Contact $contact) {
            if ($contact->is_primary && $contact->isDirty('is_primary')) {
                static::resetOtherPrimary($contact);
            }
        });
    }

    /**
     * Reset the "is_primary" flag for other contacts of the same type.
     *
     * @param  Contact  $contact  The contact being created or updated
     */
    protected static function resetOtherPrimary(Contact $contact): void
    {
        static::query()
            ->where([
                ['model_type', '=', $contact->model_type],
                ['model_id', '=', $contact->model_id],
                ['type', '=', $contact->type],
                ['id', '!=', $contact->getKey() ?: 0],
                ['is_primary', '=', true],
            ])
            ->update(['is_primary' => false]);
    }

    /**
     * {@inheritDoc}
     */
    protected function casts(): array
    {
        return [
            'model_type' => AsModelClass::of(Contactable::class),
            'model_id' => 'integer',
            'type' => ContactTypes::class,
            'value' => AsValue::class,
            'country_code' => 'string',
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

    /**
     * Scope a query to only include phone contacts.
     */
    public function scopePhone($query)
    {
        return $query->where('type', ContactTypes::Phone);
    }

    /**
     * Scope a query to only include email contacts.
     */
    public function scopeEmail($query)
    {
        return $query->where('type', ContactTypes::Email);
    }

    /**
     * Scope a query to only include WhatsApp contacts.
     */
    public function scopeWhatsapp($query)
    {
        return $query->where('type', ContactTypes::Whatsapp);
    }

    /**
     * Scope a query to only include Telegram contacts.
     */
    public function scopeTelegram($query)
    {
        return $query->where('type', ContactTypes::Telegram);
    }

    /**
     * Scope a query to only include URL contacts.
     */
    public function scopeUrl($query)
    {
        return $query->where('type', ContactTypes::Url);
    }

    /**
     * Scope a query to only include other type contacts.
     */
    public function scopeOther($query)
    {
        return $query->where('type', ContactTypes::Other);
    }
}
