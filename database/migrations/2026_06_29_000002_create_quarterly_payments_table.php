<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quarterly_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_ref')->unique();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_schedule_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('quarter');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['gcash', 'maya'])->nullable();
            $table->enum('status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->string('gateway_ref')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['enrollment_id', 'quarter']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quarterly_payments');
    }
};
