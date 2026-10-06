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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['DELIVERY', 'CONSUMED', 'RETURNED']);
            $table->integer('quantity'); // Positive for delivery, negative for consumed
            $table->string('reference_number')->nullable(); // DR or Charge Slip
            $table->enum('payment_type', ['CASH', 'CHARGED'])->nullable(); 
            $table->dateTime('transaction_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
