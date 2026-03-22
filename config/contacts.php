<?php

use Jenishev\Laravel\Contacts\Models\Contact;
use libphonenumber\PhoneNumberFormat;

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
    'model' => Contact::class,
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

    /*
    |--------------------------------------------------------------------------
    | Default Country Code
    |--------------------------------------------------------------------------
    |
    | The default country code used for phone number validation and formatting.
    | Can be overridden via the CONTACTS_DEFAULT_COUNTRY_CODE environment variable.
    |
    */
    'default_country_code' => env('CONTACTS_DEFAULT_COUNTRY_CODE', 'KG'),

    /*
    |--------------------------------------------------------------------------
    | Phone Number Format (Storage)
    |--------------------------------------------------------------------------
    |
    | The format used when storing phone numbers in the database.
    | Available formats: E164, INTERNATIONAL, NATIONAL, RFC3966
    | See: \libphonenumber\PhoneNumberFormat enum
    |
    */
    'phone_format_set' => env('CONTACTS_PHONE_FORMAT_SET', PhoneNumberFormat::NATIONAL->name),

    /*
    |--------------------------------------------------------------------------
    | Phone Number Format (Retrieval)
    |--------------------------------------------------------------------------
    |
    | The format used when retrieving phone numbers from the database.
    | Available formats: E164, INTERNATIONAL, NATIONAL, RFC3966
    | See: \libphonenumber\PhoneNumberFormat enum
    |
    */
    'phone_format_get' => env('CONTACTS_PHONE_FORMAT_GET', PhoneNumberFormat::NATIONAL->name),
];
