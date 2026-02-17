<?php

namespace Jenishev\Laravel\Contacts;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ContactsServiceProvider extends PackageServiceProvider
{
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
