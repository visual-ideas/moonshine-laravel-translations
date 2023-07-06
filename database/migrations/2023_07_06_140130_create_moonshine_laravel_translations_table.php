<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('moonshine_laravel_translations', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->unsignedSmallInteger('list_order')->default(65535);
            $table->text('key');
            $table->string('locale');
            $table->text('value')->nullable();
            $table->boolean('is_changed')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moonshine_laravel_translations');
    }
};
