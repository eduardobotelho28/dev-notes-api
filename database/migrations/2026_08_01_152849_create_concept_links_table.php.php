<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('concept_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concept_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('url');
            $table->string('type')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concept_links');
    }
};