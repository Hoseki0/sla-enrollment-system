<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_name');
            $table->string('grade_level');
            $table->string('track')->nullable();
            $table->string('strand')->nullable();
            $table->unsignedInteger('max_capacity')->default(40);
            $table->foreignId('school_year_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
