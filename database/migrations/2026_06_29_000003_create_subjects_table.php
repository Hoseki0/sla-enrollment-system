<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code', 20);
            $table->string('subject_name');
            $table->string('grade_level');
            $table->string('strand')->nullable();
            $table->unsignedTinyInteger('semester')->nullable();
            $table->decimal('units', 3, 1)->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['subject_code', 'grade_level', 'strand', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
