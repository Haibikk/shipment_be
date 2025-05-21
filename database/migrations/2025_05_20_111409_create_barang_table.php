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
        Schema::create('barang', function (Blueprint $table) {
            $table->id('id_barang');
            $table->unsignedBigInteger('id_kategori');
            $table->string('nama_barang');
            $table->text('deskripsi')->nullable();
            $table->float('berat');
            $table->string('dimensi');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('id_kategori')->references('id_kategori')->on('kategori_barang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
}; 