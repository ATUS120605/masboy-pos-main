<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->enum('tipe', ['makanan', 'paket'])->default('makanan')->after('kategori_id');
        });

        Schema::create('detail_pakets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->integer('qty')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pakets');
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
