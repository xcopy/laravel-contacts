<?php

namespace Jenishev\Laravel\Contacts\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Provides polymorphic contact relationship functionality to Eloquent models.
 * Allows any model to have multiple associated contacts through a morphMany relationship.
 */
trait HasContacts
{
    /**
     * Get all contacts for the model.
     */
    public function contacts(): MorphMany
    {
        return $this->morphMany(config('contacts.model'), 'model');
    }
}
