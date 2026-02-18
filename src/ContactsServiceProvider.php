<?php

namespace Jenishev\Laravel\Contacts;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
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
            ->hasMigration('create_contacts_table')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->publishConfigFile();
                $command->publishMigrations();
            });
    }
}
