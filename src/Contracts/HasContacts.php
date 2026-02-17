<?php

namespace Jenishev\Laravel\Contacts\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface HasContacts
{
    public function contacts(): MorphMany;
}
