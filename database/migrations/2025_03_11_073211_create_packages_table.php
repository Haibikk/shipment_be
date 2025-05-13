<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->foreignId('sender_id')->constrained('users');
            $table->foreignId('receiver_id')->constrained('users');
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->decimal('weight', 8, 2); 
            $table->decimal('length', 8, 2)->nullable(); 
            $table->decimal('width', 8, 2)->nullable(); 
            $table->decimal('height', 8, 2)->nullable(); 
            $table->decimal('value', 12, 2)->nullable(); 
            $table->string('origin_address');
            $table->string('destination_address');
            $table->string('status')->default('pending'); 
            $table->dateTime('picked_up_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
