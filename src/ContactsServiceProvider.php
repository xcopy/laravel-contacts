<?php

namespace Jenishev\Laravel\Contacts;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * Service provider for Laravel Contacts package.
 *
 * Registers package configuration, migrations, and install command.
 */
class ContactsServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the package settings.
     *
     * @param  Package  $package  The package instance to configure
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-contacts')
            ->hasConfigFile()
            ->hasMigration('create_contacts_table');
    }

    public function packageBooted(): void
    {
        if ($this->app->runningInConsole()) {
            foreach (['config', 'migrations'] as $key) {
                $group = "contacts-$key";

                $this->publishes(
                    static::pathsToPublish(self::class, $group),
                    $key
                );

                unset(static::$publishGroups[$group]);
            }
        }
    }
}
