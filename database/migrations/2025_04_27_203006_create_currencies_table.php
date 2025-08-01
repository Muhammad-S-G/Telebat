<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); 
            $table->string('name', 64); 
            $table->string('symbol', 8)->nullable(); 
            $table->unsignedTinyInteger('precision') 
                ->default(2);
            $table->boolean('active')
                ->default(true);
            $table->timestamps();
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
