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
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
        });

        Schema::create('permissions', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
        });

        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('name')->unique();
            $table->string('periodicity');
            $table->string('description')->nullable();
            $table->boolean('done')->default(false);
            $table->string('status')->nullable();
            $table->string('attach')->nullable();
            $table->timestamp('due')->nullable();
            $table->string('time')->nullable();
            $table->string('portal')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->unique(['permission_id', 'role_id']);
        });

        Schema::create('permission_task', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->unique(['permission_id', 'task_id']);
        });

        Schema::create('user_role_permissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unique(['user_id', 'role_id', 'permission_id', 'tenant_id']);
            $table->timestamps();
        });

        Schema::create('task_user', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['task_id', 'user_id']);
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['user_id', 'role_id']);
            $table->timestamps();
        });

        Schema::create('client_role', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->unique(['client_id', 'role_id']);
            $table->timestamps();
        });

        Schema::create('client_user', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['client_id', 'user_id']);
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
        Schema::dropIfExists('client_user');
        Schema::dropIfExists('client_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('task_user');
        Schema::dropIfExists('permission_task');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('user_role_permissions');
    }
};
