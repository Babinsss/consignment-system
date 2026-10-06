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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consignor_id')->constrained()->cascadeOnDelete();
            $table->string('description'); // e.g., 'SURGICAL BLADE NO. 15'
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();

            // Prevents adding the exact same item twice for the same supplier
            $table->unique(['consignor_id', 'description']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
