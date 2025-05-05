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

            $table->timestamps();
        });

        Schema::create('client_task', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->timestamps();
            // Garante que uma tarefa não seja associada ao mesmo cliente múltiplas vezes
            $table->unique(['client_id', 'task_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_task');
        Schema::dropIfExists('clients');
    }
};
