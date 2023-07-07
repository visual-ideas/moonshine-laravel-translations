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
        Schema::table('moonshine_laravel_translations', function (Blueprint $table) {
            $table->dropColumn('list_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moonshine_laravel_translations', function (Blueprint $table) {
            $table->unsignedSmallInteger('list_order')->default(65535)->after('group');
        });
    }
};
