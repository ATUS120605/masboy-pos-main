<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_bahan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('satuan')->default('porsi');
            $table->integer('stok')->default(0);
            $table->timestamps();
        });

        Schema::create('komposisi_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('bahan_id')->constrained('stok_bahan')->cascadeOnDelete();
            $table->integer('qty')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komposisi_menu');
        Schema::dropIfExists('stok_bahan');
    }
};
