<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->nullable(); // e.g. apartment, house, land
            $table->string('currency')->nullable()->default('PKR');
            $table->decimal('price', 15, 2)->nullable();
            $table->string('status')->default('draft'); // draft, published, archived
            $table->string('location')->nullable();
            $table->json('details')->nullable(); // bedrooms, area, amenities etc.
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
