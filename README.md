# Laravel Contacts

[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/xcopy/laravel-contacts/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/xcopy/laravel-contacts/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/xcopy/laravel-contacts/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/xcopy/laravel-contacts/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/xcopy/laravel-contacts.svg?style=flat-square)](https://packagist.org/packages/xcopy/laravel-contacts)

A simple Laravel package for managing polymorphic contact information (phone, email, mobile, WhatsApp, Telegram, website, etc.) for any Eloquent model.
Perfect for multi-tenant SaaS applications, CRMs, or property management systems where multiple entities need contact details.

## Features

- **Polymorphic relationships**: Attach contacts to any Eloquent model
- **Multiple contact types**: Phone, Email, Mobile, WhatsApp, Telegram, Website, and Other
- **Primary & verified flags**: Mark contacts as primary or verified
- **Unique constraints**: Prevents duplicate contacts per model
- **Type-safe**: Uses PHP 8.1+ enums and strict typing

## Installation

**Note:** This package is not yet available on Packagist. You must add it to your `composer.json` manually.

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/xcopy/laravel-contacts"
        }
    ],
    "require": {
        "xcopy/laravel-contacts": "dev-main"
    }
}
```

Run the installation command (publishes config and migrations):

```bash
php artisan contacts:install
php artisan migrate
```

Or publish manually:

```bash
php artisan vendor:publish --tag="contacts-migrations"
php artisan vendor:publish --tag="contacts-config"
php artisan migrate
```

## Usage

### 1. Add the trait to your model

Add the `HasContacts` trait to any model that needs contact information:

```php
use Illuminate\Database\Eloquent\Model;
use Jenishev\Laravel\Contacts\Concerns\HasContacts;

class Company extends Model
{
    use HasContacts;
    
    // ... your model code
}
```

### 2. Create contacts

```php
use Jenishev\Laravel\Contacts\Enums\ContactTypeEnum;

$company = Company::find(1);

// Create a primary email contact
$company->contacts()->create([
    'type' => ContactTypeEnum::Email,
    'value' => 'info@company.com',
    'is_primary' => true,
    'is_verified' => true,
]);

// Create a phone contact
$company->contacts()->create([
    'type' => ContactTypeEnum::Phone,
    'value' => '+1234567890',
    'is_primary' => false,
]);

// Create a WhatsApp contact
$company->contacts()->create([
    'type' => ContactTypeEnum::Whatsapp,
    'value' => '+1234567890',
]);
```

### 3. Retrieve contacts

```php
// Get all contacts
$contacts = $company->contacts;

// Get contacts of a specific type
$emails = $company->contacts()->where('type', ContactTypeEnum::Email)->get();

// Get primary contact
$primaryContact = $company->contacts()->where('is_primary', true)->first();

// Get verified contacts
$verified = $company->contacts()->where('is_verified', true)->get();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
