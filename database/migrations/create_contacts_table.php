<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Jenishev\Laravel\Contacts\Enums\ContactTypeEnum;

return new class extends Migration
{
    public function up(): void
    {
        $config = config('contacts');

        Schema::create($config['table'], function (Blueprint $table) use ($config) {
            $table->id();
            $table->morphs('model');
            $table->enum('type', ContactTypeEnum::values())->default(ContactTypeEnum::Phone);
            $table->string('value');
            $table->string('country_code', 2)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $usersTable = (new $config['user_model'])->getTable();

            foreach (['created_by', 'updated_by'] as $column) {
                $table->foreignId($column)
                    ->nullable()
                    ->constrained($usersTable);
            }

            $table->unique(['model_type', 'model_id', 'type', 'value'], 'unique_value_per_model_type');
        });

        $driver = DB::getDriverName();

        if ($driver === 'pgsql' || $driver === 'sqlite') {
            DB::statement("
                CREATE UNIQUE INDEX unique_primary_per_model_type
                ON {$config['table']} (model_type, model_id, type, is_primary)
                WHERE is_primary = true;
            ");
        }
    }

    public function down(): void
    {
        Schema::drop(config('contacts.table'));
    }
};
