<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->index('status');
            $table->index('user_id');
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->unique(['property_id', 'agency_id']);
        });

        Schema::table('agencies', function (Blueprint $table) {
            $table->index('auto_link_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->dropUnique(['property_id', 'agency_id']);
        });

        Schema::table('agencies', function (Blueprint $table) {
            $table->dropIndex(['auto_link_enabled']);
        });
    }
};
