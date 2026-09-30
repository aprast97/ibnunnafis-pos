<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('transaction_number', 50)->unique();
            $table->dateTime('transaction_date');
            $table->decimal('total_amount', 14, 2);
            $table->decimal('payment_amount', 14, 2);
            $table->decimal('change_amount', 14, 2);
            $table->string('status', 30)->default('COMPLETED');
            $table->dateTime('canceled_at')->nullable();
            $table->foreignId('canceled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
