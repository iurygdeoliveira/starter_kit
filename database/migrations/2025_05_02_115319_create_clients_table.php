<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();
            $table->uuid('uuid')->unique();

            $table->string('name')->unique();
            $table->string('cnpj')->unique();
            $table->string('activity');
            $table->string('regime');
            $table->string('user');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();

            $table->timestamp('email_verified_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
