<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('system_logs', function (Blueprint $table): void {
            $table->uuid('id')
                ->primary();

            $table->timestamps();
            $table->string('level')
                ->index();
            $table->string('category')
                ->index();
            $table->string('action')
                ->nullable()
                ->index();
            $table->text('message');
            $table->json('context')
                ->nullable();
            $table->string('route_name')
                ->nullable()
                ->index();
            $table->string('method')
                ->nullable()
                ->index();
            $table->string('path')
                ->nullable()
                ->index();
            $table->unsignedSmallInteger('status_code')
                ->nullable()
                ->index();
            $table->unsignedInteger('duration_ms')
                ->nullable();
            $table->string('ip_address', 45)
                ->nullable();
            $table->text('user_agent')
                ->nullable();
            $table->foreignUuid('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
