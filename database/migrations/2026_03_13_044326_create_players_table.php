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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pemain');
            $table->string('no_anggota')->nullable(); // Opsional untuk administrasi
            $table->date('tanggal_lahir');
            $table->string('tempat_lahir')->nullable();
            $table->enum('posisi', ['goalkeeper', 'defensif', 'midfielder', 'forward']);
            $table->string('foto')->nullable();
            $table->integer('nomor_punggung')->unique()->nullable();
            $table->enum('kaki_dominan',['kiri', 'kanan', 'kiri-kanan'])->default('kanan');
            $table->integer('tinggi_badan');
            $table->integer('berat_badan');
            $table->enum('status_pembayaran', ['lunas','belum_lunas', 'tunggakan'])->default('lunas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
