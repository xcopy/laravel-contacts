<?php

namespace Jenishev\Laravel\Contacts\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Interface for models that can have contact information.
 *
 * Implement this interface on any model that should support polymorphic
 * contact records (phone, email, address, etc.).
 */
interface HasContacts
{
    /**
     * Get all contacts for the model.
     */
    public function contacts(): MorphMany;
}
