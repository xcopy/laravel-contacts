<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Jenishev\Laravel\Contacts\Concerns\HasContacts as HasContactsConcern;
use Jenishev\Laravel\Contacts\Contracts\HasContacts as HasContactsContract;

class Customer extends Model implements HasContactsContract
{
    use HasContactsConcern;

    protected $fillable = ['name'];
}
