<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Contacts Table Name
    |--------------------------------------------------------------------------
    |
    | The database table name used for storing contact records.
    |
    */
    'table' => 'contacts',

    /*
    |--------------------------------------------------------------------------
    | Contact Model
    |--------------------------------------------------------------------------
    |
    | The fully qualified class name of the Contact model.
    |
    */
    'model' => Jenishev\Laravel\Contacts\Models\Contact::class,
    // 'model' => App\Models\Contact::class,

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The fully qualified class name of the User model that owns contacts.
    |
    */
    'user_model' => 'App\Models\User',
];
