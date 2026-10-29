<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Jenishev\Laravel\Contacts\Concerns\HasContacts;
use Jenishev\Laravel\Contacts\Contracts\Contactable;

class Customer extends Model implements Contactable
{
    use HasContacts;

    protected $fillable = ['name'];
}
