<?php

namespace Jenishev\Laravel\Contacts\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasContacts
{
    public function contacts(): MorphMany
    {
        return $this->morphMany(config('contacts.model'), 'model');
    }
}
