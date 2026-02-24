<?php

namespace Jenishev\Laravel\Contacts\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * Interface for models that can have contact information.
 *
 * Implement this interface on any model that should support polymorphic
 * contact records (phone, email, address, etc.).
 *
 * @mixin Model
 *
 * @property-read Collection $contacts
 */
interface HasContacts
{
    /**
     * Get all contacts for the model.
     */
    public function contacts(): MorphMany;
}
