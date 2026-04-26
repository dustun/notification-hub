<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media_assets', function (Blueprint $table): void {
            $table->uuid('id')
                ->primary();

            $table->timestamps();
            $table->string('name');
            $table->unsignedTinyInteger('media_type')
                ->index();
            $table->string('collection_name')
                ->default('original')
                ->index();
            $table->string('disk')
                ->default('public');
            $table->string('original_file_name')
                ->nullable();
            $table->string('mime_type')
                ->nullable()
                ->index();
            $table->string('extension')
                ->nullable()
                ->index();
            $table->unsignedBigInteger('size')
                ->default(0);
            $table->foreignUuid('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('description')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_assets');
    }
};
